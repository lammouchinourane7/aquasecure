<?php

namespace Tests\Feature;

use App\Models\AnalyseQualite;
use App\Models\PointPrelevement;
use App\Models\ReseauEau;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Binôme 5 — Qualité de l'eau : CRUD PointPrelevement + AnalyseQualite.
 */
class QualiteEauTest extends TestCase
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

    private function point(array $attributs = []): PointPrelevement
    {
        return PointPrelevement::factory()->for($this->reseau, 'reseauEau')->create(array_merge(['actif' => true], $attributs));
    }

    private function valeursConformes(array $surcharge = []): array
    {
        return array_merge([
            'date_prelevement' => now()->subDay()->toDateString(),
            'ph' => 7.4,
            'chlore_residuel' => 0.5,
            'turbidite' => 1.2,
            'nitrates' => 18,
            'bacteries_coliformes' => 0,
            'laboratoire' => 'Institut Pasteur de Tunis',
        ], $surcharge);
    }

    public function test_les_pages_du_back_office_s_affichent(): void
    {
        $point = $this->point();
        $analyse = AnalyseQualite::factory()->for($point, 'point')->create();

        $this->actingAs($this->gestionnaire);
        $this->get(route('admin.points.index'))->assertOk()->assertSee($point->code);
        $this->get(route('admin.points.create'))->assertOk();
        $this->get(route('admin.points.show', $point))->assertOk();
        $this->get(route('admin.points.edit', $point))->assertOk()->assertSee($point->code);
        $this->get(route('admin.analyses.index'))->assertOk();
        $this->get(route('admin.analyses.create', ['point_id' => $point->id]))->assertOk();
        $this->get(route('admin.analyses.show', $analyse))->assertOk();
        $this->get(route('admin.analyses.edit', $analyse))->assertOk();
    }

    public function test_page_citoyenne_et_acces_back_office_interdit(): void
    {
        $citoyen = User::factory()->create(['role' => 'citoyen']);
        $point = $this->point();
        AnalyseQualite::factory()->for($point, 'point')->create();

        $this->actingAs($citoyen)->get(route('qualite.index'))->assertOk()->assertSee($point->nom);
        $this->actingAs($citoyen)->get(route('admin.points.index'))->assertForbidden();
    }

    public function test_creation_d_un_point_valide(): void
    {
        $this->actingAs($this->gestionnaire)->post(route('admin.points.store'), [
            'reseau_id' => $this->reseau->id,
            'code' => 'pp-tun-100',
            'nom' => 'Fontaine de la Place',
            'adresse' => 'Avenue Habib Bourguiba, Tunis',
            'latitude' => 36.8,
            'longitude' => 10.18,
            'type_point' => 'robinet_public',
            'actif' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('point_prelevements', ['code' => 'PP-TUN-100', 'actif' => true]);
    }

    public function test_validation_du_point(): void
    {
        $this->point(['code' => 'PP-EXISTE']);

        $this->actingAs($this->gestionnaire)
            ->from(route('admin.points.create'))
            ->post(route('admin.points.store'), [
                'reseau_id' => 999,
                'code' => 'PP-EXISTE',
                'nom' => 'A',
                'adresse' => '',
                'latitude' => 120,
                'type_point' => 'piscine',
            ])
            ->assertRedirect(route('admin.points.create'))
            ->assertSessionHasErrors(['reseau_id', 'code', 'nom', 'adresse', 'latitude', 'longitude', 'type_point']);
    }

    public function test_la_conformite_est_calculee_automatiquement(): void
    {
        $point = $this->point();

        $this->actingAs($this->gestionnaire)
            ->post(route('admin.analyses.store'), $this->valeursConformes(['point_id' => $point->id]))
            ->assertSessionHasNoErrors();
        $this->assertTrue(AnalyseQualite::first()->conforme);

        $this->actingAs($this->gestionnaire)
            ->post(route('admin.analyses.store'), $this->valeursConformes([
                'point_id' => $point->id,
                'date_prelevement' => now()->subDays(2)->toDateString(),
                'bacteries_coliformes' => 12,
            ]))
            ->assertSessionHasNoErrors();

        $analyse = AnalyseQualite::where('bacteries_coliformes', 12)->first();
        $this->assertFalse($analyse->conforme);
        $this->assertSame(['Coliformes'], $analyse->parametresNonConformes());
    }

    public function test_validation_et_regles_metier_de_l_analyse(): void
    {
        $point = $this->point();
        AnalyseQualite::factory()->for($point, 'point')->create(['date_prelevement' => now()->subDay()->toDateString()]);

        // Valeurs impossibles + doublon de date pour le même point
        $this->actingAs($this->gestionnaire)
            ->post(route('admin.analyses.store'), $this->valeursConformes([
                'point_id' => $point->id,
                'ph' => 15,
                'bacteries_coliformes' => 2.5,
                'laboratoire' => '',
            ]))
            ->assertSessionHasErrors(['ph', 'bacteries_coliformes', 'laboratoire', 'date_prelevement']);

        // Point désactivé et date dans le futur
        $inactif = $this->point(['actif' => false]);
        $this->actingAs($this->gestionnaire)
            ->post(route('admin.analyses.store'), $this->valeursConformes([
                'point_id' => $inactif->id,
                'date_prelevement' => now()->addDay()->toDateString(),
            ]))
            ->assertSessionHasErrors(['point_id', 'date_prelevement']);

        $this->assertDatabaseCount('analyse_qualites', 1);
    }

    public function test_modifier_et_supprimer_une_analyse(): void
    {
        $point = $this->point();
        $analyse = AnalyseQualite::factory()->for($point, 'point')->create(['date_prelevement' => now()->subDays(5)->toDateString()]);

        $this->actingAs($this->gestionnaire)
            ->put(route('admin.analyses.update', $analyse), $this->valeursConformes([
                'point_id' => $point->id,
                'date_prelevement' => now()->subDays(5)->toDateString(),
                'nitrates' => 75,
            ]))
            ->assertRedirect(route('admin.analyses.show', $analyse));

        $this->assertFalse($analyse->fresh()->conforme);

        $this->actingAs($this->gestionnaire)->delete(route('admin.analyses.destroy', $analyse))
            ->assertRedirect(route('admin.points.show', $point));
        $this->assertDatabaseMissing('analyse_qualites', ['id' => $analyse->id]);
    }

    public function test_supprimer_un_point_supprime_ses_analyses(): void
    {
        $point = $this->point();
        AnalyseQualite::factory()->count(3)->for($point, 'point')
            ->sequence(fn ($s) => ['date_prelevement' => now()->subMonths($s->index)->toDateString()])
            ->create();

        $this->actingAs($this->gestionnaire)->delete(route('admin.points.destroy', $point))
            ->assertRedirect(route('admin.points.index'));

        $this->assertDatabaseMissing('point_prelevements', ['id' => $point->id]);
        $this->assertDatabaseCount('analyse_qualites', 0);
    }
}
