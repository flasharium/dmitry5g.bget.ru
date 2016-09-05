<?

global $user;

if (isset($_REQUEST['create_report'])) {
    db_insert('cm_reports', array(
        'ctime' => time(),
        'user_id' => $user['id'],
        'status' => REPORT_STATUS_NEW,
    ));
}

if ($remove_report_id = array_get($_REQUEST, 'remove_report')) {
    db_update('cm_tasks', array('report_id' => 0), array('report_id' => $remove_report_id));
    db_delete_by_id('cm_reports', $remove_report_id);
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?=print_result()?>

            <h2>Отчеты</h2>

            <div style="display: flex; justify-content: flex-end;">
                <form action="" name="create_report" method="post">
                    <button name="create_report" class="btn btn-primary" type="submit" role="button">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        Создать отчет
                    </button>
                </form>
            </div>

            <? if (count(reports())) { ?>

                <div class="panel panel-default">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Количество статей</th>
                                <th>Дата создания</th>
                                <th>Статус</th>
                                <? if (is_admin()) { ?>
                                    <th>Действия</th>
                                <? } ?>
                            </tr>
                        </thead>
                        <tbody>
                        <? foreach (reports() as $report) {?>
                            <tr>
                                <td width="1%" nowrap><?=$report['id']?></td>
                                <td><a href="<?= report_url($report['id']) ?>">Отчет #<?=$report['id']?></a></td>
                                <td>
                                    <?=db_count('cm_tasks', array('report_id' => $report['id']))?>
                                </td>
                                <td width="1%" nowrap><?=date("Y-m-d H:i:s", $report['ctime'])?></td>
                                <td width="1%" nowrap><?=print_report_status($report['status'])?></td>
                                <? if (is_admin()) { ?>
                                    <th width="1%" nowrap>
                                        <a type="button" class="btn btn-danger btn-xs" href="/cm/?view=report_list&remove_report=<?=$report['id']?>">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                            Удалить
                                        </a>
                                    </th>
                                <? } ?>
                            </tr>
                        <?}?>
                        </tbody>
                    </table>
                </div>
            <? } else { ?>
                <div class="jumbotron">
                    <h1>Список пуст</h1>
                    <p>Создайте отчет, чтобы он появился в списке!</p>
                    <p>
                        <form action="" name="create_report" method="post">
                            <button name="create_report" class="btn btn-primary btn-lg" type="submit" role="button">
                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                Создать отчет
                            </button>
                        </form>
                    </p>
                </div>
            <? } ?>
        </div>
    </div>
</div>
