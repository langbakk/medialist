<?php

if (file_exists($processpath.'functions.php')) {
    require_once($processpath.'functions.php');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/config.php')) {
    class Core {
        public $dbh; // handle of the db connection
        private static $instance;

        private function __construct() { // building data source name from config
            $dsn = 'mysql:host=' . Config::read('dbhost') .
            ';dbname='      . Config::read('dbname') .
            ';port='      . Config::read('dbport') .
            ';connect_timeout=15';
            $user = Config::read('dbusername'); // getting DB user from config
            $password = Config::read('dbpassword'); // getting DB password from config
            $dbname = Config::read('dbname');

            $setAttributes = [
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //sets the default fetch mode to associative
                                1002 => 'SET NAMES utf8', //remove or change this if your database is not set to UTF-8
                                PDO::ATTR_EMULATE_PREPARES => false,
                                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                                // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                // PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING //only activate this if there are problems with the script and unknown database-errors
                                ];

            function displayErrors($result, $output = false, $origin_file = false, $date = false)  {
                if ($result == false) return '';
                if (!$result->errorCode()) return '';
                $sqlError = $result->errorInfo();
                $settings = Settings::getInstance();
                $errorcontent = '';
                if ($settings->getSetting('sqlerror_log') == 1 || $settings->getSetting('sqlerror_log') == false) {
                    switch ($sqlError[0]) {
                        case 'HY093';
                        $errorcontent = 'Invalid parameter number - ';
                        break;
                        case 'HY000';
                        $errorcontent = 'General error - HY000 - '.(!empty($sqlError[2]) ? $sqlError[2] : $sqlError[1]);
                        break;
                        default:
                        $errorcontent = !empty($sqlError[2]) ? $sqlError[2] : $sqlError[1];
                        break;
                    }
                    $filename = ((!empty($origin_file) !== false)) ? $origin_file : 'file not specfiied:'.__FILE__;
                    logThis('sqlerror_log',Config::read('todaydate').' '.$_SERVER['REQUEST_URI'].' ::-:: '.$filename.' - '.__LINE__.' - '.$result->queryString.' --- '.$sqlError[0].' - '.$errorcontent.$sqlError[2]);
                }
                if (!empty($errorcontent)) {
                    return $output ? '<p class="messagebox error visible">'.$errorcontent.'</p>' : $errorcontent;
                }
            } // function displayErrors

            try {
                $this->dbh = new PDO($dsn, $user, $password, $setAttributes);
            } catch(PDOException $e) {
                $filename = ((!empty($origin_file) !== false)) ? $origin_file : __FILE__;
                logThis('sqlerror_log',Config::read('todaydate').' '.$_SERVER['REQUEST_URI'].' ::-:: '.$filename.' - '.__LINE__.' - '.$e->getMessage());
                return false;
            }
        }

        public static function getInstance() {
            if (!isset(self::$instance)) {
                $object = __CLASS__;
                self::$instance = new $object;
            }
            return self::$instance;
        } // end getInstance

    } // end Core
}

class Config {
    static $confArray;

    public static function readAll() {
        if (isset($confArray)) {
            foreach (self::$confArray as $key => $value) {
                self::$confArray[$key] = $value;
            }
        }
        return self::$confArray;
    }
    public static function read($name) {
        return self::$confArray[$name];
    }
    public static function write($name, $value) {
        self::$confArray[$name] = $value;
    }
}

class MyRecursiveFilterIterator extends RecursiveFilterIterator {
    public static $FILTERS = [
        '__MACOSX',
        '.DS_Store',
        '.gitignore',
        '.htaccess',
        'thumbs'
    ];

    public function accept() {
        return !in_array(
            $this->current()->getFilename(),
            self::$FILTERS,
            true
        );
    }
}


class valueCrypt {
    private static $instance;

    private function __construct() {
        $this->valueCrypt = '';
    }

    public static function vC_encrypt($value) {
        $key = Config::read('unique_key');
        $method = Config::read('method');
        $iv = bin2hex(openssl_random_pseudo_bytes(8));
        return [openssl_encrypt($value,$method,$key,0,$iv),$iv];
    }

    public static function vC_decrypt($value,$iv,$name = '',$id = '') {
        $key = Config::read('unique_key');
        $method = Config::read('method');
        $returnvalue = openssl_decrypt($value,$method,$key,0,$iv);
        if (!empty($returnvalue)) {
            return $returnvalue;
        } else {
            logThis('yourprofile_nin',$name.' -- NIN not found, and not able to extract correct information -- UserID: '.$id);
        }
    }

    public static function vC_pwHash($value,$db_value = '') {
        $password = false;
        if (empty($db_value)) {
            $password = password_hash($value,PASSWORD_DEFAULT);   
        } else {
            $password = password_verify($value,$db_value);
        }
        return $password;
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object();
        }
        if (!empty(self::$error)) {
            return self::$error;
        }
        return self::$instance;
    } // end getInstance
}

class PageView {
    function __construct() {
        $this->page = !empty($_GET['page']);
        $this->subpage = isset($_GET['subpage']);
    }

    function runSetup() {
        // if (file_exists('conf/config.php')) {
        //     // unlink('conf/config.php');
        // }
        if (is_dir('setup')) {
            $path = 'setup/';
            include $path.'index.php';
        }
    }

    function getLogin() {
        foreach (Config::readAll() as $key => $value) {
            $$key = $value;
        }
		if (empty($isloggedin)) {
            include 'login.php';
        } else {
            include 'logout.php';            
        }
    }

    function getPage() {
        foreach (Config::readAll() as $key => $value) {
            $$key = $value;
        }

        $page = '';
        $subpage = '';
        $sqlcore = '';
            if (!empty($this->page)) {
                if (is_dir($_GET['page'])) {
                    $path = ''.$_GET['page'].'/';
                } else {
                    $path = '';
                }
                if (!empty($this->subpage)) {
                    $subpage = $_GET['subpage'];
                }                
                $page = $_GET['page'];
                if (file_exists($path.$subpage.'.php')) {
                    include $path.$subpage.'.php';
                } elseif (file_exists($path.$page.'.php')) {
                    include $path.$page.'.php';
                } elseif (!empty($this->page) && !file_exists($path.$page.'.php')) {
                    // $sqlcore = "SELECT * FROM persdb_content WHERE content_page = '$page' AND (group_access = '$usergroup' OR group_access = 0) AND language = '$userlanguage'";
                }
            } else {
                // $sqlcore = "SELECT * FROM persdb_content WHERE content_page = '$page' AND (group_access = '$usergroup' OR group_access = 0) AND language = '$userlanguage'";
            }
                // $core = Core::getInstance();
                // $stmt = $core->dbh->prepare($sqlcore);              
                // $stmt->bindParam(':page', $page, PDO::PARAM_STR);

                // if ($stmt->execute()) {
                //     $rowscount = $stmt->rowCount();
                //     if ($rowscount == 0) {
                //         echo "<article>";
                //         echo "<div>";
                //         echo "<p>".__NOACCESS."</p>";
                //         echo "</div>";
                //         echo "</article>";
                //     } elseif ($rowscount != 0) {
                //         while ($row = $stmt->fetch()) { 
                //             echo "<article>";
                //             if ($row['edit_access'] > $userrole) {
                //             echo '<div class="edit" data-type="textarea"><div class="adminbuttons"><a href="#" id="'.$row['id'].'" class="editbutton confirm"></a><a href="#" class="deletebutton confirm"></a></div>';
                //             } else {
                //             echo '<div>';
                //             }
                //             echo "<h2>".$row['content_heading']."</h2>";
                //             echo parseDBText($row['content'], $userrole);
                            
                //             echo '</div>';
                //             echo "</article>";
                //         }
                //     }
                // }


    }
}

// this loads the config.php-file, parses the variables in it, and writes them to Config
$getconfigvars = (file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/config.php') ? file($_SERVER['DOCUMENT_ROOT'].'/conf/config.php', FILE_IGNORE_NEW_LINES) : (file_exists($_SERVER['DOCUMENT_ROOT'].'/setup/config.sample.php') ? file($_SERVER['DOCUMENT_ROOT'].'/setup/config.sample.php', FILE_IGNORE_NEW_LINES) : ''));

$lines_in_configfile = (file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/config.php') ? count(file($_SERVER['DOCUMENT_ROOT'].'/conf/config.php', FILE_IGNORE_NEW_LINES)) : (file_exists($_SERVER['DOCUMENT_ROOT'].'/setup/config.sample.php') ? count(file($_SERVER['DOCUMENT_ROOT'].'/setup/config.sample.php', FILE_IGNORE_NEW_LINES)) : ''));

    for ($i=0; $i < $lines_in_configfile; $i++) {
        if (strstr($getconfigvars[$i], '=')) {
            if (substr($getconfigvars[$i], 0, 1) == '$') {
                $first = strstr($getconfigvars[$i], '=', true);
                $name = str_replace('$','',rtrim($first));
                Config::write(''.$name.'',${$name}); // this writes each config-variable to the Config::array
            }
        }
    }

?>