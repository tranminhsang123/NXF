<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Học Tiếng Nhật - 日本語を学ぶ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            -webkit-text-size-adjust: 100%;
        }

        html, body {
            overflow-x: clip;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #111827;
        }
        
        /* Sakura Animation */
        @keyframes sakura-fall {
            from {
                transform: translateY(-100px) rotate(0deg);
                opacity: 1;
            }
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .sakura {
            position: fixed;
            width: 20px;
            height: 20px;
            background: #ffb7c5;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: sakura-fall 8s infinite linear;
            z-index: -1;
            pointer-events: none;
        }
        
        /* Text gradient */
        .text-gradient {
            background: linear-gradient(135deg, #ef4444, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Card hover effect */
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        /* Hero highlight animations */
        @keyframes hero-card-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .hero-card-float {
            animation: hero-card-float 6s ease-in-out infinite;
        }

        @keyframes hero-pulse-glow {
            0%, 100% { box-shadow: 0 10px 30px rgba(220, 38, 38, 0.25); }
            50% { box-shadow: 0 20px 45px rgba(220, 38, 38, 0.45); }
        }

        .hero-cta-main {
            animation: hero-pulse-glow 2.4s ease-in-out infinite;
        }
        
        /* Japanese pattern background */
        .pattern-bg {
            background-image: radial-gradient(circle, #fee2e2 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .mobile-snap {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .mobile-snap::-webkit-scrollbar {
            display: none;
        }

        @media (max-width: 640px) {
            .sakura {
                display: none;
            }

            .card-hover:hover {
                transform: none;
                box-shadow: inherit;
            }

            .hero-card-float,
            .hero-cta-main {
                animation: none;
            }

            .pattern-bg {
                background-size: 22px 22px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .sakura,
            .hero-card-float,
            .hero-cta-main {
                animation: none;
            }
        }
    </style>
</head>
<body class="bg-white">
    <!-- Floating Sakura Petals -->
    <div class="sakura" style="left: 10%; animation-delay: 0s;"></div>
    <div class="sakura" style="left: 20%; animation-delay: 1s;"></div>
    <div class="sakura" style="left: 30%; animation-delay: 2s;"></div>
    <div class="sakura" style="left: 40%; animation-delay: 3s;"></div>
    <div class="sakura" style="left: 50%; animation-delay: 4s;"></div>
    <div class="sakura" style="left: 60%; animation-delay: 5s;"></div>
    <div class="sakura" style="left: 70%; animation-delay: 6s;"></div>
    <div class="sakura" style="left: 80%; animation-delay: 7s;"></div>
    <div class="sakura" style="left: 90%; animation-delay: 8s;"></div>

    @include('layouts.header')

    @include('sections.hero')

    @include('sections.features')

    @include('sections.learning-path')

    @include('sections.testimonials')

    @include('sections.cta')

    @include('layouts.footer')

</body>
</html>
