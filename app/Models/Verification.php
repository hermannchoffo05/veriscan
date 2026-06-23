<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'user_id', 'qr_code_id', 'ip_address',
        'localisation', 'appareil', 'resultat'
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}