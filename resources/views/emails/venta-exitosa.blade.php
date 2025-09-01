<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra Exitosa</title>
</head>
<body>
    <h2>¡Compra Exitosa!</h2>
    <p>Hola <strong>{{ $usuarioNombre }}</strong>,</p>
    <p>Tu compra ha sido realizada correctamente.</p>
    <p><strong>ID de la venta:</strong> {{ $ventaId }}</p>
    <p><strong>Total:</strong> Bs. {{ number_format($total, 2) }}</p>
    <p>Gracias por confiar en nuestro marketplace.</p>
</body>
</html>
