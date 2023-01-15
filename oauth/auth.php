<?php

include('../autoload.php');

if($_GET['code']) {
    $url = 'http://www.swcombine.com/ws/oauth2/token/';
    $data = array(
        'code' => $_GET['code'],
        'client_id' => SWC_API_CLIENT_ID,
        'client_secret' => SWC_API_CLIENT_SECRET,
        'redirect_uri' => SWC_API_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'access_type' => 'offline'
    );

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = simplexml_load_string(file_get_contents($url, false, $context));
    $result = json_encode($result);
    $result = json_decode($result);
    $access_token = $result->access_token;
    $refresh_token = $result->refresh_token;
}

if($result){
    $url = 'http://www.swcombine.com/ws/v2.0/character/?access_token='.$result->access_token;
    $result = simplexml_load_string(file_get_contents($url, false));
    $result = json_encode($result);
    $result = json_decode($result);
    $username = $result->character->name;
    User::oAuthLogin($username, $access_token, $refresh_token);
}

?>