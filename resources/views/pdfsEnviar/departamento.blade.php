<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Vendedor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 0;
            font-size: 13px;
        }

        .info {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Compañía Farmacéutica S.A de C.V.</h2>
        <p>Departamento <b>{{ $departamento->Alias }}</b></p>
    </div>

</body>
</html>
