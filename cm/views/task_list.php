<?

// Templates
require_once "components/tasks_filter.php";
require_once "components/create_task_modal.php";
require_once "components/task_selector.php";
require_once "components/task_table.php";


$projects = to_flat_array(db_list('projects'), 'id', 'name');
$users = to_flat_array(db_list('users'), 'id', 'name');
$error = '';

if (isset($_REQUEST['new_task'])) {
    $task = $_REQUEST['new_task'];
    $task['tz_id'] = int($task['tz_url']);

    if (!$task['tz_id'] || !$task['title']) {
        $error = 'В задании должны быть указаны Название и Ссылка на TZ';
    } else if (db_get('cm_tasks', array('tz_id' => $task['tz_id']))) {
        $error = 'Задание TZ-' . $task['tz_id'] . ' уже существует!';
    } else {
        db_insert('cm_tasks', $task);
        redirect('/cm');
    }
}

process_mass_changing();

$filter = array('add' => ' and status IN (' . implode(',', array(STATUS_NEW, STATUS_REMAKE)) . ') ' );

if (isset($_REQUEST['filter'])) {
    $filter_project = isset($_REQUEST['filter']['project_id']) ? $_REQUEST['filter']['project_id'] : '';
    $filter_status = isset($_REQUEST['filter']['status']) ? $_REQUEST['filter']['status'] : '';
    if (!isset($_REQUEST['filter']['clear']) && ($filter_project || $filter_status)) {
        if ($filter_project) {
            $filter['project_id'] = $filter_project;
        }
        if ($filter_status) {
            $filter['status'] = $filter_status;
            unset($filter['add']);
        }
    }
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?=print_result()?>

            <div style="display: flex; justify-content: space-between;">
                <h2>Задания</h2>
                <? if (is_admin()) {?>
                    <?=create_task_button()?>
                <?}?>
            </div>

            <?=create_task_filter_view()?>

            <? if (count(tasks($filter))) { ?>
                <form action="" method="post" class="form-inline">
                    <div class="panel panel-default">

                        <div class="panel-body">
                            <?=create_task_selector()?>
                        </div>

                        <?=task_table_view($filter, $projects, $users)?>

                        <div class="panel-footer">
                            <?=create_task_selector()?>
                        </div>

                    </div>
                </form>
            <? } else { ?>
                <div class="jumbotron">
                    <h1>Список пуст</h1>
                    <p>Измените настройки фильтра для просмотра заданий</p>
                    <p><a class="btn btn-primary btn-lg" href="/cm?view=task_list" role="button">Новые задания</a></p>
                </div>
            <? } ?>
        </div>
    </div>
</div>

<?=create_task_modal_window()?>
