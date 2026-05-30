<?php
session_start();
include("../includes/config.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = mysqli_query($conn, "SELECT * FROM schemes WHERE id = $id");
$scheme = mysqli_fetch_assoc($query);

if(!$scheme) {
    echo "Scheme not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($scheme['title']); ?> - SarkarSetu</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        /* Navbar Styling */
        .navbar { background: #0d1b2a; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .navbar h2 { margin: 0; font-size: 1.2rem; }
        .navbar a { color: white; margin-left: 20px; text-decoration: none; font-weight: 500; }

        .ai-chat-widget { border: 1px solid #e1e4e8; border-radius: 12px; padding: 20px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 20px 0; }
        .chat-header { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-weight: 600; color: #2c3e50; }
        #ai-response { padding: 15px; background: #f8f9fa; border-left: 4px solid #3498db; margin-bottom: 15px; font-size: 14px; line-height: 1.6; }
        .input-group { display: flex; gap: 10px; }
        #scheme-query { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }
        button.ask-btn { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; }

        @media (max-width: 600px) {
            .navbar { flex-direction: column; gap: 10px; }
            .ai-chat-widget { padding: 15px; }
            .input-group { flex-direction: column; }
            #scheme-query { width: 100%; box-sizing: border-box; }
            button.ask-btn { width: 100%; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <h2>SarkarSetu</h2>
        <div>
            <a href="../index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1><?php echo htmlspecialchars($scheme['title']); ?></h1>
        <div class="dashboard-card">
            <p><?php echo nl2br(htmlspecialchars($scheme['description'])); ?></p>
        </div>

        <div class="ai-chat-widget">
            <div class="chat-header"><span>🤖</span> SarkarSetu Assistant</div>
            <div id="ai-response">Ask a question about this scheme...</div>
            <div class="input-group">
                <input type="text" id="scheme-query" placeholder="How do I apply for this?" onkeypress="handleEnter(event, <?php echo $id; ?>)">
                <button class="ask-btn" onclick="askSchemeAI(<?php echo $id; ?>)">Ask AI</button>
            </div>
        </div>
    </div>

    <script>
    // Trigger on Enter key
    function handleEnter(e, id) { if (e.key === 'Enter') askSchemeAI(id); }

    function askSchemeAI(id) {
        const query = document.getElementById('scheme-query').value;
        const responseDiv = document.getElementById('ai-response');
        if(!query) return;
        responseDiv.innerHTML = '<div class="loading">Thinking...</div>';
        
        fetch('ask_scheme_ai.php?id=' + id + '&query=' + encodeURIComponent(query))
            .then(res => res.text())
            .then(data => { responseDiv.innerHTML = data; });
    }
    </script>
</body>
</html>