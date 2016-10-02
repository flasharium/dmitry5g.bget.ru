<?php

require_once "../libs/proj/inc.php";
header('Content-Type: application/json');

if (!isset($_REQUEST['project_id'])) json_error('project_id missing!');
$id = $_REQUEST['project_id'];

if (!isset($_REQUEST['action'])) json_error('action missing');
$action = $_REQUEST['action'];

if ($action == 'list_groups') {

    $keywords = array();
    foreach (db_list('phrases', array('project_id' => $id, 'add' => 'and group_id > 0'), 'id, phrase, frequence, group_id') as $keyword) {
        $keywords[$keyword['id']] = $keyword;
    }

    function parseNode($node, &$groups, &$sections, &$keywords, $current_section_id = 0) {
        static $section_id = 1;
        static $group_id = 1;

        if (is_array($node) && isset($node[0]) && is_array($node[0])) {

            foreach ($node as $child) {
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
    $struct = db_get('key_struct', array('project_id' => $id), "order by id desc");
    $struct = json_decode($struct['data'], true);
    parseNode($struct, $data['groups'], $data['sections'], $data['keywords']);

    echo json_encode(array(
        'keywords' => array_values($data['keywords']),
        'sections' => array_values($data['sections']),
        'groups' => array_values($data['groups']),
        'struct' => $struct,
    ));
    die;

}

if ($action == 'list_dataset') {

    $dataset = isset($_REQUEST['dataset']) ? $_REQUEST['dataset'] : 'free_keywords';
    $add = '';
    $datasets = array(
        'free_keywords' => array('blacklist' => 0, 'group_id' => 0),
        'grouped_keywords' => array('blacklist' => 0),
        'blacklist_keywords' => array('blacklist' => 1),
    );

    if (isset($_REQUEST['filter'])) {
        $filters = $_REQUEST['filter'];
        foreach ($filters as $filter){
            $filter = trim($filter);
            if ($filter) {
                $add .= " and phrase like \"%$filter%\" ";
            }
        }
    }

    if ($dataset == 'grouped_keywords') {
        $add .= ' and not group_id = 0 ';
    }


    $criteria = array_merge(array('project_id' => $id, 'add' => $add), $datasets[$dataset]);
    $keys = db_list('phrases', $criteria, 'id, phrase, frequence');

    echo json_encode($keys);
}

if ($action == 'change_struct') {
    db_insert('key_struct', array(
        'project_id' => $id,
        'data' => $_REQUEST['struct'],
    ));

    db_query("DELETE FROM `key_struct`
            WHERE project_id = $id and id NOT IN (
              SELECT id
              FROM (
                SELECT id
                FROM `key_struct`
                where project_id = $id
                ORDER BY id DESC
                LIMIT 5
              ) foo
            );");

    preg_match_all('/(?<ids>\d+)/', $_REQUEST['struct'], $matches);
    if ($matches['ids']) {
        db_update('phrases', array('project_id' => $id, 'group_id' => 0));
        db_update('phrases', array('group_id' => 1, 'id' => $matches['ids']));
    }

    echo json_encode(array(
        'status' => 'success',
        'ids' => $matches['ids'],
    ));
}
