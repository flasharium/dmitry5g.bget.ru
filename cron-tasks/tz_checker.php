<?php

$root = dirname(__FILE__);
require_once "$root/../libs/proj/inc.php";
require_once "$root/../cm/cm_inc.php";
require_once "$root/../cm/views/components/binet-parser.php";

$task = db_get("cm_tasks", array('last_tz_check' => 'update_waiting'));
if (check_task_in_tz($task)) {
    echo "success checking task $task[id]";
} else {
    echo "something wrong with task $task[id]";
}


