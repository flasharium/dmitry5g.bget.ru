<?php

$id = $_REQUEST['project_id'];
$project = db_get_by_id('projects', $id);
global $success, $error;

if (isset($_REQUEST['delete_phrase_id'])) {
    db_delete_by_id('phrases', $_REQUEST['delete_phrase_id']);
}

if (array_get($_REQUEST, 'purge_phrases', false)) {
    db_delete('phrases', array('project_id' => $id));
    db_delete('key_struct', array('project_id' => $id));
}

if ($raw_phrases = array_get($_REQUEST, 'create_phrases.phrases_text', '')) {
    $filtered = create_unique_phrases($raw_phrases, $id);
    if ($filtered) {
        $success = "Добавлено " . count($filtered) . ' новых ключевых фраз';
    } else {
        $error = 'Не добавлено ни одной фразы';
    }
}

print_result();
?>
  <div class="jumbotron">
    <h1><?= $project['name'] ?></h1>
      <?
      $total = db_count('phrases', array('project_id' => $id));
      $grouped = db_count('phrases', array('project_id' => $id, 'add' => ' and group_id > 0 '));
      $blacklist = db_count('phrases', array('project_id' => $id, 'add' => ' and blacklist = 1 '));
      $groups = count(array_get(get_struct_data($id), 'groups'));
      ?>
    <p>Количество ключей: <?= $total ?></p>
    <p>Ключей в группах: <?= $grouped ?></p>
    <p>В черном списке: <?= $blacklist ?></p>
    <p>Количество групп: <?= $groups ?></p>
    <p>
      <a class="btn btn-primary btn-lg" href="/cluster?view=grouping&project_id=<?= $id ?>" role="button">
        Перейти к группировке <?= $total - $grouped - $blacklist ?> ключей
      </a>
    </p>
  </div>

  <div>
    <form action="" method="post">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">Добавить ключевые слова</h3>
        </div>
        <div class="panel-body">

          <p>Добавлять ключевые слова можно в двух форматах, без частотности:
          <pre>норма холестерина в крови
прививка от столбняка
биохимический анализ крови</pre>
          и с указанием частотности через точку с запятой:
          <pre>норма холестерина в крови;123
прививка от столбняка;456
биохимический анализ крови;789</pre>
          Добавлять ключи лучше небольшими пачками по 1000-1500 штук (нормальной массовой загрузки из файла пока нет).
          </p>

          <div class="form-group">
            <textarea class="form-control" rows="5" name="create_phrases[phrases_text]"></textarea>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-default">Добавить</button>
            <a class="btn btn-danger" role="button" href="/cluster/?view=project&project_id=<?=$id?>&purge_phrases=1">
              <span class="glyphicon glyphicon-alert"></span>
              Удалить все ключи и группы
            </a>
          </div>

        </div>
      </div>
    </form>
  </div>

<?
