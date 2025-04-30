<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function getDepartamentos(){
        $departamentos = Vendedor::all(); 
        return $departamentos;
        
}
}
