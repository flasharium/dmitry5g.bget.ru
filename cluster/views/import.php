<?php

require_once "../libs/proj/inc.php";

if (isset($_POST['import_keys'])) {

}

?>
<!doctype html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<form action="/cluster/views/import.php" method="post">
    <textarea name="import_keys" id="" cols="30" rows="10"></textarea>
    <button type="submit">Send</button>
</form>

</body>
</html>
