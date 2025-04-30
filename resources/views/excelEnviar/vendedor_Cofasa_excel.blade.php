<table>
        <thead>
            <tr>
                <th>Cod. Cliente</th>
                <th>Cliente</th>
                <th>Ciudad</th>
                <th>No. Comp.</th>
                <th>Fecha Emision</th>
                <th>Fecha Vence</th>
                <th>Monto</th>
                <th>Abono</th>
                <th>Dev/Desc</th>
                <th>Saldo</th>
                <th>Credito</th>
                <th>Días</th>
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
                <td>{{ $factura->numfac }}</td>
                <td>{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($factura->FechaVence)->format('d/m/Y') }}</td>
                <td>{{ number_format($factura->Monto, 2) }}</td>
                <td>{{ number_format($factura->Abonos,2) }}</td>
                <td>{{ number_format($factura->Dev_Des,2) }}</td>
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
                <td colspan="5" style="text-align: right;"></td>
                <td style="border-bottom: 3px double #000;"><strong>{{ number_format($facturas->sum('Monto'), 2) }}</strong></td>
                <td></td>
                <td></td>
                <td style="border-bottom: 3px double #000;"><strong>{{ number_format($facturas->sum('Saldo'), 2) }}</strong></td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        
        
    </table>
