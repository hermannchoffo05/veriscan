<?php

namespace Tests\Feature;

use App\Models\Fabricant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanGatingTest extends TestCase
{
    use RefreshDatabase;

    private function fabricantAvecPlan(string $plan, ?\Carbon\Carbon $expireLe = null): Fabricant
    {
        return Fabricant::factory()->create([
            'plan' => $plan,
            'plan_expire_le' => $expireLe,
        ]);
    }

    public function test_bloque_carte_risques_plan_gratuit(): void
    {
        $fabricant = $this->fabricantAvecPlan('gratuit');

        $this->actingAs($fabricant, 'fabricant')
            ->get(route('fabricant.carte.index'))
                    ->assertOk()
        ->assertViewIs('fabricant.plan-locked');
    }

    public function test_bloque_carte_risques_plan_starter(): void
    {
        $fabricant = $this->fabricantAvecPlan('starter');

        $this->actingAs($fabricant, 'fabricant')
            ->get(route('fabricant.carte.index'))
                    ->assertOk()
        ->assertViewIs('fabricant.plan-locked');
    }

    public function test_autorise_carte_risques_plan_pro(): void
    {
        $fabricant = $this->fabricantAvecPlan('pro');

        $this->actingAs($fabricant, 'fabricant')
            ->get(route('fabricant.carte.index'))
            ->assertOk();
    }

    public function test_autorise_carte_risques_plan_entreprise(): void
    {
        $fabricant = $this->fabricantAvecPlan('entreprise');

        $this->actingAs($fabricant, 'fabricant')
            ->get(route('fabricant.carte.index'))
            ->assertOk();
    }

    public function test_chatbot_403_json_plan_gratuit(): void
    {
        $fabricant = $this->fabricantAvecPlan('gratuit');

        $this->actingAs($fabricant, 'fabricant')
            ->postJson(route('fabricant.chatbot.ask'), ['message' => 'test'])
            ->assertStatus(403)
            ->assertJsonStructure(['error']);
    }

    public function test_chatbot_passe_plan_pro(): void
    {
        $fabricant = $this->fabricantAvecPlan('pro');

        $response = $this->actingAs($fabricant, 'fabricant')
            ->postJson(route('fabricant.chatbot.ask'), ['message' => 'test']);

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_plan_pro_expire_retombe_gratuit_et_bloque_carte(): void
    {
        $fabricant = $this->fabricantAvecPlan('pro', now()->subDay());

        $this->assertEquals('gratuit', $fabricant->planActif());

        $this->actingAs($fabricant, 'fabricant')
            ->get(route('fabricant.carte.index'))
                    ->assertOk()
        ->assertViewIs('fabricant.plan-locked');
    }

    public function test_redirige_sans_json_quand_pas_de_json_attendu(): void
    {
        $fabricant = $this->fabricantAvecPlan('gratuit');

        $this->actingAs($fabricant, 'fabricant')
            ->post(route('fabricant.dashboard.chat'), ['message' => 'test'])
                    ->assertOk()
        ->assertViewIs('fabricant.plan-locked');
    }
}