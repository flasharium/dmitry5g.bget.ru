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

    if($client->isAccessTokenExpired()) {
        $authUrl = $client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        die;
    }
    $_SESSION['access_token'] = $client->getAccessToken();
}

if (isset($_GET['yandex_token'])) {
    $ya_token = $_SESSION['ya_token'] = $_GET['yandex_token'];
}

if (!isset($_SESSION['ya_token'])) {
    header('Location: https://oauth.yandex.ru/authorize?response_type=token&client_id=5178be0b493c4db895e1766244d2e9b4');
    die;
} else {
    $ya_token = $_SESSION['ya_token'];
}


?>
<!doctype html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script src="https://code.jquery.com/jquery-3.1.0.slim.min.js" integrity="sha256-cRpWjoSOw5KcyIOaZNo4i6fZ9tKPhYYb6i5T9RSVJG8=" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">dmitry5g.bget.ru</a>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><?

if (isset($authUrl)) {
    echo "<a class='' href='" . $authUrl . "'>Login</a>";
} else {
    echo "<a class='' href='?logout'>Logout</a>";
}

?></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<br/><br/><br/>
<?
if ($client->getAccessToken()) {
$adsenseService = new Google_Service_AdSense($client);
$resource = $adsenseService->accounts_reports->generate("pub-2243971898865156", "today-7d", "today", array(
    'dimension'            => 'DATE',
    'metric'               => 'EARNINGS',
    'currency'             => 'RUB',
    'useTimezoneReporting' => true,
));
$total = $resource->getTotals();
$avg = $resource->getAverages();
?>


<div class="container theme-showcase" role="main">
    <div class="page-header">
        <h1>Adsense</h1>
    </div>
    <div class="row">

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Total: <?=$total[1]?>, Avg: <?=$avg[1]?></h3>
                    </div>
                    <div class="panel-body">

                        <table class="table">
                            <tbody>
                            <thead>
                            <tr>
                                <th>date</th>
                                <th>revenue</th>
                            </tr>
                            </thead>
                            <tbody>
<?
    foreach ($resource->getRows() as $value) {
        echo "<tr><td>$value[0]</td><td>$value[1]</td></tr>";
    }
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    </div>
</div>
<?
$counters_json = file_get_contents("https://api-metrika.yandex.ru/management/v1/counters?status=Active&oauth_token=" . $ya_token);
$counters_data = json_decode($counters_json);
$counters = $counters_data->counters;
//var_dump($counters_data);
$owners = array();
$ids = array();

foreach ($counters as $counter) {
    $name = $counter->site;
    array_push($owners, $counter->owner_login);
    array_push($ids, $counter->id);
//    echo "<div class=\"alert alert-success\" role=\"alert\">$counter->id - $name</div>";
}
$owners = array_unique($owners);
$stat = json_decode(file_get_contents("https://api-metrika.yandex.ru/stat/v1/data?" .
    "direct_client_logins=" . implode(',', $owners) .
    "&ids=" . implode(',', $ids) .
    "&metrics=ym:s:users" .
    "&oauth_token=" . $ya_token
), 1);
//var_dump($stat);
$unics = $stat['data'][0]['metrics'][0];
$cpi = $total[1]/$unics;
?>
    <span class="label label-primary">Unics: <?=$unics?></span>

    <div class="alert alert-success" role="alert">
        <strong>Доход на посетителя:</strong> <?printf("%.3f", $cpi)?>
    </div>
</body>
</html>
