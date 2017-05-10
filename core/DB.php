<?php 
namespace Main; 
class DB {

    public static $db = null;

    public static $insertID;

    public static function init($dbHost, $dbUser, $dbPassword, $dbName) {
        self::$db = new \mysqli($dbHost, $dbUser, $dbPassword, $dbName);
        if (self::$db->connect_errno) {
            $_SESSION['errors'][] = "MySQL connection failed: ". self::$db->connect_error;
            die("Mysql connection failed: ". self::$db->connect_error); 
        }
        self::$db->query("SET NAMES utf8;");

        //self::$db = new \PDO('mysql:host='.$dbHost.';dbname='.$dbName.';charset=utf8mb4', $dbUser, $dbPassword);
        //self::$db->query("SET NAMES utf8;"); 
    }
	
	public static function testConnection($dbHost, $dbUser, $dbPw, $dbName) {
		$b = new \mysqli($dbHost, $dbUser, $dbPw, $dbName); 
		if($b->connect_errno) {
			return false; 
		}
		return true; 
	}

    public static function query($sql, $debug = false) {
        $result = self::$db->query($sql);
        if ($debug == true) {
            $_SESSION['debug'][] = __FUNCTION__ . ': $sql is <strong>'.$sql.'</strong>';
        }
        if (self::$db->errno) {
            $_SESSION['errors'][] = "<p>insert failed: " . self::$db->error . "<br> statement was: <strong> $sql </strong></p>";
        }
        return $result;
    }

    public static function insert($table, $data, $debug = false) {
        $keys = ""; $values = "";
        foreach ($data as $key => $value) {
            $key = self::escape($key);
            $value = self::escape($value);
            $keys .= $key . ", ";
            if ($value == null) {
                $values .= "null, ";
            }
            else {
                $values .= "'" . $value  . "', ";
            }
        }
        $keys = rtrim($keys, ', ');
        $values = rtrim($values, ', ');

        $sql = "INSERT INTO $table (" . $keys . ") VALUES (" . $values . ")";

        if ($debug == true) {
            $_SESSION['debug'][] = __FUNCTION__ . ': $sql is <strong>' . $sql . '</strong>';
        }

        self::$db->query($sql);

        if (self::$db->errno) {
            $_SESSION['errors'][] = '<p>' . __FUNCTION__ . ' failed: ' . self::$db->error . '<br> statement was: <strong> ' . $sql . '</strong></p>';
        }

        self::$insertID = self::$db->insert_id;
    }
    
    # Wird noch verwendet? 
    # Entfernen! 
    public static function insert_passwort($uId, $passwort) {
        $sql = "UPDATE accounts SET passwort=MD5('".self::escape($passwort)."') WHERE id='".self::escape($uId)."'";
        self::$db->query($sql); 
    }
    
    public static function update($table, $id, $data, $debug = false) {
        $sql = "UPDATE $table SET ";
        foreach ($data as $key => $value) {
            $key = self::escape($key);
            $value = self::escape($value);
            if ($value == null) {
                $sql .= "$key = null, ";
            } else {
                $sql .= "$key = '$value', ";
            }
        }

        $sql = rtrim($sql, ", ");

        $sql .= " WHERE " . self::getPrimaryKeyColumn($table) . " = $id";

        if ($debug == true) {
            $_SESSION['debug'][] = __FUNCTION__ . ': $sql is <strong>' . $sql . '</strong>';
        }

        self::$db->query($sql);

        if (self::$db->errno) {
            $_SESSION['errors'][] = '<p>' . __FUNCTION__ . ' failed: ' . self::$db->error . '<br> statement was: <strong> ' . $sql . '</strong></p>';
        }
    }
    
    public static function select($table, $columns = "*", $where = null, $limit = null, $order = null, $debug = false) 
    {
        $sql = "SELECT ". self::generateColumnList($columns) ." FROM $table"; 
        if($where != null) {
            $sql .= " WHERE ".$where;
        }
        if($order != null) {
            $sql .= " ORDER BY ".$order; 
        }
        if($limit != null) {
            $sql .= " LIMIT ".$limit; 
        }
        $result = self::$db->query($sql); 
		if (self::$db->errno) {
            $_SESSION['errors'][] = '<p>' . __FUNCTION__ . ' failed: ' . self::$db->error . '<br> statement was: <strong> ' . $sql . '</strong></p>';
        }
        return $result; 
    }
    
    public static function delete($table, $id, $debug = false) {
        $sql = "DELETE FROM $table WHERE " . self::getPrimaryKeyColumn($table) . " = $id";

        if ($debug == true) {
            $_SESSION['debug'][] = __FUNCTION__ . ': $sql is <strong>' . $sql . '</strong>';
        }

        self::$db->query($sql);

        if (self::$db->errno) {
            $_SESSION['errors'][] = '<p>' . __FUNCTION__ . ' failed: ' . self::$db->error . '<br> statement was: <strong>' . $sql . '</strong></p>';
        }
    }
    
    public static function escape($string) {
        return self::$db->real_escape_string($string);
    }

    public static function getPrimaryKeyColumn($table, $debug = false) {
        $sql = "SHOW KEYS FROM $table WHERE key_name = 'PRIMARY'";

        if ($debug == true) {
            $_SESSION['debug'][] = __FUNCTION__ . ': $sql is <strong>' . $sql . '</strong>';
        }
        
        $result = self::$db->query($sql);

        while ($row = $result->fetch_assoc()) {
            return $row['Column_name'];
        }

        if (self::$db->errno) {
            $_SESSION['errors'][] = '<p>' . __FUNCTION__ . ' failed: ' . self::$db->error . '<br> statement was: <strong>' . $sql . '</strong></p>';
        }

        return false;
    }

    private static function generateColumnList($columns) {
        if (is_array($columns)) {
            return implode(', ', $columns);
        } else {
            return $columns;
        }
        
    }
}
?>