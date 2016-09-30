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
    return db_list('cm_task_comment', array('task_id' => $task['id']), "*", 'order by id desc');
}

if (isset($_REQUEST['check_in_tz']) && is_admin()) {
    $task['last_tz_check'] = 'update_waiting';
    db_update('cm_tasks', $task);
    $success .= "\nЗадание отправлено на проверку в TZ";
}

if (isset($_REQUEST['approve_task']) && is_admin()) {
    $task['status'] = STATUS_COMPLETE;
    db_update('cm_tasks', $task);
    $success .= "\nЗадание одобрено";
}

$users = to_flat_array(db_list('users'), 'id', 'name');

?>


<div class="container">
    <div class="rov">
        <div class="col-md-6">
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
                    <label for="resultUrlField"><a href="<?=$task['result_url']?>" target="_blank">Ссылка на статью</a></label>
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

                <div class="form-group">
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

                </div>


                <div class="form-group">
                    <? if (is_admin()) { ?>
                        <button type="submit" name="approve_task" class="btn btn-success">
                            <span class="glyphicon glyphicon-thumbs-up"></span>
                            Одобрить
                        </button>
                    <? } ?>

                    <? if (is_admin() && !in_array($task['status'], array(STATUS_REMAKE, STATUS_NEW))) { ?>
                        <button type="submit" name="remake" class="btn btn-danger">
                            <span class="glyphicon glyphicon-exclamation-sign"></span>
                            Вернуть на доработку
                        </button>
                    <? } ?>

                    <? if (is_admin()) { ?>
                        <button type="submit" name="check_in_tz" class="btn btn-info">
                            <span class="glyphicon glyphicon-eye-open"></span>
                            Проверить в TZ
                        </button>
                    <? } ?>
                </div>

            </form>

            <?if ($task['last_tz_check'] && $task['last_tz_check'] != 'update_waiting') {?>

                <div class="panel panel-info" style="margin: 20px 0;">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Результаты последней проверки в TZ
                            <?=($task['last_tz_check_time'] ? date('(Y-m-d H:i:s)', $task['last_tz_check_time']) : '')?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?=($task['last_tz_check'] == 'success' ? 'Текст соответствует ТЗ' : $task['last_tz_check'])?>
                    </div>
                </div>

            <?}?>
        </div>

        <div class="col-md-6">



            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Комментарии
                    </h3>
                </div>
                <div class="panel-body">
                    <? foreach (comments() as $comment) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading comment_header">
                                <strong><?=$users[$comment['user_id']]?></strong>
                                <i><?=date("Y-m-d H:i:s", $comment['ctime'])?></i>
                            </div>
                            <div class="panel-body comment_content">
                                <?= nl2br($comment['content']) ?>
                            </div>
                        </div>
                    <? } ?>

                    <form action="" method="post" style="margin-top: 40px;">
                        <h4>Добавить комментарий</h4>
                        <div class="form-group">
                            <textarea class="form-control autoresizeTextarea" rows="3" name="comment[content]" ></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Отправить</button>
                        </div>
                    </form>

                </div>
            </div>



        </div>

    </div>
</div>
