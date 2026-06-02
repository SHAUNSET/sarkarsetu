<?php
// Include the config to load environment variables
include("includes/config.php");

// 1. Get the API Key
$apiKey = getenv('GEMINI_API_KEY');
if (empty($apiKey)) {
    die("Error: Configuration error.");
}

// 2. Get user query
$user_question = isset($_POST['query']) ? trim($_POST['query']) : '';

if (empty($user_question)) {
    die("Please enter a question.");
}

// 3. Prepare the Prompt for a UPSC/Citizen expert
$prompt = "You are a helpful assistant for the 'SarkarSetu' government portal. 
           Your audience includes citizens and UPSC aspirants.
           User Question: " . $user_question . "
           
           INSTRUCTIONS:
           - Provide a concise, professional, and helpful answer.
           - If relevant, suggest how this topic relates to government policy or UPSC syllabus.
           - Use bullet points for readability.
           - Keep the tone professional, neutral, and encouraging.";

// 4. API Request
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;
$data = [
    "contents" => [["parts" => [["text" => $prompt]]]]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Keep false for local dev only
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// 5. Output response
if ($response === false) {
    echo "AI Connection Error: " . $error;
} else {
    $result = json_decode($response, true);
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        echo nl2br(htmlspecialchars($result['candidates'][0]['content']['parts'][0]['text']));
    } else {
        echo "The AI is currently unavailable. Please try again.";
    }
}
?>