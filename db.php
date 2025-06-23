<?php
$db = null;
#date_default_timezone_set('UTC');
date_default_timezone_set("America/Chicago");

require_once 'credential.php';

function getConnect()
{
    include (getcwd()."/GLOBAL.php");
    global $db, $DB_PASSWORD;
    if (!$db) {
        if ($LOCAL) {
            if ($GROUNDTRUTH_ENV) {
                $config = [
                    "user" => "root",
                    "password" => "",
                    "server" => "localhost",
                    "database" => "wildfire_groundtruth",
                    "port" => "3307",
                ];
            } else {
                $config = [
                    "user" => "root",
                    "password" => "",
                    "server" => "localhost",
                    "database" => "wildfire",
                    "port" => "3307",
                ];
            }
        } else {
            if ($GROUNDTRUTH_ENV) {
                $config = [
                    "user" => "root",
                    "password" => $DB_PASSWORD,
                    "server" => "localhost",
                    "database" => "wildfire_groundtruth",
                ];
            } else {
                $config = [
                    "user" => "root",
                    "password" => $DB_PASSWORD,
                    "server" => "localhost",
                    "database" => "wildfire",
                ];
            }
        }
        $dsn = "mysql:host=${config["server"]};dbname=${config["database"]}";
        try {
            $db = new PDO($dsn, $config["user"], $config["password"]);
        } catch (Exception $e) {
            log_error($e);
            return $e;
        }
    }
    return $db;
}

function log_error($message)
{
    $m = sprintf("%s:%s\n", date("Y-m-d H:i:s"), print_r($message, true));
    error_log($m, 3, "./db.log");
}

function execute($sql, $params = [], $mode = PDO::FETCH_ASSOC)
{
    try {
        $db = getConnect();

        $stmt = $db->prepare($sql);

        if ($stmt->execute($params)) {
            return $stmt->fetchAll($mode);
        } else {
            log_error("fail to execute sql");
            log_error($sql);
            return false;
        }
    } catch (PDOException $e) {
        log_error("fail to execute sql w/ exception:");
        log_error($e->getMessage());
        log_error($sql);
    }
    return false;
}

function gen_select_query(
    $fields = ["*"],
    $tables = [],
    $where = [],
    $group = [],
    $order = [],
    $limit = []
) {
    $sql = "SELECT %s FROM %s";
    $sql = sprintf($sql, implode(", ", $fields), implode(", ", $tables));
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    if ($group) {
        $sql .= " GROUP BY " . implode(", ", $group);
    }
    if (!empty($order)) {
        $sql .= " ORDER BY " . implode(",", $order);
    }
    if (!empty($limit)) {
        $sql .= " LIMIT " . implode(",", $limit);
    }
    return $sql;
}

function gen_insert_query($tables = [], $fields = [], $values = [])
{
    $sql = "INSERT INTO %s (%s) VALUES (%s)";
    $sql = sprintf(
        $sql,
        implode(", ", $tables),
        implode(", ", $fields),
        implode(", ", $values)
    );
    return $sql;
}

function gen_update_query($tables = [], $fields = [], $values = [], $where = [])
{
    $sql = "UPDATE %s SET %s";
    for ($i = 0; $i < count($fields); $i++) {
        $fields_values[$i] = $fields[$i] . " = " . $values[$i];
    }
    $sql = sprintf($sql, implode(", ", $tables), implode(", ", $fields_values));
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    return $sql;
}
