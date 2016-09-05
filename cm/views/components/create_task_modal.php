<?

function create_task_button() {
    ?>
    <div style="display: flex; justify-content: flex-end;">
        <button class="btn btn-primary" href="#" role="button" data-toggle="modal" data-target="#myModal">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            Добавить
        </button>
    </div>
<?

}


function create_task_modal_window() {
?>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Добавить задание</h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="exampleInputPassword2">Ссылка на TZ</label>
                            <input type="text" name="new_task[tz_url]" class="form-control" id="exampleInputPassword2" placeholder="http://tz.binet.pro/keys/view/index?id=5932402">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword2">Ссылка на статью (необязательно)</label>
                            <input type="text" name="new_task[result_url]" class="form-control" id="exampleInputPassword2" placeholder="http://medistoriya.ru/ginekologiya/sxema-lecheniya-ureaplazmoza.html">
                        </div>
                        <div class="form-group">
                            <label for="projectId">Проект</label>
                            <select class="form-control" id="projectId" name="new_task[project_id]">
                                <? foreach(projects() as $project) { ?>
                                    <option value="<?=$project['id']?>"><?=$project['name']?></option>
                                <? } ?>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?
}
