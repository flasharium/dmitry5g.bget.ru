<?php

require_once "../libs/proj/inc.php";
header('Content-Type: application/json');

if (!isset($_REQUEST['project_id'])) json_error('project_id missing!');
$id = $_REQUEST['project_id'];

if (!isset($_REQUEST['action'])) json_error('action missing');
$action = $_REQUEST['action'];

if ($action == 'list_groups') {

    $res = array();
    $res['keywords'] = db_list('phrases', array('project_id' => $id, 'add' => 'and group_id > 0'), 'id, phrase, frequence, group_id');
    $res['groups'] = db_list('groups', array('project_id' => $id), 'id, section_id');
    $res['sections'] = db_list('sections', array('project_id' => $id), 'id, parent_id, title');
    echo json_encode($res);
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

if ($action == 'diff') {

}
