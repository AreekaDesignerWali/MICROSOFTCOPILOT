<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $query = strtolower(trim($input['query'] ?? ''));
    $user_id = trim($input['user_id'] ?? '');

    if (empty($query) || empty($user_id)) {
        echo json_encode(['response' => 'Invalid input data. Please provide a query and user ID.']);
        exit;
    }

    // Local fallback responses
    $ai_response = '';
    if (in_array($query, ['hello', 'hi'])) {
        $ai_response = match ($query) {
            'hello' => 'Hello! How can I assist you today?',
            'hi' => 'Hi there! What would you like to do?',
            default => ''
        };
    } elseif ($query === 'what can you do?') {
        $ai_response = 'I can help with tasks like writing emails, summarizing text, or coding assistance. Try asking me something specific!';
    } else {
        // Handle longer messages with local fallback
        if (strpos($query, 'write') !== false && strpos($query, 'email') !== false) {
            $ai_response = "Here’s a sample professional email:\n\nSubject: Meeting Request\n\nDear [Recipient],\nI hope this message finds you well. I would like to schedule a meeting to discuss [topic]. Please let me know your availability.\n\nBest regards,\n[Your Name]";
        } elseif (strpos($query, 'summarize') !== false && strpos($query, 'text') !== false) {
            $ai_response = "Since no text was provided, here’s a sample summary: The text discusses [topic] and highlights [key point]. It concludes with [conclusion].";
        } elseif (strpos($query, 'help') !== false && strpos($query, 'coding') !== false) {
            $ai_response = "I can assist with coding! Please provide a specific problem or language (e.g., 'Help me with a Python loop'), and I’ll guide you.";
        } else {
            $ai_response = "I’m here to help! Your message was: '$query'. Please try a specific task like 'Write an email' or 'Help me with coding,' or ask a question!";
        }
    }

    // Attempt API call for non-handled queries
    if (empty($ai_response)) {
        $api_key = 'YOUR_COHERE_API_KEY'; // Replace with your actual Cohere API key
        $url = 'https://api.cohere.ai/v1/generate';

        if (!empty($api_key)) {
            $data = [
                'model' => 'medium',
                'prompt' => $query,
                'max_tokens' => 200,
                'temperature' => 0.7
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $api_key,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            if ($response !== false && $http_code === 200) {
                $result = json_decode($response, true);
                $ai_response = $result['generations'][0]['text'] ?? 'Sorry, the API response was unexpected.';
            } else {
                error_log("API Error - HTTP Code: $http_code, Error: $error, Query: $query, Full Response: " . ($response ?: 'No response'));
            }

            curl_close($ch);
        }
    }

    // Use fallback if no API response
    if (empty($ai_response)) {
        $ai_response = "I couldn’t connect to the AI service. Your message was: '$query'. Try a simpler request or check back later!";
    }

    // Save response to database
    saveResponse($user_id, $query, $ai_response);
    echo json_encode(['response' => $ai_response]);
}

function saveResponse($user_id, $query, $response) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO conversations (user_id, query, response) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $query, $response]);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
    }
}
?>
