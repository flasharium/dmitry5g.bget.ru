<?php
session_start();
require_once "../libs/proj/inc.php";

$error = '';

if (isset($_REQUEST['logout'])) {
    $_SESSION['user_id'] = null;
    redirect('/cm');
}

if (isset($_REQUEST['login'])) {
    $error = 'Неправильные имя пользователя или пароль';
    do {
        if (!$username = $_REQUEST['login']['username']) break;
        if (!$password = $_REQUEST['login']['password']) break;
        $username = trim($username);
        $hash = md5(trim($password));
        $crit = array('nick' => $username, 'pass' => $hash);

//        if (isset($_REQUEST['create'])) {
//            db_insert('users', $crit);
//        }

        if(!$user = db_get('users', $crit)) break;

        $_SESSION['user_id'] = $user['id'];
        redirect('/cm');
    } while (0);
}

insert_header();


?>

    <div class="container">
        <div class="row">
            <div class="Absolute-Center is-Responsive">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <? if ($error) { ?>
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            <?=$error?>
                        </div>
                    <? } ?>
                    <form action="/cm/auth.php" id="loginForm" method="post">
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input class="form-control" type="text" name='login[username]' placeholder="Логин"/>
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input class="form-control" type="password" name='login[password]' placeholder="Пароль"/>
                        </div>
                        <div class="form-group">
<!--                            <button name="create" type="submit" class="btn btn-def btn-block">Create</button>-->
                            <button type="submit" class="btn btn-def btn-block">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<?

insert_footer();
