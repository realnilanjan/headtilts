<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>We'll Be Back Soon!</title>
    <style>
        /* Reset & Fonts */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }

        .logo {
            width: 100px;
            height: 100px;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-bottom: 2rem;
        }

        .countdown {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 0.2;
            }

            50% {
                opacity: 1;
            }
        }

        /* Background Animation */
        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/bo-play.png');
            /* Lightweight stars pattern */
            background-size: cover;
            animation: twinkle 30s linear infinite;
            z-index: -1;
            opacity: 0.1;
        }

        @keyframes twinkle {
            from {
                background-position: 0 0;
            }

            to {
                background-position: 1000px 500px;
            }
        }

        .button {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid white;
            padding: 0.7rem 1.5rem;
            border-radius: 30px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .button:hover {
            background-color: white;
            color: #2c5364;
        }
    </style>
</head>

<body>

    <div class="stars"></div>

    <div class="container">
        <!-- Replace with your logo -->
        <img src="assets/images/Logo_White.png" alt="Logo" class="logo" />

        <h1>Under Maintenance 🛠️</h1>
        <p>We're currently working hard to improve your experience. We'll be back online shortly.</p>

        <button class="button" onclick="window.location.reload()">Refresh Page</button>
    </div>

</body>

</html>