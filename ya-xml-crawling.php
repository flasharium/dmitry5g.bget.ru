<?php

$user = "di-work";
$key = "03.42778088:bfc949705e2051449afb6e1f57f3f153";

$api_search_url = "https://yandex.ru/search/xml?" .
    "user={USER}" .
    "&key={KEY}" .
    "&query={QUERY}" .
    "&lr=213" .
    "&l10n=ru" .
    "&sortby=rlv" .
    "&filter=strict" .
    "&maxpassages=5" .
    "&groupby=attr%3D%22%22.mode%3Dflat.groups-on-page%3D10.docs-in-group%3D1";

$api_limits_url = "https://yandex.ru/search/xml?" .
    "action=limits-info" .
    "&user={USER}" .
    "&key={KEY}";

function ya_xml_search($keyword) {
    global $user, $key, $api_search_url;
    $url = str_replace(
        array('{USER}', '{KEY}', '{QUERY'),
        array($user, $key, urlencode($keyword)),
        $api_search_url
    );

//    $result = file_get_contents($url);
    $result = file_get_contents("/Users/dmitrijivasenko/projects/dmitry5g.bget.ru/test.xml");
    return $result;
}


function ya_xml_find_position($keyword) {
    $xml_string = ya_xml_search($keyword);
    $xml = simplexml_load_string($xml_string);
    $json = json_encode($xml);
    $array = json_decode($json,TRUE);
    $results = array_get($array, 'response.results.grouping.group');

    foreach ($results as $result) {
        $domain = trim(array_get($result, 'doc.domain'));
        $url = trim(array_get($result, 'doc.url'));

        echo "$domain\t\t\t|\t\t\t$url\n";
    }
}

ya_xml_find_position("", "");
