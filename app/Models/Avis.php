<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 'avis';

    protected $fillable = ['nom', 'note', 'commentaire', 'approuve'];
}