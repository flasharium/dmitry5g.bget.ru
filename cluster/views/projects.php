<?php


if ($projects_for_remove = array_get($_REQUEST, 'delete_projects')) {
    db_delete_by_id('projects', $projects_for_remove);
}

if ($new_project = array_get($_REQUEST, 'create_project')) {
    db_insert('projects', $new_project);
}

$projects = db_list('projects');

?>
  <div class="row">
    <div class="col-md-12">

      <h2>Проекты</h2>

      <div style="display: flex; justify-content: flex-end;">
        <button class="btn btn-primary" href="#" role="button" data-toggle="modal" data-target="#creteProjectModal">
          <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
          Добавить
        </button>
      </div>


      <form method="post">
      <table class="table table-hover">
        <thead>
        <tr>
          <th>#</th>
          <th>Название</th>
          <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($projects as $project) { ?>
          <tr>
            <th scope="row" width="1%"><?= $project['id'] ?></th>
            <td>
              <a href="<?= project_url($project['id']) ?>" style="display: block"><?= $project['name'] ?></a>
            </td>
            <td width="1%" nowrap>
              <button class="btn btn-danger btn-xs" role="button" name="delete_projects[]" value="<?= $project['id'] ?>">
                <span class="glyphicon glyphicon-remove"></span>
                Удалить
              </button>
            </td>
          </tr>
        <? } ?>
        </tbody>
      </table>
      </form>
    </div>
  </div>
<?

create_project_modal_window();
