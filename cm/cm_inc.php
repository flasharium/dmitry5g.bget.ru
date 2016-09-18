<?php


define('STATUS_NEW', 1);
define('STATUS_REVIEW', 2);
define('STATUS_REMAKE', 3);
define('STATUS_COMPLETE', 4);

$statuses = array(
    STATUS_NEW =>    array('id' => STATUS_NEW, 'title' => 'новое', 'class' => 'label-warning'),
    STATUS_REVIEW => array('id' => STATUS_REVIEW, 'title' => 'готово к проверке', 'class' => 'label-success'),
    STATUS_REMAKE => array('id' => STATUS_REMAKE, 'title' => 'доработки', 'class' => 'label-danger'),
    STATUS_COMPLETE => array('id' => STATUS_COMPLETE, 'title' => 'одобрено', 'class' => 'label-info'),
);

define('REPORT_STATUS_NEW', 1);
define('REPORT_STATUS_REVIEW', 2);
define('REPORT_STATUS_REMAKE', 3);
define('REPORT_STATUS_COMPLETE', 4);

$report_statuses = array(
    REPORT_STATUS_NEW => array('id' => REPORT_STATUS_NEW, 'title' => 'новый', 'class' => 'label-warning'),
    REPORT_STATUS_REVIEW => array('id' => REPORT_STATUS_REVIEW, 'title' => 'на проверке', 'class' => 'label-success'),
    REPORT_STATUS_REMAKE => array('id' => REPORT_STATUS_REMAKE, 'title' => 'доработки', 'class' => 'label-danger'),
    REPORT_STATUS_COMPLETE => array('id' => REPORT_STATUS_COMPLETE, 'title' => 'оплачен', 'class' => 'label-info'),
);

function task_statuses() {
    global $statuses;
    return $statuses;
}

function report_statuses() {
    global $report_statuses;
    return $report_statuses;
}

function is_admin() {
    global $user;
    return $user['id'] == 1;
}

function projects() {
    global $user;
    $project_links = db_list('cm_projects2users', array('user_id' => $user['id']));
    $project_ids = to_flat_array($project_links, 'project_id', 'project_id');
    $crit = is_admin() ? null : array('id' => $project_ids);
    return db_list('projects', $crit);
}

function tasks($filter = array()) {
    global $user;
    $crit = is_admin() ? array() : array('user_id' => $user['id']);
    $crit = array_merge($filter, $crit);
    return db_list('cm_tasks', $crit);
}

function reports($filter = array()) {
    global $user;
    $crit = is_admin() ? array() : array('user_id' => $user['id']);
    $crit = array_merge($filter, $crit);
    return db_list('cm_reports', $crit);
}

function users() {
    return db_list('users');
}

function print_result(){
    global $error, $success;
?>

<? if ($error) { ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <?=$error?>
    </div>
<? } ?>

<? if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
        <?=$success?>
    </div>
<? } ?>
<?
}

function clean_content($content) {
    $str = strip_tags($content);
    return preg_replace('#\[[^\]]+\]#', '', $str);
}

function get_clean_article_content($task) {
    if (!$result_url = $task['result_url']) die("result_url is empty!");

    if (!$project = db_get_by_id('projects', $task['project_id'])) die("project not found");

    $project_name = $project['name'];

    $post_id = `cd ~/$project_name/public_html/ && \
                echo "$result_url" | \
                ~/wp eval-file post_ids.php --skip-plugins=rustolat`;

    $cli_out = `cd ~/$project_name/public_html/ && \
                ~/wp post list --fields=url,content --format=csv --post__in=$post_id`;

    if (!$cli_out) die('wrong $cli_out');

    $parsed_out = str_getcsv($cli_out, ',','"');
    $content_with_html = $parsed_out[2];

    if (!$content_with_html) die('wrong $content_with_html');

    $clean_content =  clean_content($content_with_html);

    if (!$clean_content) die(' wrong $clean_content');

    return $clean_content;
}
