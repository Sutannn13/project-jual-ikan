
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Terjadi Kesalahan | FishMarket</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0891b2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0c4a6e 0%, #0e7490 50%, #0891b2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; -webkit-font-smoothing: antialiased;
        }
        .container {
            text-align: center; padding: 2rem; max-width: 520px; width: 100%;
        }
        .error-icon {
            width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 2rem;
            display: flex; align-items: center; justify-content: center;
            background: rgba(239,68,68,0.15); backdrop-filter: blur(20px);
            border: 1px solid rgba(239,68,68,0.3);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        .error-icon i { font-size: 3rem; color: #fca5a5; }
        .error-code {
            font-size: clamp(4rem, 12vw, 7rem); font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, #fca5a5, #f87171, #fca5a5);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; margin-bottom: 0.5rem;
        }
        h1 { font-size: clamp(1.25rem, 4vw, 1.75rem); font-weight: 700; margin-bottom: 1rem; }
        p { color: rgba(255,255,255,0.6); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2.5rem; }
        .btn-group { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.875rem 2rem; border-radius: 1rem; font-weight: 700; font-size: 0.95rem;
            color: white; text-decoration: none; transition: all 0.3s; border: none; cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0891b2, #14b8a6);
            box-shadow: 0 10px 30px rgba(6,182,212,0.4);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 15px 40px rgba(6,182,212,0.5); }
        .btn-secondary {
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.15); transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
            50% { transform: scale(1.03); box-shadow: 0 20px 50px rgba(239,68,68,0.15); }
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.3; z-index: -1; }
        .orb-1 { width: 400px; height: 400px; top: -100px; left: -100px; background: #22d3ee; }
        .orb-2 { width: 500px; height: 500px; bottom: -150px; right: -150px; background: #14b8a6; }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="error-code">500</div>
        <h1>Oops! Terjadi Kesalahan</h1>
        <p>Server sedang mengalami gangguan. Tim kami sudah diberitahu dan sedang memperbaikinya. Silakan coba lagi nanti.</p>
        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-home"></i> Ke Beranda
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Coba Lagi
            </a>
        </div>
    </div>
</body>
</html>
