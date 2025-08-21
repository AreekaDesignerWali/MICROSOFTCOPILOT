<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch chat history
$stmt = $pdo->prepare("SELECT id, query, response, is_saved FROM conversations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant - Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f5f5f5;
            color: #333;
            display: flex;
            min-height: 100vh;
        }
        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .sidebar {
            width: 300px;
            background: #fff;
            padding: 20px;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }
        .chat-area {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }
        .user-message {
            background: #0078d4;
            color: #fff;
            margin-left: 20%;
            margin-right: 10px;
        }
        .ai-message {
            background: #f0f0f0;
            color: #333;
            margin-right: 20%;
            margin-left: 10px;
        }
        .input-area {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
        }
        button {
            padding: 15px 30px;
            background: #0078d4;
            color: #fff;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #005ba1;
        }
        .history-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.2s;
        }
        .history-item:hover {
            background: #f0f0f0;
        }
        .saved {
            background: #e6f3ff;
        }
        .example-prompts {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .example-prompt {
            background: #0078d4;
            color: #fff;
            padding: 10px 15px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .example-prompt:hover {
            background: #005ba1;
        }
        .action-buttons button {
            margin-left: 10px;
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        @media (max-width: 800px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #ddd;
            }
            .user-message, .ai-message {
                margin-left: 10px;
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h3>Chat History</h3>
            <?php foreach ($history as $item): ?>
                <div class="history-item <?php echo $item['is_saved'] ? 'saved' : ''; ?>" data-id="<?php echo $item['id']; ?>">
                    <p><?php echo htmlspecialchars(substr($item['query'], 0, 50)) . '...'; ?></p>
                    <div class="action-buttons">
                        <button onclick="saveResponse(<?php echo $item['id']; ?>)"><?php echo $item['is_saved'] ? 'Unsave' : 'Save'; ?></button>
                        <button onclick="deleteResponse(<?php echo $item['id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="chat-area">
            <div class="example-prompts">
                <div class="example-prompt" onclick="setPrompt('Write a professional email')">Write a professional email</div>
                <div class="example-prompt" onclick="setPrompt('Summarize this text')">Summarize this text</div>
                <div class="example-prompt" onclick="setPrompt('Help me with coding')">Help me with coding</div>
            </div>
            <div class="chat-box" id="chatBox"></div>
            <div class="input-area">
                <input type="text" id="userInput" placeholder="Ask me anything...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>
    <script>
        const chatBox = document.getElementById('chatBox');
        const userInput = document.getElementById('userInput');

        function setPrompt(prompt) {
            userInput.value = prompt;
        }

        async function sendMessage() {
            const query = userInput.value.trim();
            if (!query) return;

            // Display user message
            const userMsg = document.createElement('div');
            userMsg.className = 'message user-message';
            userMsg.textContent = query;
            chatBox.appendChild(userMsg);
            userInput.value = '';

            // Call AI API
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ query, user_id: <?php echo $user_id; ?> })
            });
            const data = await response.json();

            // Display AI response
            const aiMsg = document.createElement('div');
            aiMsg.className = 'message ai-message';
            aiMsg.textContent = data.response || 'Error fetching response';
            chatBox.appendChild(aiMsg);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        async function saveResponse(id) {
            await fetch('save_response.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            window.location.reload();
        }

        async function deleteResponse(id) {
            await fetch('delete_response.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            window.location.reload();
        }

        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>
