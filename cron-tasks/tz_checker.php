<?php

$root = dirname(__FILE__);
require_once "$root/../libs/proj/inc.php";
require_once "$root/../cm/cm_inc.php";
require_once "$root/../cm/views/components/binet-parser.php";

$tasks = db_list("cm_tasks", array('last_tz_check' => 'update_waiting', 'add' => ' and result_url <> "" '));

foreach ($tasks as $task) {
    $task = db_get_by_id('cm_tasks', $task['id']);
    if ($task['last_tz_check'] != 'update_waiting') continue;
    if (check_task_in_tz($task)) {
        echo "success checking task $task[id]";
    } else {
        echo "something wrong with task $task[id]";
    }
}

