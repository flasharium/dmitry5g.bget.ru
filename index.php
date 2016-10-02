<?php
//session_set_cookie_params(86400*365);
//session_start();

require_once "libs/proj/inc.php";
require_once "cm/cm_inc.php";

if (!isset($_SESSION['user_id'])) {
    return redirect("/cm/auth.php?source=dash");
} else {
    return redirect("/dash");
}
