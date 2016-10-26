<?php

require_once "../libs/proj/inc.php";
require_once "../inc/common.inc.php";

if (!isset($_SESSION['user_id'])) {
    return redirect("/cm/auth.php?source=cluster");
}

function get_struct_data($project_id) {
    $keywords = array_hash(db_list('phrases', array(
        'project_id' => $project_id,
        'add' => 'and group_id > 0'
    ), 'id, phrase, frequence, group_id'));

    function parseNode($node, &$groups, &$sections, &$keywords, $current_section_id = 0) {
        static $section_id = 1;
        static $group_id = 1;

        if (is_array($node) && isset($node[0]) && is_array($node[0])) {

            foreach ($node as $child) {
                // we need to go deeper =)
                parseNode($child, $groups, $sections, $keywords, $current_section_id);
            }

        } else {

            if (isset($node['n'])) { // section

                $sections[$section_id] = array(
                    'id' => ++$section_id,
                    'parent_id' => $current_section_id,
                    'title' => $node['n'],
                );

                parseNode($node['c'], $groups, $sections, $keywords, $section_id);

            } elseif (isset($node['c'])) { // group

                $groups[$group_id] = array(
                    'id' => $group_id,
                    'section_id' => $current_section_id,
                );

                foreach ($node['c'] as $key_id) {
                    $keywords[$key_id]['group_id'] = $group_id;
                }

                $group_id++;
            }
        }
    }

    $data = array('keywords' => $keywords, 'groups' => array(), 'sections' => array());
    $struct = db_get('key_struct', array('project_id' => $project_id), "order by id desc");
    $struct = json_decode(array_get($struct, 'data', '{}'), true);
    parseNode($struct, $data['groups'], $data['sections'], $data['keywords']);

    return array(
        'keywords' => array_values($data['keywords']),
        'sections' => array_values($data['sections']),
        'groups' => array_values($data['groups']),
        'struct' => $struct,
    );
}

function create_unique_phrases($raw_phrases, $project_id)
{
    $phrases = explode("\n", str_replace('+', '', $raw_phrases));

    $existing_phrases = db_list('phrases', array('project_id' => $project_id));
    $existing_phrases = to_flat_array($existing_phrases, 'id', 'phrase');
    $existing_phrases = array_flip($existing_phrases);

    $filtered = array();
    foreach ($phrases as $phrase) {
        $value = trim($phrase);
        $freq = 0;
        if (strpos($value, ';') !== false) {
            list($value, $freq) = explode(';', $value);
        } else if (strpos($value, "\t") !== false) {
            list($value, $freq) = explode("\t", $value);
        }
        $freq = str_replace(' ', '', $freq);
        if ($value) {
            if (!isset($existing_phrases[$value])) {
                array_push($filtered, array(
                    $project_id,
                    $value,
                    $freq
                ));
            } else if ($freq) {
                db_update('phrases', array(
                    'id'        => $existing_phrases[$value],
                    'frequence' => $freq,
                ));
            }
        }
    }

    if ($filtered) {
        db_insert_multiple('phrases', array(
            'project_id',
            'phrase',
            'frequence'
        ), $filtered);
    }

    return count($filtered);
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
              <input type="text" name="create_project[name]" class="form-control" id="createProjectInput"/>
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
