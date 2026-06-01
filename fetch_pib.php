<?php
// fetch_pib.php
// Using the URL that is currently providing you content
$rss_url = "https://pib.gov.in/RssMain.aspx?ModId=6&Lang=1&Regid=3";
$cache_file = "pib_cache.xml";

// 1. Only fetch if the file is missing, empty, or older than 1 hour
if (!file_exists($cache_file) || filesize($cache_file) == 0 || (time() - filemtime($cache_file) > 3600)) {
    
    // 2. Set up context to mimic a browser (avoids being blocked)
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
        ]
    ];
    $context = stream_context_create($options);

    // 3. Fetch the content
    $xml_data = @file_get_contents($rss_url, false, $context);
    
    // 4. Validate: Only save if the data actually contains items
    if ($xml_data && strpos($xml_data, '<item>') !== false) {
        file_put_contents($cache_file, $xml_data);
    }
}
?>