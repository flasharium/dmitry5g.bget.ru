<?php

$error = '';
$success = '';

if (!$task = db_get_by_id('cm_tasks', $_REQUEST['task_id'])) {
    $error = 'Задание не найдено!';
}

if (isset($_REQUEST['edit_task'])) {
    $edited_task = $task;
    $edited_task['result_url'] = $_REQUEST['edit_task']['result_url'];
    $edited_task['project_id'] = $_REQUEST['edit_task']['project_id'];
//    $edited_task['title'] = $_REQUEST['edit_task']['title'];

    if (isset($_REQUEST['review'])) {
        if ($edited_task['result_url']) {
            $edited_task['status'] = STATUS_REVIEW;
        } else {
            $error = 'Для проверки нужно заполнить поле "Ссылка на статью"!';
        }
    }
    if (isset($_REQUEST['remake'])) {
        $edited_task['status'] = STATUS_REMAKE;
    }
    if (isset($_REQUEST['back_to_new'])) {
        $edited_task['status'] = STATUS_NEW;
    }
    db_update('cm_tasks', $edited_task);
    $task = $edited_task;

    $success = 'Задание сохранено!';
}

if (isset($_REQUEST['comment'])) {
    $comment = $_REQUEST['comment'];
    $comment['ctime'] = time();
    $comment['user_id'] = $user['id'];
    $comment['task_id'] = $task['id'];
    db_insert('cm_task_comment', $comment);
}

function comments() {
    global $task;
    return db_list('cm_task_comment', array('task_id' => $task['id']));
}

$users = to_flat_array(db_list('users'), 'id', 'name');

?>


<div class="container">
    <div class="rov">
        <div class="col-md-6" style="border-right: 1px solid #ccc;">
            <form action="" method="post">
                <h2>Задание #<?=$task['id']?> <?=print_status($task['status'])?></h2>

                <?=print_result()?>

                <div class="form-group">
                    <label for="titleField">Название</label>
                    <input disabled type="text" value="<?=$task['title']?>" class="form-control" name="edit_task[title]" id="titleField">
                </div>

                <div class="form-group">
                    <label for="disabledInput">Задание в TZ</label><br/>
                    <a target="_blank" href="<?= tz_url($task['tz_id']) ?>"><?= tz_url($task['tz_id']) ?></a>
                </div>
                <div class="form-group">
                    <label for="resultUrlField">Ссылка на статью</label>
                    <input type="text" value="<?=$task['result_url']?>" class="form-control" name="edit_task[result_url]" id="resultUrlField">
                </div>

                <div class="form-group">
                    <label for="projectId">Проект</label>
                    <select class="form-control" id="projectId" name="edit_task[project_id]">
                        <? foreach(projects() as $project) { ?>
                            <option
                                value="<?=$project['id']?>"
                                <?=($project['id'] == $task['project_id'] ? 'selected' : '')?>
                            >
                                <?=$project['name']?>
                            </option>
                        <? } ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-floppy-disk"></span>
                    Сохранить
                </button>

                <? if (in_array($task['status'], array(STATUS_REMAKE, STATUS_NEW)) ) { ?>
                    <button type="submit" name="review" class="btn btn-success">
                        <span class="glyphicon glyphicon-ok"></span>
                        Готово к проверке
                    </button>
                <? } ?>

                <? if (in_array($task['status'], array(STATUS_REVIEW)) ) { ?>
                    <button type="submit" name="back_to_new" class="btn btn-warning">
                        <span class="glyphicon glyphicon-ok"></span>
                        Вернуть в работу
                    </button>
                <? } ?>

                <? if (is_admin() && !in_array($task['status'], array(STATUS_REMAKE, STATUS_NEW))) { ?>
                    <button type="submit" name="remake" class="btn btn-danger">
                        <span class="glyphicon glyphicon-exclamation-sign"></span>
                        Вернуть на доработку
                    </button>
                <? } ?>

            </form>
        </div>

        <div class="col-md-6">

            <h3>Комментарии</h3>

            <div>

                <? foreach (comments() as $comment) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong><?=$users[$comment['user_id']]?></strong>
                            <i><?=date("Y-m-d H:i:s", $comment['ctime'])?></i>
                        </div>
                        <div class="panel-body">
                            <?= $comment['content'] ?>
                        </div>
                    </div>
                <? } ?>

            </div>

            <hr/>

            <form action="" method="post">
                <h4>Добавить комментарий</h4>
                <div class="form-group">
                    <textarea class="form-control" rows="3" name="comment[content]"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-default">Отправить</button>
                </div>
            </form>

        </div>

    </div>
</div>
