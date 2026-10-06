<?php

namespace Tests\Feature;

use App\Models\Capteur;
use App\Models\Releve;
use App\Models\ReseauEau;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Binôme 4 — Surveillance / Capteurs : CRUD Capteur + Releve.
 */
class SurveillanceTest extends TestCase
{
    use RefreshDatabase;

    private User $gestionnaire;

    private ReseauEau $reseau;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $zone = Zone::create(['nom' => 'Centre', 'commune' => 'Tunis']);
        $this->reseau = ReseauEau::create(['zone_id' => $zone->id, 'type' => 'Réservoir test', 'etat' => 'bon']);
    }

    private function capteur(array $attributs = []): Capteur
    {
        return Capteur::factory()->for($this->reseau, 'reseauEau')->create(array_merge([
            'type_mesure' => 'pression', 'unite' => 'bar', 'seuil_min' => 2, 'seuil_max' => 6, 'statut' => 'actif',
            'date_installation' => now()->subYear()->toDateString(),
        ], $attributs));
    }

    public function test_les_pages_du_back_office_s_affichent(): void
    {
        $capteur = $this->capteur();
        $releve = Releve::factory()->for($capteur)->create();

        $this->actingAs($this->gestionnaire);
        $this->get(route('admin.capteurs.index'))->assertOk()->assertSee($capteur->code);
        $this->get(route('admin.capteurs.create'))->assertOk();
        $this->get(route('admin.capteurs.show', $capteur))->assertOk();
        $this->get(route('admin.capteurs.edit', $capteur))->assertOk()->assertSee($capteur->code);
        $this->get(route('admin.releves.index'))->assertOk();
        $this->get(route('admin.releves.create', ['capteur_id' => $capteur->id]))->assertOk();
        $this->get(route('admin.releves.show', $releve))->assertOk();
        $this->get(route('admin.releves.edit', $releve))->assertOk();
    }

    public function test_un_citoyen_ne_peut_pas_acceder_au_back_office(): void
    {
        $citoyen = User::factory()->create(['role' => 'citoyen']);

        $this->actingAs($citoyen)->get(route('admin.capteurs.index'))->assertForbidden();
        $this->actingAs($citoyen)->get(route('surveillance.index'))->assertOk();
    }

    public function test_creation_d_un_capteur_valide(): void
    {
        $this->actingAs($this->gestionnaire)->post(route('admin.capteurs.store'), [
            'reseau_id' => $this->reseau->id,
            'code' => 'cap-pre-100',
            'modele' => 'Siemens SITRANS P',
            'type_mesure' => 'pression',
            'unite' => 'bar',
            'seuil_min' => 2,
            'seuil_max' => 6,
            'date_installation' => '2025-01-15',
            'statut' => 'actif',
        ])->assertRedirect();

        $this->assertDatabaseHas('capteurs', ['code' => 'CAP-PRE-100', 'reseau_id' => $this->reseau->id]);
    }

    public function test_validation_du_capteur(): void
    {
        $this->capteur(['code' => 'CAP-EXISTE']);

        $this->actingAs($this->gestionnaire)
            ->from(route('admin.capteurs.create'))
            ->post(route('admin.capteurs.store'), [
                'reseau_id' => 999,
                'code' => 'CAP-EXISTE',
                'modele' => '',
                'type_mesure' => 'pression',
                'unite' => 'm3/h',
                'seuil_min' => 10,
                'seuil_max' => 5,
                'date_installation' => now()->addDay()->toDateString(),
                'statut' => 'inconnu',
            ])
            ->assertRedirect(route('admin.capteurs.create'))
            ->assertSessionHasErrors(['reseau_id', 'code', 'modele', 'unite', 'seuil_max', 'date_installation', 'statut']);
    }

    public function test_un_releve_hors_seuil_est_detecte_automatiquement(): void
    {
        $capteur = $this->capteur();

        $this->actingAs($this->gestionnaire)->post(route('admin.releves.store'), [
            'capteur_id' => $capteur->id,
            'valeur' => 8.5,
            'date_releve' => now()->subHour()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('admin.capteurs.show', $capteur));

        $this->assertDatabaseHas('releves', ['capteur_id' => $capteur->id, 'valeur' => 8.5, 'hors_seuil' => true]);
    }

    public function test_regles_metier_du_releve(): void
    {
        $capteur = $this->capteur(['statut' => 'hors_service', 'date_installation' => now()->subDays(3)->toDateString()]);

        $this->actingAs($this->gestionnaire)->post(route('admin.releves.store'), [
            'capteur_id' => $capteur->id,
            'valeur' => 4,
            'date_releve' => now()->subDays(10)->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors(['capteur_id', 'date_releve']);

        $this->assertDatabaseCount('releves', 0);
    }

    public function test_modifier_les_seuils_recalcule_les_releves(): void
    {
        $capteur = $this->capteur();
        $releve = Releve::factory()->for($capteur)->create(['valeur' => 5]);
        $releve->refresh();
        $this->assertFalse($releve->hors_seuil);

        $this->actingAs($this->gestionnaire)->put(route('admin.capteurs.update', $capteur), [
            'reseau_id' => $this->reseau->id,
            'code' => $capteur->code,
            'modele' => $capteur->modele,
            'type_mesure' => 'pression',
            'unite' => 'bar',
            'seuil_min' => 2,
            'seuil_max' => 4,
            'date_installation' => $capteur->date_installation->toDateString(),
            'statut' => 'actif',
        ])->assertSessionHasNoErrors();

        $this->assertTrue($releve->fresh()->hors_seuil);
    }

    public function test_supprimer_un_capteur_supprime_ses_releves(): void
    {
        $capteur = $this->capteur();
        Releve::factory()->count(3)->for($capteur)->create();

        $this->actingAs($this->gestionnaire)->delete(route('admin.capteurs.destroy', $capteur))
            ->assertRedirect(route('admin.capteurs.index'));

        $this->assertDatabaseMissing('capteurs', ['id' => $capteur->id]);
        $this->assertDatabaseCount('releves', 0);
    }

    public function test_modifier_et_supprimer_un_releve(): void
    {
        $capteur = $this->capteur();
        $releve = Releve::factory()->for($capteur)->create(['valeur' => 3]);

        $this->actingAs($this->gestionnaire)->put(route('admin.releves.update', $releve), [
            'capteur_id' => $capteur->id,
            'valeur' => 1.2,
            'date_releve' => now()->subHour()->format('Y-m-d\TH:i'),
            'remarque' => 'Chute de pression',
        ])->assertRedirect(route('admin.releves.show', $releve));

        $this->assertTrue($releve->fresh()->hors_seuil);

        $this->actingAs($this->gestionnaire)->delete(route('admin.releves.destroy', $releve))
            ->assertRedirect(route('admin.releves.index'));
        $this->assertDatabaseMissing('releves', ['id' => $releve->id]);
    }
}
