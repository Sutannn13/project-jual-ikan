<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Mobile Layout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white p-4">
    <h1 class="text-2xl font-bold mb-4">Test Layout Mobile</h1>
    
    <div class="bg-white/10 p-4 rounded-lg mb-4">
        <p>Jika kamu bisa baca ini, berarti:</p>
        <ul class="list-disc ml-6 mt-2 space-y-1">
            <li>Vite assets berhasil load</li>
            <li>Tailwind CSS bekerja</li>
            <li>Tidak ada masalah routing</li>
        </ul>
    </div>
    
    <div class="grid grid-cols-2 gap-2">
        <div class="bg-teal-500/20 p-3 rounded text-center">Card 1</div>
        <div class="bg-cyan-500/20 p-3 rounded text-center">Card 2</div>
        <div class="bg-orange-500/20 p-3 rounded text-center">Card 3</div>
        <div class="bg-emerald-500/20 p-3 rounded text-center">Card 4</div>
    </div>
    
    <div class="mt-4 bg-yellow-500/20 p-4 rounded">
        <p class="font-bold">Info:</p>
        <p class="text-sm">Screen width: <span id="width"></span>px</p>
        <p class="text-sm">Viewport height: <span id="height"></span>px</p>
    </div>
    
    <script>
        document.getElementById('width').textContent = window.innerWidth;
        document.getElementById('height').textContent = window.innerHeight;
    </script>
</body>
</html>
