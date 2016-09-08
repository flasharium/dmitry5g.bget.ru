<?php

function task_table_view($filter, $projects, $users) {
    ?>
    <table class="table table-hover">
        <thead>
        <tr>
            <th width="1%">#</th>
            <th width="1%"></th>
            <th width="1%">–</th>
            <th>Статья</th>
            <th width="1%">TZ</th>
            <th width="1%">Проект</th>
            <th width="1%">КМ</th>
            <th width="1%">Отчет</th>
            <th width="1%">Статус</th>
        </tr>
        </thead>
        <tbody>
        <? foreach (tasks($filter) as $task) {?>
            <tr>
                <td><?=$task['id']?></td>
                <th><input name="mass_changing[task_ids][]" type="checkbox" class="task-checkbox" value="<?=$task['id']?>"/></th>
                <td width="100%"><a href="<?= task_url($task['id']) ?>"><?=($task['title']?$task['title']:'Открыть')?></a></td>
                <td width="100%">
                    <? if ($task['result_url']) {
                        $parts = explode('/', $task['result_url']);
                        $last = end($parts);
                        ?>
                        <a target="_blank" href="<?= $task['result_url'] ?>"><?=$last?></a>
                    <? } else {?>
                        –
                    <?}?>
                </td>
                <td><a target="_blank" href="<?= tz_url($task['tz_id']) ?>"><?= $task['tz_id'] ?></a></td>
                <td><?=$projects[$task['project_id']]?></td>
                <td nowrap><?=$task['user_id'] ? $users[$task['user_id']] : '–'?></td>
                <td><?=$task['report_id'] ?
                        "<a href='".report_url($task['report_id'])."'>Отчет&nbsp;#".$task['report_id']."</a>"
                        : '-'?></td>
                <td><?=print_status($task['status'])?></td>
            </tr>
        <?}?>
        </tbody>
    </table>
<?
}
