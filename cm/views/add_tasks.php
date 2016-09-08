<?

$success = $error = '';

if (isset($_REQUEST['create_tasks'])) {
    $content = $_REQUEST['create_tasks']['content'];
    $project_id = $_REQUEST['create_tasks']['project_id'];

    $content = explode("\n", $content);
    $counter = 0;

    foreach ($content as $item) {
        $result_url = $tz_url = '';
        if (strpos($item, "\t")) {
            list($result_url, $tz_url) = explode("\t", $item);
        } else {
            $tz_url = $item;
        }
        $result_url = trim($result_url);
        $tz_url = trim($tz_url);
        $tz_id = int($tz_url);

        if (!$tz_id || db_get('cm_tasks', array('tz_id' => $tz_id))) {
            continue;
        }

        db_insert('cm_tasks', array(
            'tz_id' => $tz_id,
            'result_url' => $result_url,
            'project_id' => $project_id
        ));
        $counter++;
    }
    if ($counter > 0) {
        $success = "Добавлено $counter заданий";
    } else {
        $error = 'Не добавлено ни одного задания!';
    }

}

if (array_get($_REQUEST, 'action') == 'refresh_group_titles') {
    require_once "components/binet-parser.php";
    if ($groups = get_all_groups()) {
        $counter = 0;
        $tasks = db_list('cm_tasks');
        foreach ($tasks as $task) {
            if (isset($groups[$task['tz_id']])) {
                $task['title'] = $groups[$task['tz_id']];
                db_update('cm_tasks', $task);
                $counter++;
            }
        }
        if ($counter) {
            $success .= " Обновлено $counter названий ";
        }

    }
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?=print_result()?>

            <form action="">

                <h4>Добавить задания</h4>
                <div class="form-group">
                    <textarea class="form-control" rows="5" name="create_tasks[content]"></textarea>
                </div>

                <div class="form-group">
                    <label for="projectId">Проект</label>
                    <select class="form-control" id="projectId" name="create_tasks[project_id]">
                        <? foreach(projects() as $project) { ?>
                            <option value="<?=$project['id']?>"><?=$project['name']?></option>
                        <? } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="userId">Контент-менеджер</label>
                    <select class="form-control" id="userId" name="create_tasks[user_id]">
                        <option value="">-</option>
                        <? foreach(users() as $user) { ?>
                            <option value="<?=$user['id']?>"><?=$user['name']?></option>
                        <? } ?>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-default">Отправить</button>
                </div>

            </form>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a href="/cm?view=add_tasks&action=refresh_group_titles" class="btn btn-default" role="button"><span class="glyphicon glyphicon-refresh"></span> Обновить названия</a>
        </div>
    </div>
</div>
