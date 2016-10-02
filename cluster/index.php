<?php

require_once "clust_inc.php";

insert_header();

$view_properties = array(
    'grouping' => array( 'insert_dash_navbar' => false ),
);

$view = array_get($_REQUEST, 'view', 'projects');

if (array_get($view_properties, "$view.insert_dash_navbar", true)) {
    insert_dash_navbar('cluster');
}

require_once "views/$view.php";


insert_footer();
