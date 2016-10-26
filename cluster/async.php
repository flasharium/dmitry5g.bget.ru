<?php

require_once "clust_inc.php";
header('Content-Type: application/json');

if (!isset($_REQUEST['project_id'])) json_error('project_id missing!');
$id = $_REQUEST['project_id'];

if (!isset($_REQUEST['action'])) json_error('action missing');
$action = $_REQUEST['action'];

$routing = array(
//  'action_name'        => 'function_name'
    'list_groups'        => 'list_groups_handler',
    'list_dataset'       => 'list_dataset_handler',
    'change_struct'      => 'change_struct_handler',
    'move_to_trash'      => 'move_to_trash_handler',
    'restore_from_trash' => 'restore_from_trash_handler',
    'unset_group_ids'    => 'unset_group_ids_handler',
    'create_phrases'     => 'create_phrases_handler',
);

function create_phrases_handler() {
    global $id;
    $raw_phrases = array_get($_REQUEST, 'phrases_raw_text', '');
    $filtered = create_unique_phrases($raw_phrases, $id);
    return array('created_count' => $filtered);
}

function unset_group_ids_handler() {
    if (!$ids = array_get($_REQUEST, 'ids')) return false;
    db_update('phrases', array('group_id' => 0, 'id' => $ids));
    return array('unset_group_id_for_phrases' => $ids);
}

function restore_from_trash_handler() {
    if (!$ids = array_get($_REQUEST, 'ids')) return false;
    db_update('phrases', array('blacklist' => 0, 'id' => $ids));
    return array('restored_from_blacklist' => $ids);
}

function move_to_trash_handler() {
    if (!$ids = array_get($_REQUEST, 'ids')) return false;
    db_update('phrases', array('blacklist' => 1, 'id' => $ids));
    return array('new_blacklist_ids' => $ids);
}

function list_groups_handler() {
    global $id;
    return get_struct_data($id);
}

function list_dataset_handler() {
    global $id;
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
    $keys = db_list('phrases', $criteria, 'id, phrase, frequence', ' order by frequence desc ');

    return $keys;
}

function change_struct_handler() {
    global $id;
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
        db_update('phrases', array('group_id' => 1, 'blacklist' => 0, 'id' => $matches['ids']));
    }

    return array(
        'status' => 'success',
        'ids' => $matches['ids'],
    );
}

$result = array();

if ($func = array_get($routing, $action, false)) {
    $result = $func();
    if (!$result) {
        $result = array(
            'status' => 'error',
            'msg' => 'wrong request data',
        );
    }
} else {
    $result = array(
        'status' => 'error',
        'msg' => 'unknown action',
    );
}

echo json_encode($result);
