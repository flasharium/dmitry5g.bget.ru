<?php

require_once "../libs/proj/inc.php";

if (isset($_REQUEST['delete_id'])) {
    db_delete_by_id('projects', $_REQUEST['delete_id']);
}

if (isset($_REQUEST['project'])) {
    db_insert('projects', $_REQUEST['project']);
}

insert_header();

$project_list = db_list("projects");

print_table($project_list, '/clustering/project.php?id={ID}', '/clustering/projects.php?delete_id={ID}');

print_create_form('project', array('name'));

insert_footer();

