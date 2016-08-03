<?php

?>
<!doctype html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script type="text/javascript">
        var token = /access_token=([-0-9a-zA-Z_]+)/.exec(document.location.hash)[1];
        window.location.replace('http://dmitry5g.bget.ru/index.php?yandex_token=' + token);
    </script>
</head>
<body>

</body>
</html>
