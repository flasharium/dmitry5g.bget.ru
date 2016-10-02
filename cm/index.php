<?php
//session_set_cookie_params(86400*365);
session_start();

require_once "../libs/proj/inc.php";
require_once "cm_inc.php";

if (!isset($_SESSION['user_id'])) {
    return redirect("/cm/auth.php");
}

$user = db_get_by_id('users', $_SESSION['user_id']);

insert_header('CM');


$views = array(
    'manage_users' =>  array('view' => 'manage_users',  'admin' => 1, 'parent_view' => '',            'title' => 'Пользователи'),
    'add_tasks' =>     array('view' => 'add_tasks',     'admin' => 1, 'parent_view' => '',            'title' => 'Добавить задания'),
    'task_list' =>     array('view' => 'task_list',     'admin' => 0, 'parent_view' => '',            'title' => 'Задания'),
    'task' =>          array('view' => 'task',          'admin' => 0, 'parent_view' => 'task_list',   'title' => 'Просмотр задания'),
    'report_list' =>   array('view' => 'report_list',   'admin' => 0, 'parent_view' => '',            'title' => 'Отчеты'),
    'report' =>        array('view' => 'report',        'admin' => 0, 'parent_view' => 'report_list', 'title' => 'Просмотр отчета'),
);

$view = isset($_REQUEST['view']) ? $views[$_REQUEST['view']]['view'] : 'task_list';
$active_section = $views[$view]['parent_view'] ? $views[$view]['parent_view'] : $view;
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/cm">CM</a>
            </div>

            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <? foreach ($views as $item_view) {
                        if (!$item_view['parent_view'] && (!$item_view['admin'] || ($item_view['admin'] && is_admin()))) {
                            $class = $item_view['view'] == $active_section ? "active" : '';
                            echo "<li class='$class'><a href='/cm?view=$item_view[view]'>$item_view[title]</a></li>";

                        }
                    }?>
                </ul>
                <ul class="nav navbar-nav">
                    <li><a class='' href='/cm/auth.php?logout=1'>Выйти</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
    <br/><br/><br/>
<?

$breadcrumbs = array('/cm' => 'CM');
if ($parent = $views[$view]['parent_view']) {
    $breadcrumbs['/cm?view=' . $parent] = $views[$parent]['title'];
}
$breadcrumbs[$_SERVER['REQUEST_URI']] = $views[$view]['title'];

?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <?
                $end_url = end(array_keys($breadcrumbs));
                foreach ($breadcrumbs as $url => $title) {
                    if ($url == $end_url) {
                        ?><li class="active"><?=$title?></li><?
                    } else {
                        ?><li><a href="<?=$url?>"><?=$title?></a></li><?
                    }
                }
                ?>
            </ol>
        </div>
    </div>
</div>
<?

require_once "views/{$view}.php";

insert_footer();
