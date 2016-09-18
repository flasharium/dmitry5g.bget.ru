<?

require_once "components/task_table.php";
require_once "components/task_selector.php";

$success = $error = '';

if (!$report = db_get_by_id('cm_reports', $_REQUEST['report_id'])) {
    $error = 'Отчет не найден!';
}

$projects = to_flat_array(db_list('projects'), 'id', 'name');
$users = to_flat_array(db_list('users'), 'id', 'name');
$filter = array('report_id' => $report['id']);

function set_status($status) {
    global $report;
    db_update('cm_reports', array( 'id' => $report['id'], 'status' => $status));
    $report['status'] = $status;
}

if (isset($_REQUEST['to_review'])) {
    do {

        $tasks = db_list('cm_tasks', array( 'report_id' => $report['id']));
        if (count($tasks) < 10) {
            $error = 'В отчете должно быть минимум 10 заданий!';
            break;
        }

        foreach ($tasks as $task) {
            if ($task['status'] != STATUS_REVIEW) {
                $error = 'Все задания в отчете должны быть в статусе "Готово к проверке"!';
                break 2;
            }
        }

        db_update('cm_reports', array(
            'id' => $report['id'],
            'status' => REPORT_STATUS_REVIEW,
        ));
    } while(0);
}

if (isset($_REQUEST['mark_complete']) && is_admin()) {
    set_status(REPORT_STATUS_COMPLETE);
}

if (isset($_REQUEST['to_remake']) && is_admin()) {
    set_status(REPORT_STATUS_REMAKE);
}

if (isset($_REQUEST['check_in_tz']) && is_admin()) {
    db_update('cm_tasks', array('last_tz_check' => 'update_waiting'), array('report_id' => $report['id']));
    $success .= "Задания отправлены на проверку в TZ";
}

process_mass_changing();

?>


<div class="container">
    <div class="row">

        <div class="col-md-12">
            <h2>Отчет #<?=$report['id']?> <?=print_report_status($report['status'])?></h2>
            <?=print_result()?>

            <? if (count(tasks($filter)) == 0) { ?>
                <div class="jumbotron">
                    <h1>Список пуст</h1>
                    <p>Добавьте статьи к отчету, чтобы они появились в списке</p>
                    <p><a class="btn btn-primary btn-lg" href="/cm?view=task_list&filter[status]=<?=STATUS_REVIEW?>" role="button">Выбрать статьи</a></p>
                </div>
            <? } else { ?>

                <form action="" method="post" style="display:flex;justify-content: flex-end" class="form-inline">
                    <? if (in_array($report['status'], array(REPORT_STATUS_NEW)) ) { ?>
                        <div class="form-group">
                            <button type="submit" name="to_review" class="btn btn-success">
                                <span class="glyphicon glyphicon-ok"></span>
                                Готово к проверке
                            </button>
                        </div>
                    <? } ?>
                    <? if (is_admin() && !in_array($report['status'], array(REPORT_STATUS_COMPLETE)) ) { ?>
                        <div class="form-group">
                            <button type="submit" name="mark_complete" class="btn btn-success">
                                <span class="glyphicon glyphicon-ok"></span>
                                Утвердить отчет
                            </button>
                        </div>
                    <? } ?>
                    <? if (is_admin()) { ?>
                        <div class="form-group">
                            <button type="submit" name="check_in_tz" class="btn btn-info">
                                <span class="glyphicon glyphicon-eye-open"></span>
                                Проверить в TZ
                            </button>
                        </div>
                    <? } ?>
                    <? if (is_admin() && !in_array($report['status'], array(REPORT_STATUS_REMAKE))) { ?>
                        <div class="form-group">
                            <button type="submit" name="to_remake" class="btn btn-danger">
                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                Вернуть на доработки
                            </button>
                        </div>
                    <? } ?>
                </form>

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
            <? } ?>
        </div>

    </div>
</div>
