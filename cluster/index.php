<?php

require_once "clust_inc.php";

insert_header();

$view_properties = array(
    'grouping' => array( 'insert_dash_navbar' => false ),
);

$views = array(
    'grouping' =>  array('view' => 'grouping',  'admin' => 1, 'parent_view' => '',            'title' => 'Кластеризация'),
    'project' =>   array('view' => 'project',   'admin' => 1, 'parent_view' => 'projects',    'title' => 'Проект'),
    'projects' =>  array('view' => 'projects',  'admin' => 1, 'parent_view' => '',            'title' => 'Проекты'),
);

$view = array_get($_REQUEST, 'view', 'projects');

if (array_get($view_properties, "$view.insert_dash_navbar", true)) {
    insert_dash_navbar('cluster');
    insert_dash_breadcrumbs('cluster', $views, $view);
}

require_once "views/$view.php";


insert_footer();
