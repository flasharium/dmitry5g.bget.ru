<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once "../libs/proj/inc.php";
require_once "dash_inc.php";
require_once '../libs/google-api-php-client/src/Google/autoload.php';
require_once '../libs/webmaster_api.class.php';

$authUrl = $ya_token = '';

$client = new Google_Client();
$client->setClientId("783339917195-31t7vm567pgvrat30lt0p72uf4lmkdcq.apps.googleusercontent.com");
$client->setClientSecret("67DeEScrD5RPhJsJDXyhoB09");
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST']);
$client->addScope(Google_Service_AdSense::ADSENSE_READONLY);
$client->setRedirectUri('http://'.conf('hostname').'/dash');

if (isset($_REQUEST['ga_logout'])) {
    unset($_SESSION['access_token']);
}

if (isset($_REQUEST['ym_logout'])) {
    unset($_SESSION['ya_token']);
}


if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect = 'http://'.conf('hostname').'/dash';
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {

    if ($client->isAccessTokenExpired()) {
        $authUrl = $client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        die;
    }
    $_SESSION['access_token'] = $client->getAccessToken();
}

if (isset($_GET['yandex_token'])) {
    $ya_token = $_SESSION['ya_token'] = $_GET['yandex_token'];
}

if (isset($_REQUEST['ym_login'])) {
    $ya_client_id = '5178be0b493c4db895e1766244d2e9b4';
    header("Location: https://oauth.yandex.ru/authorize?response_type=token&client_id=$ya_client_id");
    die;
}
if (isset($_SESSION['ya_token'])) {
    $ya_token = $_SESSION['ya_token'];
}

$ya_connect = !!$ya_token;
$ga_connect = !$authUrl;


/* Page content */


insert_header();
insert_dash_navbar($authUrl);

if ($ga_connect) {
    $adsenseService = new Google_Service_AdSense($client);
    $resource = $adsenseService->accounts_reports->generate("pub-2243971898865156", "today-7d", "today", array(
        'dimension'            => 'DATE',
        'metric'               => 'EARNINGS',
        'currency'             => 'RUB',
        'useTimezoneReporting' => true,
    ));
    $total = $resource->getTotals();
    $avg = $resource->getAverages();
}

if ($ya_connect) {
    $counters_json = file_get_contents("https://api-metrika.yandex.ru/management/v1/counters?status=Active&oauth_token=" . $ya_token);
    $counters_data = json_decode($counters_json);
    $counters = $counters_data->counters;
    $owners = array();
    $ids = array();
    $site_names = array();

    foreach ($counters as $counter) {
        array_push($site_names, $counter->site);
        array_push($owners, $counter->owner_login);
        array_push($ids, $counter->id);
    }

    $owners = array_unique($owners);
    $stat = json_decode(file_get_contents("https://api-metrika.yandex.ru/stat/v1/data?" .
        "direct_client_logins=" . implode(',', $owners) .
        "&ids=" . implode(',', $ids) .
        "&metrics=ym:s:users" .
        "&oauth_token=" . $ya_token
    ), 1);

    $unique_users = $stat['data'][0]['metrics'][0];
    $searchable_pages_count = 0;

    $wmApi = new webmasterApi($ya_token);
    foreach ($wmApi->getHosts()->hosts as $host) {
        $host_summary = $wmApi->getHostSummary($host->host_id);
        $searchable_pages_count += $host_summary->searchable_pages_count;
    }
}

?>

<? if (!conf('alpha')) {?>
  <div class="row">
    <div class="col-md-12">
        <? require_once 'views/alpha.php'; ?>
    </div>
  </div>
<? die; } ?>


<? if ($ga_connect && $ya_connect) {?>
  <div class="row">
    <div class="col-md-12">
        <? require_once 'views/summary.php'; ?>
    </div>
  </div>
<? } ?>

  <div class="row">
    <div class="col-md-12">
        <? require_once 'views/actions.php'; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
        <? insert_dash_connect_block($authUrl, $ga_connect, $ya_connect); ?>
    </div>
  </div>


  <div class="row">
    <div class="col-md-6">
        <? if ($ga_connect) {
            require_once 'views/ga_info.php';
        } ?>
    </div>

    <div class="col-md-6">
        <? if ($ya_connect) {
            require_once 'views/ya_info.php';
        } ?>
    </div>

  </div>
<?


