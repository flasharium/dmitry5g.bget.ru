<?php

function process_mass_changing()
{
    global $success;

    if (isset($_REQUEST['mass_changing'])) {
        $task_ids = array_get($_REQUEST, 'mass_changing.task_ids');
        if (count($task_ids)) {
            $changes = array();

            if ($new_user_id = array_get($_REQUEST, 'change_manager.user_id')) {
                $changes['user_id'] = $new_user_id;
                $success = 'Задания (' . count($task_ids) . ') назначену КМу ' . array_get(users(), $new_user_id . '.name') . '<br/>';
            }

            if ($new_status = array_get($_REQUEST, 'change_task_status.status')) {
                $changes['status'] = $new_status;
                $success = 'Задания (' . count($task_ids) . ') получили статус ' . array_get(task_statuses(), "$new_status.title") . '<br/>';
            }

            if ($new_report_ids = array_get($_REQUEST, 'add_to_report.report_id')) {
                foreach ($new_report_ids as $new_report_id) {
                    if ($new_report_id) {
                        $changes['report_id'] = $new_report_id;
                        $success = 'Задания (' . count($task_ids) . ') добавлены к отчету #' . $new_report_id . '<br/>';
                        break;
                    }
                }
            }

            if ($unbind = array_get($_REQUEST, 'unbind_from_report', false)) {
                $changes['report_id'] = 0;
            }

            if ($changes) {
                $changes['id'] = $task_ids;
                db_update('cm_tasks', $changes);
            }
        }
    }
}

function create_task_selector()
{
    ?>
  <div>
    <div class="form-group checkbox"
    <label>
      <input type="checkbox"
             onchange="var a='checked',c=$(this).prop(a);$('.task-checkbox').each(function(){$(this).prop(a,c)})"/>
      Выделить все
    </label>
  </div>

  <span style="border-left: 1px solid #b3b3b3; padding-right: 10px; margin-left: 8px;"></span>

  <div class="form-group checkbox">
    Выберите отчет:
    <label>
      <select name="add_to_report[report_id][]">
        <option value="">–</option>
          <?
          $reports = is_admin() ? reports() : reports(array('status' => REPORT_STATUS_NEW));
          foreach ($reports as $report) { ?>
            <option value="<?= $report['id'] ?>">Отчет #<?= $report['id'] ?></option>
          <? } ?>
      </select>
    </label>
  </div>

  <div class="form-group">
    <button type="submit" class="btn btn-primary btn-xs">Добавить к отчету</button>
  </div>

  <? if (is_admin()) { ?>

    <div class="form-group">
      <button type="submit" name="unbind_from_report" value="1" class="btn btn-primary btn-xs">Отвязать от отчета</button>
    </div>

    <span style="border-left: 1px solid #b3b3b3; padding-right: 10px; margin-left: 8px;"></span>

    <div class="form-group checkbox">
      КМ:
      <label>
        <select name="change_manager[user_id]">
          <option value="">–</option>
            <? foreach (to_flat_array(users(), 'id', 'name') as $id => $name) { ?>
              <option value="<?= $id ?>"><?= $name ?></option>
            <? } ?>
        </select>
      </label>
      <button type="submit" class="btn btn-primary btn-xs">Назначить</button>
    </div>

    <span style="border-left: 1px solid #b3b3b3; padding-right: 10px; margin-left: 8px;"></span>

    <div class="form-group checkbox">
      Статус:
      <label>
        <select name="change_task_status[status]">
          <option value="">–</option>
            <? foreach (to_flat_array(task_statuses(), 'id', 'title') as $id => $name) { ?>
              <option value="<?= $id ?>"><?= $name ?></option>
            <? } ?>
        </select>
      </label>
      <button type="submit" class="btn btn-primary btn-xs">Изменить</button>
    </div>
  <? } ?>

  </div>
<?
}
