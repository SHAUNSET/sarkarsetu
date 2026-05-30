<?php
// ask_scheme_ai.php
include("../includes/config.php");

// 1. Get and Validate API Key
$apiKey = getenv('GEMINI_API_KEY');
if (empty($apiKey)) {
    die("Error: GEMINI_API_KEY is not defined.");
}

$scheme_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_question = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($scheme_id === 0 || empty($user_question)) {
    die("Error: Missing scheme ID or question.");
}

// 2. Fetch scheme context
$stmt = $conn->prepare("SELECT title, description FROM schemes WHERE id = ?");
$stmt->bind_param("i", $scheme_id);
$stmt->execute();
$scheme = $stmt->get_result()->fetch_assoc();

if (!$scheme) { die("Error: Scheme not found."); }

// 3. Prepare Prompt
$prompt = "You are a helpful assistant for the 'SarkarSetu' government portal. 
           User is asking about: " . $scheme['title'] . ".
           Details: " . $scheme['description'] . "
           User Question: " . $user_question . "
           
           INSTRUCTIONS:
           - Provide a concise, professional answer.
           - If the scheme has an official government website URL in the details, explicitly highlight it as 'Official Portal'. 
           - If no URL is provided, provide a generic search query link to the official state government website.
           - Use bullet points for readability.
           - Keep the tone helpful and encouraging.";

// 4. API Request
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . $apiKey;
$data = [
    "contents" => [["parts" => [["text" => $prompt]]]]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// 5. Handle Results
if ($response === false) {
    echo "Connection Error: " . $error;
} else {
    $result = json_decode($response, true);
    if (isset($result['error'])) {
        echo "API Error: " . $result['error']['message'];
    } elseif (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        echo nl2br(htmlspecialchars($result['candidates'][0]['content']['parts'][0]['text']));
    } else {
        echo "Unexpected response from AI. Please try again.";
    }
}
?>