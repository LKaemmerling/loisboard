<?php
/**
* LoisBoard Installation 1.0 
*
*/
session_start(); 
header ('Content-type: text/html; charset=utf-8');

$step = 0; 
if(isset($_POST["step"]))
    $step = $_POST["step"]; 

$html = Main::init($step); 

class Main 
{
    public static $apiBaseUrl = "https://www.loisboard.at/"; 
    public static $connKey = "345fdwf5W235"; 
    public static $installVersion = "Beta002"; 

    public static function init($step) 
    {
        $html = ""; 

        $html .= "<div class='pageWrapper'>"; 

            $html .= "<div class='pageHeaderContainer'>
            
            </div>"; 

            $html .= "<div class='pageContentContainer'>"; 
                $html .= self::showContent($step); 
            $html .= "</div>";

        $html .= "</div>";  

        $html .= "<div class='pageFooter'>powered by <a href='".self::$apiBaseUrl."' target='_blank'>LoisBoard</a></div>";

        return $html; 
    }

    public static function showContent($step) 
    {
        $html = ""; 

        if($step == 0) # Verbindung zur API prüfen
        {
            $html .= "<h1>LoisBoard Installation</h1>";
            $html .= "<p>
                Sie befinden sich im Installationsprozess der Forensoftware LoisBoard. Falls Sie nicht beabsichtigen diese Forensoftware auf ihren Webserver
                zu installieren, schließen Sie bitte das Fenster und löschen Sie die Datei install.php von ihrem Server. 
            </p>
            <p>
                Auf den folgenden Seiten werden einige Grundlagen für die Installation überprüft und gesetzt. Klicken Sie auf Weiter um die Installation zu beginnen.
            </p>"; 
            if(self::testAPIconnection())
            {
                $html .= "<form action='install.php' method='post'>
                    <input type='hidden' name='step' value='1'  /> 
                    <button class='button-ok button-right'>Weiter</button>
                </form>"; 
            } 
            else
            {
                $html .= "<div class='alert alert-danger'>Es konnte keine Verbindung zur API hergestellt werden. Bitte wenden Sie sich an einen Systemadministrator und 
                vergewissern Sie sich das der Webserver mit dem Internet verbunden ist.</div>"; 
            }
        }
        else if($step == 1) # Schreibrechte prüfen
        {
            $html .= "<h1>LoisBoard Installation &nbsp; Schritt 1: Rechte überprüfen</h1>";
            $html .= "<p>Im folgenden wird geprüft ob die notwendigen Lese- und Schreibrechte für den Ordner vorhanden sind.</p>";

            $perms = false; 

            if(is_writeable(".") && is_writeable("install.php") && is_readable(".") && is_readable("install.php"))
            {
                $perms = true; 
            }

            if($perms) # Hat die Rechte 
            {
                $html .= "<div class='alert alert-success'>Die nötigen Rechte für das Hauptverzeichnis sind vorhanden. Klicken Sie auf Weiter um die Installation fortzusetzen.</div>
                <form action='install.php' method='post'>
                    <input type='hidden' name='step' value='2' /> 
                    <button class='button-ok button-right'>Weiter</button>
                </form>"; 
            }
            else # Hat nicht die notwendigen Rechte 
            {
                $html .= "<div class='alert alert-danger'>Bitte stellen Sie sich das für das Hauptverzeichnis (in dem die install.php Datei liegt) und für die install.php Datei Schreibrechte vorhanden sind. Andernfalls ändern
                Sie die Rechte und aktualisieren Sie die Seite.</div>";
                $html .= "<form action='install.php' method='post'>
                    <input type='hidden' name='step' value='1' /> 
                    <button class='button-ok button-right'>Aktualisieren</button>
                </form>";  
            }
        }
        else if($step == 2)
        {
            $html .= "<h1>LoisBoard Installation &nbsp; Schritt 2: Dateien herunterladen</h1>";
            $html .= "<p>In diesem Schritt werden automatisch alle Dateien gedownloadet und eingerichtet.</p>";

            $postdata = "connKey=".self::$connKey."&domain=test&func=downloadfull&version=" . self::$installVersion; 
            $ch = curl_init(self::$apiBaseUrl . "data/plugins/download/api.php"); 
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $data = curl_exec($ch);
            if(substr($data, 0, 5) == "Error")
            {
                $html .= "<div class='alert alert-danger'>Es konnte keine Verbindung zu der API hergestellt werden.</div>";
                return $html; 
            } 
            $data = json_decode($data, true); 
            if(self::downloadUrlToFile(self::$apiBaseUrl . $data["path"], "install.zip")) 
            {
                //exec("tar -xf install.tar"); 
                $za = new ZipArchive(); 
                $res = $za->open("install.zip"); 
                if($res === TRUE) 
                {
                    $za->extractTo(".");
                    $za->close(); 
                }

                if(file_exists("installdb.php") && file_exists("installperms.php"))
                {
                    require_once("installperms.php"); 
                    foreach($fullperms["folders"] as $fullperm) 
                    {
                        chmod($fullperm, 0777); 
                    }
                    foreach($fullperms["files"] as $fullperm) 
                    {
                        chmod($fullperm, 0777); 
                    }

                    unlink("install.zip"); 

                    $html .= "<div class='alert alert-success'>Die Datei wurde erfolgreich heruntergeladen. Klicken Sie auf Weiter um die 
                    Datenbankverbindung einzurichten.</div>
                    <form action='install.php' method='post'>
                        <input type='hidden' name='step' value='3' /> 
                        <button class='button-ok button-right'>Weiter</button>
                    </form>"; 
                }
                else
                {
                    $html .= "<div class='alert alert-danger'>Die Datei konnte nicht entpackt werden. Sie benötigen das Linux Paket \"tar\" auf ihrem Webspace um die Datei zu entpacken.</div>";
                }
            }
            else
            {
                $html .= "<div class='alert alert-danger'>Die Datei konnte nicht heruntergeladen werden.</div>";
            }
        }
        else if($step == 3)
        {
            $showform = true; 

            if(isset($_POST["domain"])) # Wurde abgesendet
            {

                $input = array("domain" => $_POST["domain"],
                    "dbhost" => $_POST["dbhost"],
                    "dbuser" => $_POST["dbuser"],
                    "dbpw" => $_POST["dbpw"],
                    "dbdb" => $_POST["dbdb"]
                );
                if(DB::testConnection($input["dbhost"], $input["dbuser"], $input["dbpw"], $input["dbdb"]))
                {
                    $html .= "<h1>LoisBoard Installation &nbsp; Schritt 3.2: Datenbankverbindung erfolgreich</h1>";
                    $html .= "<div class='alert alert-success'>Die Verbindung zur Datenbank wurde erfolgreich hergestellt. Klicken Sie auf Weiter um die Datenbank vollständig einzurichten.</div>";
                    $showform = false; 

                    $string = "<?php\n\$db = array(\n\"host\" => \"".$input["dbhost"]."\",\n\"user\" => \"".$input["dbuser"]."\",\n\"pw\" => \"".$input["dbpw"]."\",\n\"db\" => \"".$input["dbdb"]."\"\n);\n\$api = array(\n\"domain\" => \"".$input["domain"]."\",\n\"api_link\" => \"".self::$apiBaseUrl."\",\n\"version\" => \"".self::$installVersion."\"\n);\n?>";
                    $file = "config/db.php";
                    $fop = fopen($file, "w+"); 
                    fwrite($fop, $string); 
                    fclose($fop); 

                    $html .= "<form action='install.php' method='post'>
                        <input type='hidden' name='step' value='4' /> 
                        <button class='button-ok button-right'>Weiter</button>
                    </form>";
                }
                else
                {
                    $html .= "<div class='alert alert-danger'>Es konnte keine Verbindung zur Datenbank hergestellt werden.</div>";
                }
            }

            if($showform) 
            {
                $html .= "<h1>LoisBoard Installation &nbsp; Schritt 3: Datenbankverbindung einrichten</h1>";
                $html .= "<p>Bitte geben Sie in diesem Schritt die Domain an unter der das Forum später erreichbar sein soll und eine Datenbankverbindung.</p>";
                $html .= "<form action='install.php' method='post'>
                    <input type='hidden' name='step' value='3' /> 
                    <input type='text' name='domain' placeholder='Domain' /> <br />
                    <input type='text' name='dbhost' placeholder='Datenbank Host' /> <br />
                    <input type='text' name='dbuser' placeholder='Datenbank Benutzer' /> <br />
                    <input type='text' name='dbpw' placeholder='Datenbank Passwort' /> <br />
                    <input type='text' name='dbdb' placeholder='Datenbank' /> <br />
                    <button class='button-ok'>Weiter</button>
                </form>";
            }
        }
        else if($step == 4)
        {
            require_once("config/db.php"); 
            DB::init($db["host"], $db["user"], $db["pw"], $db["db"]); 

            self::testAPIconnection($api["domain"]);

            require_once("installdb.php"); 
            foreach($sql as $sq) 
            {
                DB::query($sq); 
            }

            $html .= "<h1>LoisBoard Installation &nbsp; Schritt 4: Datenbank einrichten</h1>";
            $html .= "<p>In diesem Schritt wird die Datenbankstruktur automatisch eingerichtet.</p>";

            $html .= "<div class='alert alert-success'>Die Datenbank wurde vollständig eingerichtet. Klicken Sie auf Weiter um einen Admin-Account zu erstellen.</div>";
            $html .= "<form action='install.php' method='post'>
                        <input type='hidden' name='step' value='5' /> 
                        <button class='button-ok button-right'>Weiter</button>
                    </form>";
        }
        else if($step == 5)
        {
            require_once("config/db.php"); 
            DB::init($db["host"], $db["user"], $db["pw"], $db["db"]);

            $html .= "<h1>LoisBoard Installation &nbsp; Schritt 5: Account einrichten / Installation abschließen</h1>";

            $showform = true; 

            if(isset($_POST["username"]))
            {
                $input = array("username" => $_POST["username"],
                    "vorname" => $_POST["firstName"],
                    "nachname" => $_POST["lastName"],
                    "mail" => $_POST["mail"],
                    "pw1" => $_POST["password"],
                    "pw2" => $_POST["password2"]
                );

                if($input["pw1"] != $input["pw2"] || $input["pw1"] == "") 
                {
                    $html .= "<div class='alert alert-danger'>Sie müssen ein Passwort angeben!</div>";
                }
                else
                {
                    $showform = false;
                    $sql = "INSERT INTO `accounts` (username, vorname, nachname, mail, passwort, registerTime) VALUES ('".DB::escape($input["username"])."', '".DB::escape($input["vorname"])."', '".DB::escape($input["nachname"])."', '".DB::escape($input["mail"])."', MD5('".DB::escape($input["pw1"])."'), '".time()."')"; 
                    DB::query($sql);
                    $html .= "<div class='alert alert-success'><strong>Glückwunsch!</strong> Dein Forum wurde erfolgreich eingerichtet. Klicke auf Weiter um zum Forum zu gelangen.</div>";
                    $html .= "<form action='install.php' method='post'>
                        <input type='hidden' name='step' value='6' />
                        <button class='button-ok button-right'>Weiter</button>
                    </form>";
                }
            } 

            if(file_exists("installdb.php"))
                unlink("installdb.php"); 
            if(file_exists("installperms.php"))
                unlink("installperms.php"); 

            if($showform)
            {
                $html .= "<form action='install.php' method='post'>
                    <input type='hidden' name='step' value='5' /> 
                    <input type='text' name='username' placeholder='Benutzername' /> <br />
                    <input type='text' name='firstName' placeholder='Vorname' /> <br />
                    <input type='text' name='lastName' placeholder='Nachname' /> <br />
                    <input type='text' name='mail' placeholder='E-Mail Adresse' /> <br />
                    <input type='password' name='password' placeholder='Passwort' /> <br />
                    <input type='password' name='password2' placeholder='Passwort wiederholen' /> <br />
                    <button class='button-ok button-right'>Account anlegen</button>
                </form>";
            }
        }
        else if($step == 6)
        {
            unlink("install.php"); 
            header("Location: index.php"); 
        }

        return $html; 
    }

    public static function testAPIconnection($domain = null) 
    {
        $postdata = "connKey=".self::$connKey."&domain=test&func=test"; 
        if($domain != null) $postdata = "connKey=".self::$connKey."&domain=$domain&func=test"; 
        $ch = curl_init(self::$apiBaseUrl . "data/plugins/download/api.php"); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $data = curl_exec($ch);
        if(substr($data, 0, 5) == "Error") return false; 
        $array = json_decode($data, true); 
        if(is_array($array)) 
        {
            if($array["works"] == 1) return true; 
        }
        return false; 
    }

    public static function downloadUrlToFile($url, $outFileName)
    {
        if(is_file($url))
        {
            if(copy($url, $outFileName)) return true; 
            else return false; 
        }
        else
        {
            $options = array(
            CURLOPT_FILE => fopen($outFileName, 'w+'),
            CURLOPT_TIMEOUT => 28800, // set this to 8 hours so we dont timeout on big files
            CURLOPT_URL => $url
            );
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            curl_close($ch);

            if(file_exists($outFileName)) return true; 
            else return false; 
        }
    }
}

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
    }
	
	public static function testConnection($dbHost, $dbUser, $dbPw, $dbName) {
		$b = new mysqli($dbHost, $dbUser, $dbPw, $dbName); 
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

<style>
    * {
        margin:0; 
        padding:0; 
        color:#333; 
    }

    html, body {
        width:100%; 
        height:100%; 
    }

    body {
        background:rgba(0,0,0,0.04);
    }

    div {
        box-sizing:border-box; 
    }

    h1 {
        margin-bottom:5px; 
    }

    p {
        margin-top:3px; 
        margin-bottom:4px; 
    }

    .pageWrapper {
        width:100%; 
        min-height:calc(100% - 30px); 
    }

    .pageHeaderContainer {
        width:100%; 
        height:70px; 
        background:lightblue; 
        background:#E94646;
    }

    .pageContentContainer {
        width:1000px; 
        margin-left:auto; 
        margin-right:auto; 
        margin-top:10px; 
        padding:20px;
        border:1px solid #ddd; 
        background:#fff;  
    }

    .pageContentContainer:after {
        content:''; 
        display:block;
        clear:both; 
    }

    .pageFooter {
        width:100%; 
        height:30px; 
        background:#E94646; 
        color:#fff; 
        text-align:center; 
        padding-top:3px; 
    }

    .pageFooter > a {
        color:#fff; 
        text-decoration:none;
    }

    .pageFooter > a:hover {
        text-decoration:underline; 
        color:#fff; 
    }

    .button-ok {
        padding:10px 20px; 
        border:1px solid #ddd; 
        cursor:pointer; 
    }

    .button-right {
        float:right; 
    }

    .alert {
        width:100%; 
        padding:10px 15px; 
        background:rgba(0,0,0,0.1); 
        border:1px solid rgba(0,0,0,0.2); 
        margin-top:5px; 
        margin-bottom:7px; 
        border-radius:2px; 
    }

    .alert-success {
        background:#2ECC40; 
        color:#fff; 
    }

    .alert-danger {
        background:#FF4136; 
        color:#fff; 
    }

    
</style>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8" />
        <link rel="shortcut icon" href="https://www.loisboard.at/media/upload/Lbicon.png" type="image/png" />
        <title>(Beta) LoisBoard Installation</title>
    </head>
    <body>
        <?php echo $html; ?> 
    </body>
</html>