<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table='dpto';
    protected $PrimaryKey ='idDpto';
    protected $fillable =['nombre','Zona','CodigoMH'];
    public $timestamps = false;
}
