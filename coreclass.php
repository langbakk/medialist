<?php

class Core
{
    public $dbh; // handle of the db connexion
    // public $sqlerror; // handle of potential db-errors
    private static $instance;

    private function __construct()
    {
        // building data source name from config
        $dsn = 'mysql:host=' . Config::read('db.host') .
               ';dbname='    . Config::read('db.name') .
               ';port='      . Config::read('db.port') .
               ';connect_timeout=15';
        // getting DB user from config                
        $user = Config::read('db.username');
        // getting DB password from config                
        $password = Config::read('db.password');

        $setUTF8 = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); //comment out this or change the character encoding if your database is not set to UTF-8 character encoding
        
        function displayErrors($result) {
            $sqlerror = ($result->errorCode() > 0) ? $result->errorInfo() : '';
            return $sqlerror[2];
        }

        try {
            $this->dbh = new PDO($dsn, $user, $password, $setUTF8);
        } catch(PDOException $e) {
            echo '<p class="messagebox error">'.$e->getMessage().'</p>';
        } 

    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }

     // others global functions
}

class Config
{
    static $confArray;

    public static function read($name)
    {
        return self::$confArray[$name];
    }

    public static function write($name, $value)
    {
        self::$confArray[$name] = $value;
    }

}

class PageView
{
    //private $page;

    function __construct() {
//        $isloggedin = Config::read('user.loggedin');
        $this->page = !empty($_GET['page']);
        $this->subpage = isset($_GET['subpage']);
    }

    function runSetup() {
        if (file_exists('config.php')) {
            // unlink('config.php');
        // $core = Core::getInstance();
        }
        // $hash = Config::read('hash');
        if (is_dir('setup')) {
            $path = "setup/";
            include $path."index.php";
        }
    }

    function getLogin() {
        $core = Core::getInstance();
  //      $userrole_name = Config::read('user.role.name');
    //    $hash = Config::read('hash');
      //  $todaydate = Config::read('todaydate');
        // $sessiontimeout = Config::read('user.timeout');
        $isloggedin = Config::read('isloggedin');
        //$register_user = Config::read('register_user');
        //$forgottenpassword = Config::read('forgottenpassword');
        //$system_user_id = Config::read('system_user_id');
        $baseurl_page = Config::read('baseurl_page');
//        if ($register_user == 1) {
    //        include 'addusers.php';
  //      } elseif ($forgottenpassword == 1) {
      //      include 'forgottenpassword.php';
        //} else
        if (empty($isloggedin)) {
            include 'login.php';
        } else {
           include 'login.php';            
        }
    }

    function getPage() {

            // $chosenlanguage = Config::read('chosenlanguage');
            // $hash = Config::read('hash');
            $isloggedin = Config::read('isloggedin');
            $show_quotes = Config::read('showquotes');
            $use_db = Config::read('usedb');
            $use_login = Config::read('uselogin');
            // $isadmin = Config::read('user.admin');
            // $sessiontimeout = Config::read('user.timeout');
            // $userid = Config::read('user.id');
            // $activeuser = Config::read('activeuser');
            // $usergroup = Config::read('user.group');
            // $usergroup_name = Config::read('user.group.name');
            // $userrole = Config::read('user.role');
            // $userrole_name = Config::read('user.role.name');
            // $usertype = Config::read('user.type');
            // $usertype_name = Config::read('user.type.name');
            // $userlanguage = Config::read('user.language');
            // $fullname = Config::read('fullname');
            $username = Config::read('username');
            // $shiftleader = Config::read('shiftleader');
            // $workalone = Config::read('workalone');
            $domain = Config::read('domain');
            $rootfolder = Config::read('rootfolder');
            $baseurl = Config::read('baseurl');
            $baseurl_page = Config::read('baseurl_page');
            $scriptpath = Config::read('scriptpath');
            $imagepath = Config::read('imagepath');
            $webgfxpath = Config::read('webgfxpath');
            $userpath = Config::read('userpath');
            $current_page = Config::read('current_page');
            $sub_page = Config::read('sub_page');
            $sub_page_url = Config::read('sub_page_url');
            // $main_support_email = Config::read('main_support_email');
            // $forgottenpassword = Config::read('forgottenpassword');
            // $register_user = Config::read('register_user');

            // $system_user_id = Config::read('system_user_id');

            // $year = Config::read('year');
            // $month = Config::read('month');
            // $nextmonthurl = Config::read('nextmonthurl');
            // $previousmonthurl = Config::read('previousmonthurl');
            // $currentday = Config::read('currentday');
            // $currentweek = Config::read('currentweek');
            // $currentmonth = Config::read('currentmonth');
            // $currentyear = Config::read('currentyear');
            // $maxyear = Config::read('maxyear');
            // $minyear = Config::read('minyear');
            // $daysinmonth = Config::read('daysinmonth');
            // $date = Config::read('date');
            // $norwegiandate = Config::read('norwegiandate');
            // $time = Config::read('time');
            // $todaydate = Config::read('todaydate');
            // $day_array = Config::read('dayarray');
            // $month_array = Config::read('montharray');

            // $location = Config::read('location');
            // $locationurl = Config::read('locationurl');

            $core = Core::getInstance();

$page = "";
$subpage = "";
$sqlcore = "";
            if (!empty($this->page)) {
                if (is_dir($_GET['page'])) {
                    $path = "".$_GET['page']."/";
                } else {
                    $path = "";
                }
                if (!empty($this->subpage)) {
                    $subpage = $_GET['subpage'];
                }                
                $page = $_GET['page'];
                if (file_exists($path.$subpage.".php")) {
                    include $path.$subpage.".php";
                } elseif (file_exists($path.$page.".php")) {
                    include $path.$page.".php";
                } elseif (!empty($this->page) && !file_exists($path.$page.".php")) {
                    // echo "elseif";
                    // $sqlcore = "SELECT * FROM persdb_content WHERE content_page = '$page' AND (group_access = '$usergroup' OR group_access = 0) AND language = '$userlanguage'";
                }
            } else {
                // echo "else";
                    // $sqlcore = "SELECT * FROM persdb_content WHERE content_page = '$page' AND (group_access = '$usergroup' OR group_access = 0) AND language = '$userlanguage'";
            }
                    //DEBUG echo $page; echo $sqlcore;

                    // $core = Core::getInstance();
                    $stmt = $core->dbh->prepare($sqlcore);              
                    $stmt->bindParam(':page', $page, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $rowscount = $stmt->rowCount();
                        if ($rowscount == 0) {
                            echo "<article>";
                            echo "<div>";
                            echo "<p>".__NOACCESS."</p>";
                            echo "</div>";
                            echo "</article>";
                        } elseif ($rowscount != 0) {
                            while ($row = $stmt->fetch()) { 
                                echo "<article>";
                                if ($row['edit_access'] > $userrole) {
                                echo '<div class="edit" data-type="textarea"><div class="adminbuttons"><a href="#" id="'.$row['id'].'" class="editbutton confirm"></a><a href="#" class="deletebutton confirm"></a></div>';
                                } else {
                                echo '<div>';
                                }
                                echo "<h2>".$row['content_heading']."</h2>";
                                echo parseDBText($row['content'], $userrole);
                                
                                echo '</div>';
                                echo "</article>";
                            }
                        }
                    }
                
            
}
}


//Config-variable
Config::write('isloggedin', $isloggedin);

Config::write('domain',$domain);
Config::write('rootfolder', $rootfolder);
Config::write('baseurl',$baseurl);
Config::write('baseurl_page',$baseurl_page);
Config::write('scriptpath', $scriptpath);
Config::write('imagepath', $imagepath);
Config::write('webgfxpath', $webgfxpath);
Config::write('userpath',$userpath);
Config::write('current_page', $current_page);
Config::write('sub_page', $sub_page);
Config::write('sub_page_url', $sub_page_url);

Config::write('username',$username);
Config::write('showquotes', $show_quotes);
Config::write('usedb', $use_db);
Config::write('uselogin', $use_login);

Config::write('db.host', $dbhost);
Config::write('db.port', $dbport);
Config::write('db.name', $dbname);
Config::write('db.username', $dbusername);
Config::write('db.password', $dbpassword);

?>