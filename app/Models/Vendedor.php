<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $table = 'Vendedor';
    protected $PrimaryKey='idVendedor';
    protected $fillable =['codigo','Alias','Correo','Telefono','CodEmpleado'];
    public $timestamps = false;

}
