<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JJ Flowershop - Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1f3b2a 0%, #27ae60 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container {
            text-align: center;
            padding: 2rem;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: white;
            color: #1f3b2a;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .status {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌸 J' J FLOWER SHOP</h1>
        <p class="subtitle">Est. 2023</p>
        <div class="links">
            <a href="/login" class="btn">Login</a>
            <a href="/register" class="btn">Register</a>
            <a href="/customer/dashboard" class="btn">Customer Dashboard</a>
        </div>
        <div class="status">
            <p>✅ Application is running successfully!</p>
            <p style="font-size: 0.9rem; margin-top: 0.5rem; opacity: 0.8;">Deployed on Railway</p>
        </div>
    </div>
</body>
</html> 
