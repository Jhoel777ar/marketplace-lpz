<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pago exitoso</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-50">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Pago recibido</h1>
        <p class="mb-4">Gracias por tu compra. Si necesitas, guarda este número de pago:</p>
        @if(isset($payment_intent))
            <p class="font-mono bg-gray-100 p-2 rounded">{{ $payment_intent }}</p>
        @else
            <p class="text-gray-600">No se recibió un identificador de pago.</p>
        @endif

        <a href="{{ route('dashboard') }}" class="inline-block mt-6 bg-black text-white px-4 py-2 rounded">Volver al inicio</a>
    </div>
</body>
</html>
