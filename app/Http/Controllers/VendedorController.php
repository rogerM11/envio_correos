<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Vendedor;
class VendedorController extends Controller
{
   public function getVendedores(){
    $vendedores = Vendedor::all(); 
    return $vendedores;
    
    }
   
}
