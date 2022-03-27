<?php
// DB Details
define("DB_HOST", "localhost");
define("DB_USER", "dbusername");
define("DB_PASS", "dbpassword");
define("DB_NAME", "dbname");
define('DB_CHAR', 'utf8mb4');
// URL Root
define('URLROOT', 'http://localhost/M-BiTsy');
// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// Paths
define('BACUP', '../data/backups');
define('CACHE', '../data/cache'); // Cache dir (only used if type is "disk"). Must be CHMOD 777
define('LANG', '../data/languages');
define('LOGGER', '../data/logs');
define('UPLOADDIR', '../data/uploads');
// Quick Time
define('TT_TIME', time());
define('TT_DATE', date("Y-m-d H:i:s"));
// Version
define('VERSION', 'PDO');
// File Charset
define('CHARSET', 'utf-8');
// Announcelist //seperate via comma
define('ANNOUNCELIST', URLROOT . '/announce.php');
// Passkey Url
define('PASSKEYURL', URLROOT . '/announce.php?passkey=%s');
// Can edit Settings
define('_OWNERS', array('M-jay')); // Example & with more define('_OWNERS', array('mjay', 'mjay', 'mjay'));
// Image upload settings
define('IMAGEMAXFILESIZE', 524288); // Max uploaded image size in bytes (Default: 512 kB)
define('ALLOWEDIMAGETYPES', array(
    "image/gif" => ".gif",
    "image/pjpeg" => ".jpg",
    "image/jpeg" => ".jpg",
    "image/jpg" => ".jpg",
    "image/png" => ".png",
));
// Hide Blocks On Pages
define('ISURL', array('login', 'logout', 'signup', 'contact', 'recover', 'recover/confirm'));
// category,name,dl,magnet,uploader,tube,tmdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating
define('TORRENTTABLE_COLUMNS', 'category,name,dl,magnet,uploader,tube,tmdb,comments,nfo,size,completed,seeders,leechers,health,external,added,speed,wait,rating,admin');
// Set User Group
define('_USER', 1);
define('_POWERUSER', 2);
define('_VIP', 3);
define('_UPLOADER', 4);
define('_MODERATOR', 5);
define('_SUPERMODERATOR', 6);
define('_ADMINISTRATOR', 7);
// Cat arrays
define('PopularCats', array(28)); // popular cat id for popular block
define('MovieCats', array('Anime')); // movie cats eg array('Movies','MoviesHD')
define('SerieCats', array('Apps')); // serie cat eg array('Anime','tv')
// TMDB
define('_TMDBAPIKEY', 'Place_Api_Key_Here'); // Place Key Here ////////
define('_TMDBLANG', 'en'); // TMDB Language
define('_TMDBDEBUG', true); // TMDB Debug