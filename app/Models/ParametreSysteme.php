<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ParametreSysteme extends Model
{
    protected $table    = 'parametres_systeme';
    protected $fillable = ['cle', 'valeur', 'type', 'description'];
    /**
     * Récupère la valeur d'un paramètre par sa clé
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        $param = static::where('cle', $cle)->first();
        return $param ? $param->valeur : $default;
    }
    /**
     * Définit la valeur d'un paramètre
     */
    public static function set(string $cle, mixed $valeur): void
    {
        static::updateOrCreate(['cle' => $cle], ['valeur' => $valeur]);
    }
    /**
     * Retourne tous les paramètres sous forme clé => valeur
     */
    public static function tous(): array
    {
        return static::all()->pluck('valeur', 'cle')->toArray();
    }
}