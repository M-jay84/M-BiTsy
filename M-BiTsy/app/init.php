<?php
// Start Session
session_start();

// Load Config
require_once 'config/config.php';
$config = require_once 'config/settings.php';

// Load Langauge
$language = isset($_SESSION['language'])  ? $_SESSION['language'] : 'english';
require_once LANG."/$language.php";

// Error Reporting
error_reporting(0); // error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//ini_set('error_log', '../data/logs/errors_log.txt');

// Register custom exception handler
include "helpers/exception_helper.php";
set_exception_handler("handleUncaughtException");
//set_error_handler('runtime_error_handler');

// Load Debug
require_once 'helpers/debug_helper.php';

// Load TimeZones/Smileys Array
require_once 'helpers/tzs_helper.php';
require "helpers/smileys.php";

// Load Helpers
require "helpers/general_helper.php";
require "helpers/forum_helper.php";
require "helpers/format_helper.php";
require "helpers/comment_helper.php";
require "helpers/torrent_helper.php";
require "helpers/bbcode_helper.php";
require "helpers/nfo_helper.php";

// Autoload Models
spl_autoload_register(function ($model){
    $filename2 = APPROOT."/models/$model.php";
    if(file_exists($filename2)) {
        require_once APPROOT."/models/$model.php";
    }
});

// Autoload Classes
spl_autoload_register(function ($class){
    $filename = APPROOT."/libraries/$class.php";
    if(file_exists($filename)) {
        require_once APPROOT."/libraries/$class.php";
    }
});