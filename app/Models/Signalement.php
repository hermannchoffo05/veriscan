<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    protected $fillable = [
        'user_id', 'qr_code_id', 'nom_signalant', 'contact_signalant',
        'description', 'photo_preuve', 'statut', 'analyse_ia',
        'latitude', 'longitude', 'region', 'localisation',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lot()
    {
        return $this->hasOneThrough(
            Lot::class, QrCode::class,
            'id', 'id', 'qr_code_id', 'lot_id'
        );
    }
}