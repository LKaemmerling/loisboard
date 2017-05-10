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

class PDB 
{
    public static $db = null; 

    /**
    * Verbindung herstellen
    *
    * Stellt die Verbindung zur MySql Datenbank her. Die Verbindung wird in der public static $db Variable gespeichert. 
    * Sendet eine Fehlermeldung wenn die Verbindung nicht erfolgreich hergestellt werden konnte. 
    * 
    * @author s-l 
    * @version 0.1.0 
    * @return bool 
    */
    public static function init($dbHost, $dbUser, $dbPassword, $dbDatabase)  
    {
        try {
            self::$db = new \PDO('mysql:host='.$dbHost.';dbname='.$dbDatabase.';charset=utf8mb4', $dbUser, $dbPassword);
        } catch(PDOException $ex) {
            echo "<strong>DB::".__FUNCTION__." Error:</strong> Es konnte keine Verbindung hergestellt werden!"; 
            $_SESSION["errors"][] = "<strong>DB::".__FUNCTION__." Error:</strong> Es konnte keine Verbindung hergestellt werden!"; 
            return false; 
        }
        self::$db->query("SET NAMES utf-8"); 
        return true; 
    }

    /**
    * Verbindung testen
    *
    * Versucht eine Verbindung zur MySql Datenbank herzustellen. Die Verbindung wird nicht gehalten. 
    *
    * @author s-l 
    * @version 0.0.1 
    * @return bool 
    */
    public static function testConnection($dbHost, $dbUser, $dbPassword, $dbDatabase) 
    {
        try {
            $db = new \PDO('mysql:host='.$dbHost.';dbname='.$dbDatabase.';charset=utf8mb4', $dbUser, $dbPassword);
        } catch(PDOException $ex) {
            return false; 
        }
        return true; 
    }

    /**
    * Query ausführen
    *
    * Führt eine Query direkt aus. 
    * Optional: Die Variable $params kann Optional mit Parametern befüllt werden.
    *
    * @author s-l 
    * @version 0.1.3
    * @return array
    */
    public static function query($sql, $params=null) 
    {
        if(self::$db == null) {
            echo "<strong>DB::".__FUNCTION__." Error:</strong> Es wurde keine Verbindung zur Datenbank gefunden.";
            return; 
        }
        $result = array(); 
        try {
            $state = self::$db->prepare($sql); 
            if($params == null)
                $state->execute(); 
            else
                $state->execute($params); 
            $result = $state->fetchAll(); 
        } catch(PDOException $ex) {
            $_SESSION["errors"][] = "<strong>DB::".__FUNCTION__." Error:</strong> " . $ex; 
        }
        return $result; 
    }

    /**
    * INSERT
    *
    * Wird verwendet um Daten in die Datenbank einzutragen. Dabei wird die Tabelle und ein Array mit Key => Value eingetragen. 
    *
    * @param $table(string) Die Datenbank-Tabelle in die etwas eingetragen werden soll
    * @param $datas(array) Die Daten die eingetragen werden sollen - im Style Key => Value 
    * @author s-l 
    * @version 0.0.6 
    */
    public static function insert($table, $datas) 
    {
        $sql = "INSERT INTO $table";
        $keys = ""; 
        $values = ""; 
        $params = array(); 
        foreach($datas as $key => $value) {
            $keys .= $key.",";
            $values .= "?,";  
            $params[] = $value; 
        }
        $keys = rtrim($keys, ","); 
        $values = rtrim($values, ","); 
        $sql .= " ($keys) VALUES ($values)";
        try 
        {
            $state = self::$db->prepare($sql); 
            $state->execute($params); 
        } 
        catch(PDOException $ex) 
        {
            $_SESSION["errors"][] = "<strong>DB::".__FUNCTION__. "Error:</strong> ".$ex;
        }
    }

    /**
    * UPDATE
    *
    * Wird verwendet um einen Tabelleneintrag (von dem die ID bekannt ist) upzudaten. 
    *
    * @param $table(string) Die Datenbank-Tabelle
    * @param $id(int) Die Row-ID die geupdated werden soll 
    * @param $data(string) Alle Felder die geupdated werden sollen 
    * @param $params(array) Die Parameter die in $data eingesetzt werden sollen 
    * @author s-l 
    * @version 0.0.2 
    */
    public static function update($table, $id, $data, $params=array()) 
    {
        $sql = "UPDATE $table SET ".$data." WHERE id=?";
        $params[] = $id; 
        try {
            $state = self::$db->prepare($sql); 
            $state->execute($params); 
        } 
        catch(PDOException $ex) {
            $_SESSION["errors"][] = "<strong>DB::".__FUNCTION__. "Error:</strong> ".$ex;
        }
    }

    /**
    * SELECT
    * 
    * Wird verwendet um Daten aus einer Tabelle auszulesen. Kann verschiedene Parameter beinhalten um die Suche zu verfeinern. 
    *
    * @param $table(string) Die Datenbank-Tabelle
    * @param $fields(string) Die Felder die zurückgegeben werden
    * @param $where(string) Die WHERE Angaben (z.B.: "id=? AND ground=?")
    * @param $order(string) Die Order Angaben (z.B.: "lastCheck DESC, id DESC")
    * @param $limit(string) Die Limit Angaben (z.B.: "5, 24")
    * @param $params(array) Die Parameter (für gewöhnlich bei $where verwendet) Inhalte die Escaped werden sollen
    * @author s-l 
    * @version 0.0.9 
    */
    public static function select($table, $fields="*", $where=null, $order=null, $limit=null, $params=null)
    {
        $sql = "SELECT $fields FROM $table";
        if($where != null) 
        {
            $sql .= " WHERE " . $where;
        }
        if($order != null) 
        {
            $sql .= " ORDER BY " . $order;
        }
        if($limit != null) 
        {
            $sql .= " LIMIT " . $limit;
        }
        try {
            $state = self::$db->prepare($sql); 
            if($params != null) 
                $state->execute($params); 
            else
                $state->execute(); 
            $rst = $state->fetchAll(); 
            return $rst; 
        }
        catch(PDOException $ex) {
            $_SESSION["errors"][] = "<strong>DB::".__FUNCTION__. "Error:</strong> ".$ex;
        }
    }

    /**
    * DELETE
    *
    * Wird verwendet um einen Eintrag aus einer Tabelle zu löschen.
    *
    * @param $table(string) Die Datenbank-Tabelle
    * @param $where(string) Die WHERE Angaben (z.B.: "id=?")
    * @param $params(array) Die Parameter die Escaped werden sollen 
    * @author s-l 
    * @version 0.0.3
    */
    public static function delete($table, $where, $params=null) 
    {
        $sql = "DELETE FROM $table WHERE " . $where;
        try {
            $state = self::$db->prepare($sql); 
            if($params != null)
                $state->execute($params); 
            else 
                $state->execute(); 
        }
        catch(PDOException $ex) {
            $_SESSION["errors"][] = "<strong>DB::".__FUNCTION__. "Error:</strong> ".$ex;
        }
    }

    /**
    * Insert ID 
    *
    * Lest die letzte eingesetzte ID aus. 
    *
    * @author s-l 
    * @version 0.0.1 
    */
    public static function insertID() 
    {
        return self::$db->lastInsertId(); 
    }


}
?>