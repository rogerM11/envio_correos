<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facturas saldo > 0 {{ $vendedor->Alias }} - Cofasa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 10px 30px 30px 30px;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
    
        table th, table td {
            padding: 3px 4px;
            text-align: left;
            border: none; /* sin bordes */
        }
    
        table th {
            background-color: #f0f0f0;
        }
    
        .info td {
            border: none;
            padding: 2px 4px;
        }
    </style>
    
</head>
<body>
    <div class="header">
        <center><h2>Compañía Farmacéutica S.A de C.V.</h2>
        <p>Listado de Facturas vivas a cobrar por <b>{{ $vendedor->Alias }}</b></p>
        <p>Hasta: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p></center>
    </div>

    <div class="info">
        <table style="width: 100%; border: none;">
            <tr>
                <td><strong>Código Vendedor:</strong> {{ $vendedor->codigo }}</td>
                <td><strong>Correo:</strong> {{ $vendedor->Correo }}</td>
            </tr>
            <tr>
                <td><strong>Nombre:</strong> {{ $vendedor->Alias }}</td>
                <td><strong>Codigo Empleado:</strong> {{ $vendedor->CodEmpleado }}</td>
            </tr>
        </table>
    </div>
    

    <table>
        <thead>
            <tr>
                <th>Cod. Cliente</th>
                <th>Cliente</th>
                <th>Ciudad</th>
                <th>Tipo Doc.</th>
                <th>No. Comp.</th>
                <th>Fecha Emision</th>
                <th>Fecha Vence</th>
                <th>Monto</th>
                <th>Abono</th>
                <th>Saldo</th>
                <th>Credito</th>
                <th>Días Vencidos</th>
                <th>Número Control</th>
                <th>Cod.Generacion</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facturas->sortBy('FechaVence') as $factura)
            <tr>
                <td>{{ $factura->Codcli }}</td>
                <td>{{ $factura->Establecimiento }}</td>
                <td>{{ $factura->ciudad }}</td>
                <td>{{  $factura->Tipo }}</td>
                <td>{{ $factura->numfac }}</td>
                <td>{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($factura->FechaVence)->format('d/m/Y') }}</td>
                <td>{{ number_format($factura->Monto, 2) }}</td>
                <td>{{ number_format($factura->Abonos,2) }}</td>
                <td>{{ number_format($factura->Saldo,2) }}</td>
                <td>{{ $factura->conPago }}</td>
                <td>{{ $factura->Dia }}</td>
                <td>{{ $factura->ws_numeroControl }}</td>
                <td>{{ $factura->ws_codigoGeneracion }}</td>
            </tr>
            @endforeach
        </tbody>
        <br>
        <tfoot>
            <tr>
                <td><strong>Totales</strong></td>
                <td colspan="6" style="text-align: right;"></td>
                <td style="border-bottom: 3px double #000;"><strong>{{ number_format($facturas->sum('Monto'), 2) }}</strong></td>
                <td style="border-bottom: 3px double #000;"><strong>{{ number_format($facturas->sum('Abonos'), 2) }}</strong></td>
                <td style="border-bottom: 3px double #000;"><strong>{{ number_format($facturas->sum('Saldo'), 2) }}</strong></td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        
        
    </table>

  

    <div class="footer">
        <p>Fecha y Hora de generacion del listado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
