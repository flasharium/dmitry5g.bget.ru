<?php

function get_binet_content($url) {
    static $context;
    $content = file_get_contents($url, false, $context);
    if (!$context || strpos($content, '<a href="#" class="h_login_btn" title="Вход">Вход</a>') !== false) {
        // unauthorized
        // curl '' -H 'Cookie: '
        // -H 'Origin: http://tz.binet.pro'
        // -H 'Accept-Encoding: gzip, deflate'
        // -H 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4'
        // -H 'Upgrade-Insecure-Requests: 1'
        // -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36'
        // -H 'Content-Type: application/x-www-form-urlencoded'
        // -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
        // -H 'Cache-Control: max-age=0'
        // -H 'Referer: http://tz.binet.pro/'
        // -H 'Connection: keep-alive'
        //
        // --data '' --compressed
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => "http://tz.binet.pro/site/login",
            CURLOPT_COOKIE => "login=dmitrygrand%40gmail.com; password=dcc0f2556f861584cc3527cde3b1ab83; PHPSESSID=grj10ugoakagcat94cqrcjtr94; _csrf=eead68a31be934d54f7284257b85203e29e081f9d879921f7de0454b214d6439a%3A2%3A%7Bi%3A0%3Bs%3A5%3A%22_csrf%22%3Bi%3A1%3Bs%3A32%3A%229wsLMykmtyWlFqQVBSHqm6ydTs96JB-x%22%3B%7D",
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array(
                'Origin: http://tz.binet.pro',
                'Accept-Encoding: gzip, deflate',
                'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
                'Upgrade-Insecure-Requests: 1',
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Cache-Control: max-age=0',
                'Referer: http://tz.binet.pro/',
                'Connection: keep-alive',
            ),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => '_csrf=dGIzWGJkSHhNFUAULx0jFQAbZDQkFRkuNjF7KQ9SMRwgEQpuKCZlAA%3D%3D&LoginForm%5Busername%5D=dmitrygrand%40gmail.com&LoginForm%5Bpassword%5D=MKFUMVK1CR',
            CURLOPT_RETURNTRANSFER => 1,
        );

        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);

        preg_match_all('|Set-Cookie: (.*);|U', $res, $content);
        $ResponseCookie = implode(';', $content[1]);

        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: $ResponseCookie\r\n"
            )
        );

        $context = stream_context_create($opts);
    }
    $content = file_get_contents($url, false, $context);

    return $content;
}

function get_all_groups() {
    $file = get_binet_content('http://tz.binet.pro/');
    preg_match_all('/<a href="\/keys\/list\/index\?project_id\=(?<id>\d+)">(?<name>.*?)<\/a>/U', $file, $matches);
    $arr = array_combine($matches['name'], $matches['id']);

    $keys = array();

    foreach ($arr as $name => $id) {
        $content = get_binet_content("http://tz.binet.pro/keys/list/index?project_id=$id");
        $pattern = '/<a href="\/keys\/view\/index\?id=(?<id>\d+)"\s*class="project_url">(?<key>.+?)<\/a>/U';
        preg_match_all($pattern, $content, $matches);

        $keys = array_merge($keys, array_combine($matches['key'], $matches['id']));

        $page = 2;
        while (strpos($content, "/keys/list/index?project_id=$id&amp;page=$page") !== false) {
            $content = get_binet_content("http://tz.binet.pro/keys/list/index?project_id=$id&page=$page");
            preg_match_all($pattern, $content, $matches);
            $keys = array_merge($keys, array_combine($matches['key'], $matches['id']));
            $page++;
        }
    }
    $keys = array_flip($keys);

    return $keys;
}

