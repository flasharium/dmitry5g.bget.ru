<?php
session_start();
ini_set('display_errors', true);
error_reporting(E_ALL);

set_include_path(get_include_path() . PATH_SEPARATOR . 'libs/google-api-php-client/src');

require_once dirname(__FILE__) . '/libs/google-api-php-client/src/Google/autoload.php';

$client = new Google_Client();
$client->setClientId("783339917195-31t7vm567pgvrat30lt0p72uf4lmkdcq.apps.googleusercontent.com");
$client->setClientSecret("67DeEScrD5RPhJsJDXyhoB09");
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST']);
$client->addScope(Google_Service_AdSense::ADSENSE_READONLY);
$client->setRedirectUri('http://dmitry5g.bget.ru/');

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
//    var_dump($client->getAccessToken());
    $redirect = 'http://dmitry5g.bget.ru/';
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
}
else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    $adsenseService = new Google_Service_AdSense($client);
    $resource = $adsenseService->reports->generate("today-1d", "today");
    var_dump($resource->getTotals());

    $_SESSION['access_token'] = $client->getAccessToken();
}

if (isset($authUrl)) {
    echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
}

echo "end";
