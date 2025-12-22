<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
include('../autoload.php');

if (isset($_GET['code'])) {

    /* =====================
     * OAuth token exchange
     * ===================== */

    $url = 'https://www.swcombine.com/ws/oauth2/token/';
    $data = array(
        'code' => $_GET['code'],
        'client_id' => SWC_API_CLIENT_ID,
        'client_secret' => SWC_API_CLIENT_SECRET,
        'redirect_uri' => SWC_API_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'access_type' => 'offline'
    );

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: SWC OAuth Client'
        ],
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        die('cURL error (token): ' . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        die("OAuth HTTP $httpCode response:\n$response");
    }

    $tokenResult = json_decode($response);

    if (!isset($tokenResult->access_token)) {
        die('Invalid OAuth response: ' . $response);
    }

    $access_token  = $tokenResult->access_token;
    $refresh_token = $tokenResult->refresh_token ?? null;
}

if (!empty($access_token)) {

    $url = 'https://www.swcombine.com/ws/v2.0/character/?access_token=' . urlencode($access_token);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'User-Agent: SWC OAuth Client'
        ],
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        die('cURL error (character): ' . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        die("Character API HTTP $httpCode response:\n$response");
    }

    $charResult = json_decode($response);

    if (!isset($charResult->swcapi->character->name)) {
        die('Invalid character API response: ' . $response);
    }
    
    $username = $charResult->swcapi->character->name;
    User::oAuthLogin($username, $access_token, $refresh_token);
}