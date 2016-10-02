<?php

session_start();
require_once "../libs/proj/inc.php";

if (!isset($_SESSION['user_id'])) {
    return redirect("/cm/auth.php");
}
