<?php
if (!session_id()) { session_start(); };

$websitename = 'Uploadr.io';

$workplacename = 'Uploadr.io';

ini_set('display_errors',1); // this should be commented out in production environments
error_reporting(E_ALL); // this should be commented out in production environments

// This file, in conjunction with coreclass.php, language.php and functions.php is the main core behind the site.
// This file contains most of the config-parameters, and the coreclass.php-file contains the actual implementation of the configuration-settings found in this file.
// The language.php-file contains the language-settings, arrays for language-specific settings etc.
// Functions.php contains functions used for minor page-content retrieval, for instance getting files from the script and css-folders, and providing the menu etc.

// Config.php should be loaded on every page of the site

// override settings in the php.ini file
//ini_set('session.cookie_lifetime','3600'); //set the time-to-live for the sessions - if this isn't set, the time-out for the sessions will be the default setting in php.ini

// if this is set, it trumps the session-timeout in php.ini

// DB-information
// Set the hostname for your database - either a direct IP-address, or a domain name
$dbhost = '';
// If a custom port is needed to connect to the database, set the value here
$dbport = '';
// Set the database name
$dbname = '';
// Set the username for accessing the database
$dbusername = '';
// Set the password for accessing the database - if no password is set, leave it blank
$dbpassword = '';
// Set the database's table-prefix, in case you have multiple databases in one instance, or just want to differentiate between tables
$prefix = '';

$dbprefix = $prefix.'_';

// $_SESSION / user settings  --  DO NOT MODIFY THIS
$user_array = file_exists($_SERVER['DOCUMENT_ROOT'].'/conf/.userlist') ? file($_SERVER['DOCUMENT_ROOT'].'/conf/.userlist',FILE_IGNORE_NEW_LINES) : '';

$userpath = 'users/';
$username = (isset($_SESSION['loggedin'])) ? $_SESSION['username'].'/' : 'public/';
$usertype = (isset($_SESSION['usertype'])) ? $_SESSION['usertype'] : 'user';

$allow_public = true;

$countrycode = !empty($_SESSION['userlanguage']) ? $_SESSION['userlanguage'] : 187;

$forgottenpassword = (isset($_GET['page']) && $_GET['page'] == 'forgottenpassword') ? 1 : '';
$register_user = (isset($_GET['page']) && $_GET['page'] == 'adduser') ? 1 : '';
$deactivated = 0;

$use_login = true;
$isloggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
$show_quotes = true;
$use_db = false;
$debug = true;
$filesize_units = explode(' ', 'B KB MB GB TB PB');
$total_filesize_limit = 536870912; //0; 512MB //GB
// Session-control
// The inactive variable controls how many seconds an active session lasts - if you want another value, replace this with the amount. Value in seconds Take note to change the value in the persdb.js file as well - the value for count
$inactive = '600';
$session_life = (isset($_SESSION['timeout'])) ? $_SERVER['REQUEST_TIME'] - $_SESSION['timeout'] : '';

$unique_key = '';
 //A unique key used in encryption/decryption functions.
define('UNIQUE_KEY',$unique_key);
$method = 'AES-256-CBC';

// Path-configuration
$document_root = $_SERVER['DOCUMENT_ROOT'];
$httptype = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
$domain = ($_SERVER['SERVER_PORT'] == 80 || $_SERVER["SERVER_PORT"] == 443) ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
// leave rootfolder as / if installed in root webfolder
$rootfolder = '/';
$baseurl = $httptype.'://'.$domain.$rootfolder.'';
// $baseurl = $rootfolder;
$conf_folder = 'conf/'; // here's all the configuration-files
$confpath = $document_root.$rootfolder.$conf_folder;
$csspath = $baseurl.'css/'; // here's all the CSS-files
$script_folder = 'script/'; // here's all the js-files
$scriptpath = $document_root.$rootfolder.$script_folder; // file-path to the script-folder
$scriptwebpath = $baseurl.$script_folder; // web-url for the script-folder
$uploadspath = 'files/';
$uploadswebpath = $baseurl.'files/';
$imagepath = $document_root.$rootfolder.$uploadspath.'images/'; // file path to the image folder
$imagewebpath = $uploadswebpath.'images/'; // web-url for the image-folder
$documentpath = $document_root.$rootfolder.$uploadspath.'documents/'; // file path to the documents folder
$documentwebpath = $uploadswebpath.'documents/'; // web-url for the documents folder
$calendarpath = $document_root.$rootfolder.$uploadspath.'calendars/'; // file path to the calendars folder
$calendarwebpath = $uploadswebpath.'calendars/'; // web-url for the calendars folder
$webgfxpath = $baseurl.'webgfx/';
$translations_folder = $document_root.$rootfolder.'translations/';
$translations = $baseurl.$translations_folder;
$logfolder = 'logs/';
define('LOG_FOLDER', $document_root . $rootfolder . $logfolder);
define('LOG_SQL_ERROR', LOG_FOLDER . 'sqlerror_log.txt');
$baseurl_page = $baseurl.'index.php?page=';
$current_page = (!empty($_GET['page']) && strpos($_GET['page'],'/')) ? explode('/',$_GET['page'])[0] : (!empty($_GET['page']) ? $_GET['page'] : '');
$sub_page = (!empty($_GET['subpage'])) ? $_GET['subpage'] : '';
$sub_page_url = (!empty($_GET['subpage'])) ? '&amp;subpage='.$sub_page : '';

// Default values inserted into the pages - contact-emails etc.
// note that the main_support_email has to be used inside a link tag if you want it to be clickable
$main_support_email = 'webmaster@uploadr.io';

// System-specific settings
$system_user_id = '000';

//External information
$visitoripaddress = $_SERVER['REMOTE_ADDR'];

// Date-settings and controls for the timesheet-functionality
$year = (!empty($_GET['year'])) ? $_GET['year'] : '';
$month = (!empty($_GET['month'])) ? $_GET['month'] : '';
$day = (!empty($_GET['day'])) ? $_GET['day'] : '';
$location = (!empty($_GET['location'])) ? $_GET['location'] : '';

$nextmonth = sprintf('%02d', $month+1);
$previousmonth = sprintf('%02d', $month-1);

$nextmonthurl = $baseurl_page.$current_page.$sub_page_url.'&year='.$year.'&month='.$nextmonth.'&location='.$location;
$previousmonthurl = $baseurl_page.$current_page.$sub_page_url.'&year='.$year.'&month='.$previousmonth.'&location='.$location;

$locationurl = $baseurl_page.$current_page.$sub_page_url.'&year='.$year.'&month='.$month.'&location=';

$currentday = date('d');
$currentweek = date('W');
$currentmonth = date('m');
$currentyear = date('Y');

$maxyear = date('Y', strtotime('+20 years'));
$minyear = date('Y', strtotime('-20 years'));

if (empty($year) || empty($month)) {
$daysinmonth = '30';
}
elseif (!empty($year) && !empty($month)) {
$daysinmonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
};

$date = date('Y-m-d');
$norwegiandate = date('d / m / Y');
$time = date('H:i:s');
$todaydate = $date.' '.$time;

$currentsemesteryear = $currentyear;
$currentsemestershortcode = ($currentsemesteryear.'-08-01' > $date) ? 'v' : 'h';

if (file_exists($document_root.$rootfolder.'coreclass.php')) {
	require_once($document_root.$rootfolder.'coreclass.php');	
}

?>
