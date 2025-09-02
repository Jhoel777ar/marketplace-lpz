<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura de compra - Tu ex Market</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #2d89ef, #6a11cb);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 25px;
        }

        h2 {
            color: #2d89ef;
            margin-top: 0;
        }

        h3 {
            color: #444;
            margin-bottom: 8px;
        }

        .info p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
        }

        table thead {
            background: #f0f4f8;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            font-weight: 600;
            color: #555;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .total-row td {
            font-weight: bold;
            border-top: 2px solid #ddd;
        }

        .footer {
            background: #fafafa;
            padding: 15px;
            text-align: center;
            font-size: 13px;
            color: #777;
            border-top: 1px solid #eee;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: white;
        }

        .badge-pagado {
            background: #28a745;
        }

        .badge-pendiente {
            background: #ffc107;
            color: #222;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1>🛍️ Tu ex Market</h1>
            <p>Factura electrónica de tu compra</p>
        </div>

        <div class="content">
            <h2>¡Gracias por tu compra!</h2>
            <p>Hola <strong>{{ $usuario->name }}</strong> ({{ $usuario->email }}),</p>
            <p>Tu compra se ha realizado correctamente. Aquí tienes el detalle de tu factura:</p>

            <h3>📌 Datos de la venta</h3>
            <div class="info">
                <p><strong>ID Venta:</strong> #{{ $venta->id }}</p>
                <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong>
                    @if ($venta->estado === 'pagado')
                        <span class="badge badge-pagado">Pagado</span>
                    @else
                        <span class="badge badge-pendiente">{{ ucfirst($venta->estado) }}</span>
                    @endif
                </p>
            </div>

            <h3>📦 Dirección de envío</h3>
            @if ($envio)
                <p>{{ $envio->direccion }}, {{ $envio->ciudad }}, {{ $envio->departamento }} - {{ $envio->pais }} (CP:
                    {{ $envio->codigo_postal }})</p>
            @else
                <p>No se registró dirección de envío.</p>
            @endif

            <h3>🛒 Productos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $item)
                        <tr>
                            <td>{{ $item->producto->nombre }}</td>
                            <td>{{ $item->cantidad }}</td>
                            <td>Bs. {{ number_format($item->producto->precio, 2) }}</td>
                            <td>Bs. {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3">TOTAL</td>
                        <td>Bs. {{ number_format($venta->total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top:20px;">Esperamos que disfrutes tu compra 💙</p>
            <p>Atentamente,<br><strong>El equipo de Tu ex Market</strong></p>
        </div>
        <div class="footer">
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>Powered by <strong>ARK DEV SYSTEM</strong></p>
        </div>
    </div>
</body>

</html>
