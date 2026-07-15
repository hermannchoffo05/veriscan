<?php

namespace Database\Factories;

use App\Models\Fabricant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Fabricant>
 */
class FabricantFactory extends Factory
{
    protected $model = Fabricant::class;

    public function definition(): array
    {
        return [
            'nom_entreprise'        => $this->faker->company(),
            'email'                 => $this->faker->unique()->safeEmail(),
            'password'              => Hash::make('password'),
            'telephone'             => $this->faker->phoneNumber(),
            'adresse'               => $this->faker->address(),
            'pays'                  => 'Cameroun',
            'statut'                => 'actif',
            'plan'                  => 'gratuit',
            'plan_expire_le'        => null,
            'essai_deja_utilise'    => false,
            'qrcodes_generes_mois'  => 0,
            'rapports_generes_mois' => 0,
            'ia_requetes_mois'      => 0,
            'usage_mois_reference'  => now()->format('Y-m'),
            'remember_token'        => Str::random(10),
        ];
    }
}