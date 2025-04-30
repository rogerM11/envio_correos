<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendedorNemul extends Model
{
    protected $connection = 'DB_CONNECTION_INCCFACNE';
    protected $table = 'Vendedor';
    protected $PrimaryKey='idVendedor';
    protected $fillable =['codigo','Alias','Correo','Telefono','CodEmpleado'];
    public $timestamps = false;
}
