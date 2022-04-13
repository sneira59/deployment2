<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servidor extends Model
{

    protected $table = "Servidor";
    protected $primaryKey = "idServidor";
    public $timestamps = false;
    
    protected $guarded = [];
}
