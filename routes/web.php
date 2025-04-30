<?php
use App\Http\Controllers\VendedorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
Route::get('/', function () {
    return view('welcome');
});



Route::get('/test-db', function () {
    try {
        $result = DB::connection()->select("SELECT GETDATE() AS fecha");
        return 'Conexión exitosa. Fecha actual del servidor: ' . $result[0]->fecha;
    } catch (\Exception $e) {
        return 'Error de conexión: ' . $e->getMessage();
    }
});

Route::get('/getListVendedor',[VendedorController::class,'getVendedores']);
Route::get('/datosv/{codigoVendedor}', [VendedorController::class, 'getFacturasVivasPorVendedor']);


Route::get('/test-db2', function () {
    try {
        $result = DB::connection('DB_CONNECTION_INCCFACNE')->select("SELECT GETDATE() AS fecha");
        return 'Conexión exitosa. Fecha actual del servidor: ' . $result[0]->fecha;
    } catch (\Exception $e) {
        return 'Error de conexión: ' . $e->getMessage();
    }
});




Route::get('/test-sp', function() {
    try {
        $params = [
            'idvendedor' => 2,
            'fechafin' => now()->format('d/m/Y'),
            'numdias' => 0,
            'Ordenado' => 0,
            'negativo' => 0
        ];
        

        $results = DB::connection('DB_CONNECTION_INCCFACNE')
                   ->select("EXEC oSP_Report_facturas_vivas2 ?, ?, ?, ?, ?", array_values($params));
        

        return response()->json([
            'success' => true,
            'parameters' => $params,
            'data' => $results
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});