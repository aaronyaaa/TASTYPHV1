<?php
require_once __DIR__ . '/../includes/groq_client.php';
header('Content-Type: application/json');

$prompt = $_GET['q'] ?? '';
if (!$prompt) {
    echo json_encode(['error' => 'No input provided.']);
    exit;
}

$aiPrompt = "Suggest a traditional Filipino kakanin recipe based on this request: " . $prompt;

$response = askGroq($aiPrompt);

echo json_encode([
    'steps' => $response
]);
exit;
