<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './google-api-php-client/src');


$client = new Google_Client();
$client->setAuthConfigFile('config.json');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/index.php');

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    $adsenseService = new Google_Service_AdSense($client);
    $resource = $adsenseService->reports->generate("today-1m", "today");
    var_dump($resource);
    
    $_SESSION['access_token'] = $client->getAccessToken();
}
