<?php
//session_set_cookie_params(86400*365);
session_start();

require_once "../libs/proj/inc.php";
require_once "../inc/common.inc.php";

if (!isset($_SESSION['user_id'])) {
    return redirect("/cm/auth.php?source=cluster");
}

function project_url($id)
{
    return "/cluster?view=project&project_id=$id";
}

function create_project_modal_window()
{
    ?>
  <!-- Modal -->
  <div class="modal fade" id="creteProjectModal" tabindex="-1" role="dialog" aria-labelledby="creteProjectModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="creteProjectModal">Добавить проект</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="createProjectInput">Название</label>
              <input type="text" name="create_project[name]" class="form-control" id="createProjectInput" />
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
