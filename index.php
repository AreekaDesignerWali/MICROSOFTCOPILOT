<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'chat.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Copilot Clone - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }
        .container {
            max-width: 800px;
            text-align: center;
            padding: 20px;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .btn {
            background: #0078d4;
            color: #fff;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 1.1rem;
            transition: background 0.3s, transform 0.2s;
        }
        .btn:hover {
            background: #005ba1;
            transform: scale(1.05);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            .btn {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to AI Assistant</h1>
        <p>Your intelligent companion for text generation, task assistance, and smart suggestions.</p>
        <a href="login.php" class="btn">Get Started</a>
        <div class="features">
            <div class="feature-card">
                <h3>Chat with AI</h3>
                <p>Ask questions or generate text with our powerful AI.</p>
            </div>
            <div class="feature-card">
                <h3>Task Automation</h3>
                <p>Automate tasks like writing emails or summarizing text.</p>
            </div>
            <div class="feature-card">
                <h3>Save Conversations</h3>
                <p>Keep track of your interactions and save important responses.</p>
            </div>
        </div>
    </div>
</body>
</html>
