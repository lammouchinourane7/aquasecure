<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Intervention;
use App\Models\ReseauEau;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Binôme 2 — Gestion des incidents : CRUD Incident + Intervention.
 */
class IncidentAndInterventionTest extends TestCase
{
    use RefreshDatabase;

    private User $gestionnaire;

    private ReseauEau $reseau;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $zone = Zone::create(['nom' => 'Centre-Ville', 'commune' => 'Tunis']);
        $this->reseau = ReseauEau::create(['zone_id' => $zone->id, 'type' => 'Canalisation principale', 'etat' => 'bon']);
    }

    public function test_les_pages_du_back_office_incidents_et_interventions_s_affichent(): void
    {
        $incident = Incident::create([
            'reseau_id' => $this->reseau->id,
            'type' => 'fuite',
            'gravite' => 'moyenne',
            'date_signalement' => now()->toDateString(),
        ]);

        $intervention = Intervention::create([
            'incident_id' => $incident->id,
            'statut' => 'planifiee',
            'date_intervention' => now()->toDateString(),
        ]);

        $this->actingAs($this->gestionnaire);

        $this->get(route('admin.incidents.index'))->assertOk();
        $this->get(route('admin.incidents.create'))->assertOk();
        $this->get(route('admin.incidents.show', $incident))->assertOk();
        $this->get(route('admin.incidents.edit', $incident))->assertOk();

        $this->get(route('admin.interventions.index'))->assertOk();
        $this->get(route('admin.interventions.create'))->assertOk();
        $this->get(route('admin.interventions.show', $intervention))->assertOk();
        $this->get(route('admin.interventions.edit', $intervention))->assertOk();
    }

    public function test_peut_creer_modifier_et_supprimer_un_incident(): void
    {
        $this->actingAs($this->gestionnaire);

        // Store
        $response = $this->post(route('admin.incidents.store'), [
            'reseau_id' => $this->reseau->id,
            'type' => 'contamination',
            'gravite' => 'critique',
            'date_signalement' => now()->toDateString(),
        ]);

        $incident = Incident::latest()->first();
        $this->assertNotNull($incident);
        $response->assertRedirect(route('admin.incidents.show', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'type' => 'contamination',
            'gravite' => 'critique',
        ]);

        // Update
        $this->put(route('admin.incidents.update', $incident), [
            'reseau_id' => $this->reseau->id,
            'type' => 'fuite',
            'gravite' => 'faible',
            'date_signalement' => now()->toDateString(),
        ])->assertRedirect(route('admin.incidents.show', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'type' => 'fuite',
            'gravite' => 'faible',
        ]);

        // Destroy
        $this->delete(route('admin.incidents.destroy', $incident))
            ->assertRedirect(route('admin.incidents.index'));

        $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    }

    public function test_peut_creer_modifier_et_supprimer_une_intervention(): void
    {
        $this->actingAs($this->gestionnaire);

        $incident = Incident::create([
            'reseau_id' => $this->reseau->id,
            'type' => 'coupure',
            'gravite' => 'moyenne',
            'date_signalement' => now()->toDateString(),
        ]);

        // Store
        $response = $this->post(route('admin.interventions.store'), [
            'incident_id' => $incident->id,
            'statut' => 'en_cours',
            'date_intervention' => now()->toDateString(),
        ]);

        $intervention = Intervention::latest()->first();
        $this->assertNotNull($intervention);
        $response->assertRedirect(route('admin.interventions.show', $intervention));

        $this->assertDatabaseHas('interventions', [
            'id' => $intervention->id,
            'statut' => 'en_cours',
        ]);

        // Update
        $this->put(route('admin.interventions.update', $intervention), [
            'incident_id' => $incident->id,
            'statut' => 'terminee',
            'date_intervention' => now()->toDateString(),
        ])->assertRedirect(route('admin.interventions.show', $intervention));

        $this->assertDatabaseHas('interventions', [
            'id' => $intervention->id,
            'statut' => 'terminee',
        ]);

        // Destroy
        $this->delete(route('admin.interventions.destroy', $intervention))
            ->assertRedirect(route('admin.interventions.index'));

        $this->assertDatabaseMissing('interventions', ['id' => $intervention->id]);
    }
}
