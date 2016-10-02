<?php

$id = $_REQUEST['project_id'];
$project = db_get_by_id('projects', $id);

if (isset($_REQUEST['delete_phrase_id'])) {
    db_delete_by_id('phrases', $_REQUEST['delete_phrase_id']);
}

if (isset($_REQUEST['create_phrases'])) {
    $phrases = explode("\n", $_REQUEST['create_phrases']['phrases_text']);

    $existing_phrases = db_list('phrases', array('project_id' => $id));
    $existing_phrases = to_flat_array($existing_phrases, 'id', 'phrase');
    $existing_phrases = array_flip($existing_phrases);
    $filtered = array();
    foreach ($phrases  as $phrase) {
        $value = trim($phrase);
        $freq = 0;
        if (strpos($value, ';') !== false) {
            list($value, $freq) = explode(';', $value);
        }
        if ($value && !isset($existing_phrases[$value])) {
            array_push($filtered, array($id, $value, $freq));
        }
    }
    db_insert_multiple('phrases', array('project_id', 'phrase', 'frequence'), $filtered);
}

?>
    <div class="jumbotron">
        <h1><?=$project['name']?></h1>
        <p>Количество ключей: <?=db_count('phrases', array('project_id' => $id))?></p>
        <p>
          <a class="btn btn-primary btn-lg" href="/cluster?view=grouping&project_id=<?=$id?>" role="button">
            Перейти к группировке
          </a>
        </p>
    </div>
<?

//print_link("/cluster/grouping.php?project_id=$id", "Grouping");
//$keys = db_list('phrases', array('project_id' => $id));
//print_table($keys, '', "/cluster/project.php?id=$id&delete_phrase_id={ID}");
//print_create_form('create_phrases', array('phrases_text' => 'textarea'));
