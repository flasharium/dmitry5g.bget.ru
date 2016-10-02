<?php


// dmitry5g_clust - [Hw^n8C*

$user = 'dmitry5g_clust';
$password = '[Hw^n8C*';
$host = 'localhost';
$db_name = 'dmitry5g_clust';

$mysqli = new mysqli($host, $user, $password, $db_name);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

function db() {
    global $mysqli;
    return $mysqli;
}

function db_query($query) {
    $result = db()->query($query);
    if (db()->error) {
        dump($query);
        dump(db()->error);
        die;
    } else {
        return $result;
    }
}

function db_get($table, $criteria, $order = '') {
    $add = '';
    if ($criteria) {
        foreach ($criteria as $field => $value) {
            if ($field == 'add') {
                $add .= " $value ";
            } else {
                $add .= " and $field = '$value' ";
            }
        }
    }
    $query = "select * from $table where 1 " . $add . " $order limit 1 ";
    $result = db_query($query);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return array();
    }
}

function db_list($table, $criteria = null, $fields = '*', $order = '') {
    $add = '';
    if ($criteria) {
        foreach ($criteria as $field => $value) {
            if ($field == 'add') {
                $add .= " $value ";
            } elseif (is_array($value)) {
                $add .= " and $field IN (" . implode(',', $value) . ") ";
            } else {
                $add .= " and $field = '$value' ";
            }
        }
    }
    $query = "select $fields from $table where 1 " . $add . " $order ";
    $result = db_query($query);

    

    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return array();
    }
}

function db_delete_by_id($table, $ids) {
    if (is_array($ids)) {
        $where = "id IN (" . implode(',', $ids) . ') ';
    } else {
        $where = "id = $ids";
    }
    $query = "delete from $table where $where";
    db_query($query);
}

function db_insert($table, $fields) {
    $query = "INSERT INTO `$table` (`id`, `" . implode('`,`', array_keys($fields))
        ."`) VALUES (NULL, '".implode('\',\'', array_values($fields))
        ."');";
    db_query($query);
    
}

function db_insert_multiple($table, $fields, $values) {
    $query = "INSERT INTO `$table` (`id`, `" . implode('`, `', $fields) ."`) VALUES ";
    $parts = array();
    foreach ($values as $table_values) {
        array_push($parts, " (NULL, '" . implode('\', \'', $table_values) ."') ");
    }
    $query .= implode(',', $parts) . ';';
    db_query($query);
}

function db_get_by_id($table, $id) {
    $result = db_query("select * from $table where id=$id limit 1")->fetch_all(MYSQLI_ASSOC);
    return $result ? $result[0] : null;
}

function db_update($table, $fields, $crit = array()) {

    $set = array();
    $where = '';
    foreach ($fields as $key => $value) {
        if ($key != 'id') {
            array_push($set, " $key='$value' ");
        }
    }
    $set = implode(',', $set);
    if (isset($fields['id'])) {
        if (is_string($fields['id'])) {
            $where = " and id = " . $fields['id'];
        } elseif (is_array($fields['id'])) {
            $where = " and id in (" . implode(',', $fields['id']) . ') ';
        }
    }

    foreach ($crit as $field => $value) {
        $where .= " and $field = '$value'";
    }


    $query = "UPDATE $table
        SET $set
        WHERE 1 $where ";

    db_query($query);
}

function db_count($table, $fields = array()) {
    $where = '';
    foreach ($fields as $key => $value) {
        $where .= " and $key='$value'";
    }

    $query = "SELECT COUNT(*) FROM $table WHERE 1 $where";
    $result = db_query($query);
    
    $res = $result->fetch_row();

    return $res[0];
}
