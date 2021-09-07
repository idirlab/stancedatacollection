<?php
$db = null;
#date_default_timezone_set('UTC');
date_default_timezone_set('America/Chicago');

function getConnect() {
    # TODO: change local to server
    global $db;
    $local = FALSE;
    
    if(!$db) {
        if($local) {
            $config = array(
                'user' => 'root',
                'password' => '',
                'server' => 'localhost',
                'database' => 'wildfire',
                );  
        } else {
            $config = array(
                'user' => 'wildfire',
                'password' => '123456',
                'server' => 'localhost',
                'database' => 'wildfire',
                );
        }
        $dsn= "mysql:host=${config['server']};dbname=${config['database']}";
        $db = new PDO($dsn, $config['user'], $config['password']);
    }
    return $config;
    // return $db;
}

function log_error($message) {
    $m = sprintf("%s:%s\n", date('Y-m-d H:i:s'), print_r($message, true));
    error_log($m, 3, './db.log');
}

function execute($sql, $params = array(), $mode=PDO::FETCH_ASSOC) {
    try {
        $db = getConnect();

        $stmt = $db->prepare($sql);

        if($stmt->execute($params)) {
#            log_error($sql);
            return $stmt->fetchAll($mode);
        }
        else {
            log_error('fail to execute sql');    
            log_error($sql);    
            return false;
        }
    }
    catch(PDOException $e) {
        log_error('fail to execute sql w/ exception:');    
        log_error($e->getMessage());    
        log_error($sql);    
    }
    return false;
}

function gen_select_query($fields=array('*'), $tables=array(), $where=array(), $group=array(), $order=array(), $limit=array()) {
    $sql = 'SELECT %s FROM %s';
    $sql = sprintf($sql, implode(', ', $fields), implode(', ', $tables));
    if(!empty($where)) $sql .=  ' WHERE ' . implode(' AND ', $where);
    if($group) $sql .=  ' GROUP BY ' . implode(', ', $group);
    if(!empty($order)) $sql .= ' ORDER BY '. implode(',', $order);
    if(!empty($limit)) $sql .= ' LIMIT '. implode(',', $limit);
    return $sql;
}

function gen_insert_query($tables=array(), $fields=array(), $values=array()) {
    $sql = 'INSERT INTO %s (%s) VALUES (%s)';
    $sql = sprintf($sql, implode(', ', $tables), implode(', ', $fields), implode(', ', $values));    
    return $sql;
}

function gen_update_query($tables=array(), $fields=array(), $values=array(), $where=array()) {
    $sql = 'UPDATE %s SET %s';
    for ($i = 0; $i < count($fields); $i++)
	{
		$fields_values[$i] = $fields[$i]." = ".$values[$i];
	}
    $sql = sprintf($sql, implode(', ', $tables), implode(', ', $fields_values));
    if(!empty($where)) $sql .=  ' WHERE ' . implode(' AND ', $where);
    return $sql;
}
