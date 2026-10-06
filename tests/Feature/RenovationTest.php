<?php

namespace Tests\Feature;

use App\Models\Financement;
use App\Models\ProjetRenovation;
use App\Models\ReseauEau;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Binôme 3 — Financement des rénovations : CRUD ProjetRenovation + Financement.
 */
class RenovationTest extends TestCase
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
        $this->reseau = ReseauEau::create(['zone_id' => $zone->id, 'type' => 'Canalisation principale', 'etat' => 'degrade']);
    }

    private function projet(array $attributs = []): ProjetRenovation
    {
        return ProjetRenovation::create(array_merge([
            'reseau_id' => $this->reseau->id,
            'titre' => 'Rénovation canalisation test',
            'statut' => 'en_cours',
        ], $attributs));
    }

    private function financement(ProjetRenovation $projet, array $attributs = []): Financement
    {
        return Financement::create(array_merge([
            'projet_id' => $projet->id,
            'source' => 'Etat',
            'montant' => 50000.00,
        ], $attributs));
    }

    public function test_les_pages_du_back_office_s_affichent(): void
    {
        $projet = $this->projet();
        $financement = $this->financement($projet);

        $this->actingAs($this->gestionnaire);
        $this->get(route('admin.projets.index'))->assertOk()->assertSee($projet->titre);
        $this->get(route('admin.projets.create'))->assertOk();
        $this->get(route('admin.projets.show', $projet))->assertOk()->assertSee($projet->titre);
        $this->get(route('admin.projets.edit', $projet))->assertOk()->assertSee($projet->titre);

        $this->get(route('admin.financements.index'))->assertOk()->assertSee('50 000,00');
        $this->get(route('admin.financements.create', ['projet_id' => $projet->id]))->assertOk();
        $this->get(route('admin.financements.show', $financement))->assertOk()->assertSee('50 000,00');
        $this->get(route('admin.financements.edit', $financement))->assertOk();
    }

    public function test_un_citoyen_ne_peut_pas_acceder_au_back_office_des_projets_et_financements(): void
    {
        $citoyen = User::factory()->create(['role' => 'citoyen']);
        $this->actingAs($citoyen);

        $this->get(route('admin.projets.index'))->assertForbidden();
        $this->get(route('admin.financements.index'))->assertForbidden();
    }

    public function test_un_gestionnaire_peut_creer_un_projet_renovation(): void
    {
        $this->actingAs($this->gestionnaire);

        $response = $this->post(route('admin.projets.store'), [
            'reseau_id' => $this->reseau->id,
            'titre' => 'Modernisation des vannes de régulation',
            'statut' => 'planifie',
        ]);

        $projet = ProjetRenovation::where('titre', 'Modernisation des vannes de régulation')->first();
        $this->assertNotNull($projet);
        $this->assertSame('planifie', $projet->statut);
        $response->assertRedirect(route('admin.projets.show', $projet));
    }

    public function test_la_creation_d_un_projet_echoue_si_donnees_invalides(): void
    {
        $this->actingAs($this->gestionnaire);

        $response = $this->post(route('admin.projets.store'), [
            'reseau_id' => 999999,
            'titre' => '',
            'statut' => 'statut_inconnu',
        ]);

        $response->assertSessionHasErrors(['reseau_id', 'titre', 'statut']);
    }

    public function test_un_gestionnaire_peut_modifier_un_projet_renovation(): void
    {
        $projet = $this->projet(['statut' => 'planifie']);
        $this->actingAs($this->gestionnaire);

        $response = $this->put(route('admin.projets.update', $projet), [
            'reseau_id' => $this->reseau->id,
            'titre' => 'Titre modifié avec succès',
            'statut' => 'termine',
        ]);

        $response->assertRedirect(route('admin.projets.show', $projet));
        $this->assertSame('Titre modifié avec succès', $projet->fresh()->titre);
        $this->assertSame('termine', $projet->fresh()->statut);
    }

    public function test_un_gestionnaire_peut_supprimer_un_projet_renovation(): void
    {
        $projet = $this->projet();
        $this->financement($projet);
        $this->actingAs($this->gestionnaire);

        $response = $this->delete(route('admin.projets.destroy', $projet));
        $response->assertRedirect(route('admin.projets.index'));

        $this->assertDatabaseMissing('projet_renovations', ['id' => $projet->id]);
        $this->assertDatabaseMissing('financements', ['projet_id' => $projet->id]);
    }

    public function test_un_gestionnaire_peut_creer_un_financement(): void
    {
        $projet = $this->projet();
        $this->actingAs($this->gestionnaire);

        $response = $this->post(route('admin.financements.store'), [
            'projet_id' => $projet->id,
            'source' => 'municipalite',
            'montant' => 35000.50,
        ]);

        $response->assertRedirect(route('admin.financements.index'));
        $this->assertDatabaseHas('financements', [
            'projet_id' => $projet->id,
            'source' => 'municipalite',
            'montant' => 35000.50,
        ]);
    }

    public function test_la_creation_d_un_financement_echoue_si_donnees_invalides(): void
    {
        $this->actingAs($this->gestionnaire);

        $response = $this->post(route('admin.financements.store'), [
            'projet_id' => 999999,
            'source' => 'source_invalide',
            'montant' => -100,
        ]);

        $response->assertSessionHasErrors(['projet_id', 'source', 'montant']);
    }

    public function test_un_gestionnaire_peut_modifier_un_financement(): void
    {
        $projet = $this->projet();
        $financement = $this->financement($projet, ['montant' => 10000.00]);
        $this->actingAs($this->gestionnaire);

        $response = $this->put(route('admin.financements.update', $financement), [
            'projet_id' => $projet->id,
            'source' => 'bailleur',
            'montant' => 75000.00,
        ]);

        $response->assertRedirect(route('admin.financements.index'));
        $this->assertSame('bailleur', $financement->fresh()->source);
        $this->assertEquals(75000.00, (float) $financement->fresh()->montant);
    }

    public function test_un_gestionnaire_peut_supprimer_un_financement(): void
    {
        $projet = $this->projet();
        $financement = $this->financement($projet);
        $this->actingAs($this->gestionnaire);

        $response = $this->from(route('admin.financements.index'))
            ->delete(route('admin.financements.destroy', $financement));

        $response->assertRedirect(route('admin.financements.index'));
        $this->assertDatabaseMissing('financements', ['id' => $financement->id]);
    }

    public function test_un_citoyen_peut_consulter_les_projets_en_front(): void
    {
        $citoyen = User::factory()->create(['role' => 'citoyen']);
        $projet = $this->projet(['titre' => 'Chantier public visible']);
        $this->financement($projet, ['montant' => 20000.00]);

        $this->actingAs($citoyen);
        $response = $this->get(route('projets.index'));

        $response->assertOk();
        $response->assertSee('Chantier public visible');
        $response->assertSee('20 000,00 TND');
    }
}
