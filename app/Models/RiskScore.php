<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RiskScore extends Model
{
    protected $fillable = [
        'produit_id',
        'zone_code',
        'score',
        'freq_signalements',
        'densite_geo',
        'vetuste_lot',
        'taux_scan_negatif',
        'poids_categorie',
        'score_fabricant',
        'niveau',
        'computed_at',
    ];
    protected $casts = [
        'computed_at' => 'datetime',
        'score'       => 'float',
    ];
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
    /**
     * Retourne la couleur CSS associée au niveau de risque
     */
    public function getCouleurAttribute(): string
    {
        return match($this->niveau) {
            'critique' => '#CE1126',
            'eleve'    => '#f97316',
            'modere'   => '#FCD116',
            'faible'   => '#007A4D',
            default    => '#6b7280',
        };
    }
}