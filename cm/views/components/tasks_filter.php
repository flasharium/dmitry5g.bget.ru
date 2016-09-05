<?php

function create_task_filter_view() {
    global $filter, $statuses;
    ?>
    <div class="panel panel-default">
        <div class="panel-body">

            <div style="display: flex; justify-content: space-between;">

                <div>
                    <h4>Количество: <span class="label label-primary"><?=count(tasks($filter))?></span></h4>
                </div>

                <div>
                    <form class="form-inline" action="" method="get">
                        <div class="form-group">
                            <label for="projectId">Проект</label>
                            <select class="form-control" id="projectId" name="filter[project_id]">
                                <option value="">Все</option>
                                <? foreach(projects() as $project) { ?>
                                    <option value="<?= $project['id'] ?>"
                                        <?=($project['id'] == $filter['project_id'] ? 'selected' : '')?>
                                        >
                                        <?=$project['name']?>
                                    </option>
                                <? } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Статус</label>
                            <select class="form-control" id="status" name="filter[status]">
                                <option value="">новые и доработки</option>
                                <?
                                $filter_status = isset($filter['status']) ? $filter['status'] : '';
                                foreach($statuses as $id => $data) { ?>
                                    <option value="<?= $id ?>"
                                        <?=($id == $filter_status ? 'selected' : '')?>
                                        >
                                        <?=$data['title']?>
                                    </option>
                                <? } ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-default">Применить</button>
                        <button type="reset" name="filter[clear]" class="btn btn-default">Сбросить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?
}
