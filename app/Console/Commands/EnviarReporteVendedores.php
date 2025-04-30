<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendedor;
use App\Models\VendedorNemul;
use Illuminate\Support\Facades\Mail;
use App\Mail\correos_vendedores;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EnviarReporteVendedores extends Command
{
  
    protected $signature = 'app:enviar-reporte-vendedores';

 
     
    protected $description = 'Envia un correo de prueba con PDF a un vendedor';


    public function handle(){
    $vendedoresCofasa = Vendedor::where('activo', 1)
        ->where('envioCC', 1)
        ->get();

    foreach ($vendedoresCofasa as $vendedor) {

        $idNemul =  $vendedor->CodEmpleado;

        $facturasCofasa = DB::connection()
            ->select('EXEC oSP_Report_facturas_vivas2 ?, ?, ?, ?, ?', [
                $vendedor->idVendedor,
                now()->format('d/m/Y'),
                0,
                0,
                0
            ]);

        // vendedor en Nemul por idVendedor
        $vendedorNemul = VendedorNemul::where('CodEmpleado', $idNemul)
            ->where('activo', 1)
            ->where('envioCC', 1)
            ->first();

        $facturasNemul = collect();
        if ($vendedorNemul) {
            $this->info("Procesando vendedor Nemul - ID: {$vendedorNemul->idVendedor}, Código: {$vendedorNemul->codigo}");
            
            $facturasNemul = DB::connection('DB_CONNECTION_INCCFACNE')
                ->select('EXEC oSP_Report_facturas_vivas2 ?, ?, ?, ?, ?', [
                    $vendedorNemul->idVendedor, 
                    now()->format('d/m/Y'),
                    0,
                    0,
                    0
                ]);
                // $this->info(json_encode($facturasNemul));   esto es de prueba para ver si se envía

            
        }else{
            $this->info("No se ejecutó el sp de NEMUL para el vendedor con id: {$vendedorNemul->idVendedor},  codigo: {$vendedorNemul->codigo}");

        }

        if (empty($facturasCofasa) && empty($facturasNemul)) {
            $this->info("No hay facturas para {$vendedor->Alias} (Cofasa:{$vendedor->idVendedor}, Nemul:{$idVendedor}), se omite el envío.");
            continue;
        }

        // Generar PDFs
        $pdfCofasa = !empty($facturasCofasa)
            ? Pdf::loadView('pdfsEnviar.vendedor', [
                'vendedor' => $vendedor,
                'facturas' => collect($facturasCofasa),
            ])->setPaper('a4', 'landscape')
            : null;

        $pdfNemul = !empty($facturasNemul)
            ? Pdf::loadView('pdfsEnviar.vendedorNemul', [
                'vendedor' => $vendedorNemul,
                'facturas' => collect($facturasNemul),
            ])->setPaper('a4', 'landscape')
            : null;

         // aca genero los excel
              $spreadsheet = new Spreadsheet();

              // --- Cofasa
              if (!empty($facturasCofasa)) {
                // Ordenar las facturas por FechaVence
                usort($facturasCofasa, function ($a, $b) {
                    return strtotime($a->FechaVence) - strtotime($b->FechaVence);
                });
            
                // Crear la hoja para Cofasa
                $sheetCofasa = $spreadsheet->getActiveSheet();
                $sheetCofasa->setTitle('Cofasa');
            
                // Encabezados
                $headersCofasa = [
                    'Cod. Cliente', 'Cliente', 'Ciudad', 'No. Comp.', 'Fecha Emision', 
                    'Fecha Vence', 'Monto', 'Abono', 'Dev/Desc', 'Saldo', 
                    'Credito', 'Días', 'Número Control', 'Cod.Generacion'
                ];
            
                // Agregar los encabezados
                foreach ($headersCofasa as $index => $header) {
                    $cell = chr(65 + $index) . '1'; 
                    $sheetCofasa->setCellValue($cell, $header);
                }
            
                // Inicializar los totales
                $totalMonto = 0;
                $totalAbono = 0;
                $totalSaldo = 0;
            
                // Llenar la hoja con datos ordenados
                foreach ($facturasCofasa as $row => $factura) {
                    $values = [
                        $factura->Codcli,
                        $factura->Establecimiento,
                        $factura->ciudad,
                        $factura->numfac,
                        \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y'),
                        \Carbon\Carbon::parse($factura->FechaVence)->format('d/m/Y'),
                        $factura->Monto,
                        $factura->Abonos,
                        $factura->Dev_Des,
                        $factura->Saldo,
                        $factura->conPago,
                        $factura->Dia,
                        $factura->ws_numeroControl,
                        $factura->ws_codigoGeneracion,
                    ];
            
                    // Agregar los valores a la hoja
                    foreach ($values as $col => $value) {
                        $cell = chr(65 + $col) . ($row + 2);
                        $sheetCofasa->setCellValue($cell, $value);
                    }
            
                    // Sumar los totales
                    $totalMonto += $factura->Monto;
                    $totalAbono += $factura->Abonos;
                    $totalSaldo += $factura->Saldo;
                    
                }
            
                // Agregar los totales al final de la tabla
                $row = count($facturasCofasa) + 2;
                $sheetCofasa->setCellValue('A' . $row, 'Totales');
                $sheetCofasa->setCellValue('G' . $row, $totalMonto);
                $sheetCofasa->setCellValue('H' . $row, $totalAbono);
                $sheetCofasa->setCellValue('J' . $row, $totalSaldo);
            
                // Estilo para encabezados y celdas
                $highestColumn = $sheetCofasa->getHighestColumn();
                $highestRow = $sheetCofasa->getHighestRow();
            
                // Autoajustar columnas
                foreach (range('A', $highestColumn) as $col) {
                    $sheetCofasa->getColumnDimension($col)->setAutoSize(true);
                }
            
                // Estilo para encabezados
                $sheetCofasa->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9D9D9'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            
                // Estilo para los datos de las celdas
                $sheetCofasa->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
            
      
              // --- Nemul
              if (!empty($facturasNemul)) {
                // Ordenar las facturas por FechaVence
                usort($facturasNemul, function ($a, $b) {
                    return strtotime($a->FechaVence) - strtotime($b->FechaVence);
                });
            
                // Crear la hoja para Nemul
                $sheetNemul = $spreadsheet->createSheet();
                $sheetNemul->setTitle('Nemul');
            
                // Encabezados
                $headersNemul = [
                    'Cod. Cliente', 'Cliente', 'Ciudad', 'No. Comp.', 'Fecha Emision', 
                    'Fecha Vence', 'Monto', 'Abono','Dev_Des', 'Saldo','DiasCliente', 'Días'
                ];
            
                // Agregar los encabezados
                foreach ($headersNemul as $index => $header) {
                    $cell = chr(65 + $index) . '1';
                    $sheetNemul->setCellValue($cell, $header);
                }
            
                // Inicializar los totales
                $totalMonto = 0;
                $totalAbono = 0;
                $totalSaldo = 0;
            
                // Llenar la hoja con datos ordenados
                foreach ($facturasNemul as $row => $factura) {
                    $values = [
                        $factura->Codcli,
                        $factura->Establecimiento,
                        $factura->ciudad,
                        $factura->numfac,
                        \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y'),
                        \Carbon\Carbon::parse($factura->FechaVence)->format('d/m/Y'),
                        $factura->Monto,
                        $factura->Abonos,
                        $factura->Dev_Des,
                        $factura->Saldo,
                        $factura->DiasCliente,
                        $factura->Dia,
                    ];
            
                    // Agregar los valores a la hoja
                    foreach ($values as $col => $value) {
                        $cell = chr(65 + $col) . ($row + 2);
                        $sheetNemul->setCellValue($cell, $value);
                    }
            
                    // Sumar los totales
                    $totalMonto += $factura->Monto;
                    $totalAbono += $factura->Abonos;
                    $totalSaldo += $factura->Saldo;
                }
            
                // Agregar los totales al final de la tabla
                $row = count($facturasNemul) + 2;
                $sheetNemul->setCellValue('A' . $row, 'Totales');
                $sheetNemul->setCellValue('G' . $row, $totalMonto);
                $sheetNemul->setCellValue('H' . $row, $totalAbono);
                $sheetNemul->setCellValue('J' . $row, $totalSaldo);
            
                // Aplicar estilo
                foreach ($spreadsheet->getAllSheets() as $sheet) {
                    $highestColumn = $sheet->getHighestColumn();
                    $highestRow = $sheet->getHighestRow();
            
                    // Autoajustar columnas
                    foreach (range('A', $highestColumn) as $col) {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    }
            
                    // Estilo para encabezados
                    $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '000000'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D9D9D9'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);
            
                    // Estilo para los datos de las celdas
                    $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }
            }
            
      
              // Guardar Excel en memoria
              $tempFile = tempnam(sys_get_temp_dir(), 'excel');
              $writer = new Xlsx($spreadsheet);
              $writer->save($tempFile);
              $excelContent = file_get_contents($tempFile);
              unlink($tempFile);
      

        Mail::to('rogermorales@labcofasa.com')->send(new correos_vendedores($pdfCofasa, $pdfNemul,$excelContent));
        $this->info("Correo enviado para {$vendedor->Alias} (Cofasa:{$vendedor->codigo}, Nemul:{$idNemul})");
    }
}

}

