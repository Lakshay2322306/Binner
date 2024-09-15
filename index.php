<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$botToken = $_ENV['BOT_TOKEN'];
$website = "https://api.telegram.org/bot" . $botToken;
$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

// Webhook Setup
if (isset($_GET['setWebhook'])) {
    $webhook_url = "https://your-render-url.onrender.com";  // Replace with your Render app URL
    $response = file_get_contents($website . "/setWebhook?url=" . $webhook_url);
    echo $response;
    exit();
}

$chatId = $update["message"]["chat"]["id"];
$firstname = $update["message"]["from"]["first_name"];
$username = $update["message"]["from"]["username"];
$message = $update["message"]["text"];
$message_id = $update["message"]["message_id"];

// More Powerful Commands

// Start Command
if (strpos($message, "/start") === 0) {
    sendMessage($chatId, "Welcome to Switchblade Checker Bot. Commands available with /cmds.", $message_id);
}

// Commands List
elseif (strpos($message, "/cmds") === 0) {
    sendMessage($chatId, "Available Commands: %0A/bin [BIN] - Check BIN info%0A/key [SK Key] - Check SK Key%0A/info - User Info", $message_id);
}

// BIN Check Command
elseif (preg_match("/\/bin\s+(\d{6})/", $message, $matches)) {
    $bin = $matches[1];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/' . $bin);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    $binData = json_decode($response, true);

    $bank = strtoupper($binData['bank']['name'] ?? 'UNKNOWN');
    $country = strtoupper($binData['country']['name'] ?? 'UNKNOWN');
    $brand = strtoupper($binData['brand'] ?? 'UNKNOWN');
    $scheme = strtoupper($binData['scheme'] ?? 'UNKNOWN');
    $type = strtoupper($binData['type'] ?? 'UNKNOWN');
    $emoji = $binData['country']['emoji'] ?? '';

    sendMessage($chatId, "<b>Valid BIN</b>%0ABIN: <b>$bin</b>%0ABANK: <b>$bank</b>%0A COUNTRY: <b>$country $emoji</b>%0ABRAND: <b>$brand</b>%0ATYPE: <b>$type</b>", $message_id);
}

// Check SK Key (Secret Key) Command
elseif (preg_match("/\/key\s+(\w+)/", $message, $matches)) {
    $sk_key = $matches[1];
    // Add logic to validate the SK Key
    sendMessage($chatId, "Checking SK Key: $sk_key (This is where you'd implement validation)", $message_id);
}

// User Info Command
elseif (strpos($message, "/info") === 0) {
    sendMessage($chatId, "⁕ ─ INFO ─ ⁕%0AChat ID: <code>$chatId</code>%0AName: $firstname%0AUsername: @$username", $message_id);
}

// Send Message Function
function sendMessage($chatId, $message, $message_id = null) {
    global $botToken;
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    $post_fields = [
        'chat_id' => $chatId,
        'text' => $message,
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

?>
