<?php
$php_recommended_settings = array(
    array('Safe Mode', 'safe_mode', 'OFF'),
    array('Display Errors (Can be off, but does make debugging difficult.)', 'display_errors', 'ON'),
    array('File Uploads', 'file_uploads', 'ON'),
    array('Register Globals', 'register_globals', 'OFF'),
    array('Output Buffering', 'output_buffering', 'OFF'),
    array('Session auto start', 'session.auto_start', 'OFF'),
    array('allow_url_fopen (Required for external torrents)', 'allow_url_fopen', 'ON'),
);

// Check PHP ini Settings
if (isset($_GET["phpinfo"]) == 1) {
    echo "<br /><center><a href='check.php'>Back To Check</a></center><br>";
    phpinfo();
    die();
}

function get_php_setting($val)
{
    $r = (ini_get($val) == '1' ? 1 : 0);
    return $r ? 'ON' : 'OFF';
}

function writableCell($folder, $relative = 1, $text = '')
{
    $writeable = '<b><font color="green">Writeable</font></b>';
    $unwriteable = '<b><font color="red">Unwriteable</font></b>';

    echo '<tr>';
    echo '<td>' . $folder . '</td>';
    echo '<td align="right">';
    if ($relative) {
        echo is_writable("./$folder") ? $writeable : $unwriteable;
    } else {
        echo is_writable("$folder") ? $writeable : $unwriteable;
    }
    echo '</td>';
    echo '</tr>';
}

require_once "../app/config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET ?>">
    <link href="assets/themes/default/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
      
      <p class="text-center"><b>Config Check</b></p>
      <p class="text-center"><button type="button" class="btn btn-warning" onclick="window.location=window.location">Check Again</button></p>
      <p class="text-center"><a href="check.php?phpinfo=1"><b>PHPInfo</b></a></p>
      <p class="text-center"><a href='index.php'><b>Return to homepage</b></a></p>
      
      <p class="text-center"><b>Required Settings Check</b></p>
      <p class="text-center">If any of these items are highlighted in red then please take actions to correct them. <br />
                             Failure to do so could lead to your installation not functioning correctly.</p>
      
      <div class="d-flex justify-content-center">
          <table cellpadding="3" cellspacing="1" style="border-collapse: collapse" border="1">
              <tr>
                  <td>&nbsp; - PHP version >= 8</td>
                  <td> <?php echo phpversion() < '8' ? '<b><font color="red">No</font> 8 or above required</b>' : '<b><font color="green">Yes</font></b>';
                  echo " - Your PHP version is " . phpversion(); ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - PDO Ext</td>
                 <td><?php echo extension_loaded('PDO') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - sockets support</td>
                 <td><?php echo extension_loaded('sockets') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - check mb support</td>
                 <td><?php echo extension_loaded('mbstring') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - zlib compression support</td>
                 <td><?php echo extension_loaded('zlib') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - XML support</td>
                 <td><?php echo extension_loaded('xml') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - curl support (Not required but external torrents may scrape faster)</td>
                 <td><?php echo function_exists('curl_init') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - openSSL (for the torrent encryption mod)</td>
                 <td><?php echo extension_loaded('openssl') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>&nbsp; - bcmath support (Required for IPv6)</td>
                 <td><?php echo extension_loaded('bcmath') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
             </tr>
             <tr>
                 <td>app/config/config.php</td>
                 <td> <?php
                 if (@file_exists('../config/config.php') && @is_writable(APPROOT.'/config/config.php')) {
                    echo '<b><font color="red">Writeable</font></b><br />Warning: leaving app/config/config.php writeable is a security risk';
                 } else {
                    echo '<b><font color="green">Unwriteable</font></b>';
                 } ?>
                 </td>
             </tr>
             <tr>
                 <td>Document Root<br /><i><font size="1">(Use this for your PATHS in app/config/config.php)</font></i></td>
                 <td><?php echo str_replace('\\', '/', getcwd()) ?></td>
             </tr>
        </table>

     </div><br>
     
     <p class="text-center"><b>These settings are recommended for PHP in order to ensure full compatibility!.</b></p>
     <p class="text-center">However, it will still operate if your settings do not quite match the recommended.</p>

    <div class="d-flex justify-content-center">
        <table cellpadding="3" cellspacing="1" style="border-collapse: collapse" border="1">
            <tr>
            <td width="500px">Directive</td>
            <td>Recommended</td>
            <td>Actual</td>
            </tr> <?php
            foreach ($php_recommended_settings as $phprec) { ?>
               <tr>
                   <td><?php echo $phprec[0]; ?>:</td>
                   <td><?php echo $phprec[2]; ?>:</td>
                   
                   <td><b> <?php
                   if (get_php_setting($phprec[1]) == $phprec[2]) { ?>
                      <font color="green"> <?php
                   } else { ?>
		              <font color="red"> <?php
                   }
                   echo get_php_setting($phprec[1]); ?>
                   </font></b></td>
              </tr> <?php
           } ?>
        </table>
    </div><br>
    
    <p class="text-center"><b>Directory and File Permissions Check:</b></p>
    
    <div class="d-flex justify-content-center">
        <table cellpadding="3" cellspacing="1" style='border-collapse: collapse' border="1" > <?php
        writableCell('uploads/avatars');
        writableCell('uploads/thumbnail'); ?>
        </table>
    </div><br>
    
    <p class="text-center"><b>DB Connection Check:</b></p> <?php
    
    try {
        $link = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "<p class='text-center'><font color='#ff0000'><b>Failed to connect to database:</b></font></p> (%d) %s<br />" . $e->getMessage();
    }
    echo "<p class='text-center'><font color='#00cc00'><b>Database Connect </b></font></p>";
    
    echo "<p class='text-center'><b>Check SQL Mode:</b><p>";
    $stmt = $link->query("SHOW VARIABLES LIKE 'sql_mode'")->fetch();
    if (!$stmt) {
        echo "<p class='text-center'><font color='#ff0000'><b>SQL Mode:</b></font>&nbsp;&nbsp;";
        echo "Error Getting SQL Mode</p><br><br>";
    } else {
        echo "<p class='text-center'><font color='#00cc00'><b>SQL Mode:</b></font>&nbsp;&nbsp;";
        echo $stmt[1]."</p><br><br>";
    }
    
    echo "<p class='text-center'><b>Table Status Check:</b><p>"; ?>
    
    <div class="d-flex justify-content-center"> <?php
    
$stmt = $link->prepare("SHOW TABLES");
$stmt->execute();
if (!$stmt) {
    echo "<font color='#ff0000'><b>Failed to list tables:</b></font>";
} else {
    $tables = array();
    while ($rr = $stmt->fetch()) {
        $tables[] = $rr[0];
    }
    $arr = ["addedrequests","attachments","bans","bookmarks","bonus","blocks","categories","censor","clients","comments","completed"
            ,"countries","email_bans","faq","files","forumcats","forum_posts","forum_forums","forum_readposts"
            ,"forum_topics","friends","groups","guests","iplog","languages","likes","log","messages","news"
            ,"peers","pollanswers","polls","ratings","reports","requests","rules","snatched","shoutbox","staffmessages"
            ,"stylesheets","tasks","teams","thanks","tmdb","torrentlang","torrents","users","warnings"];
    echo "<table cellpadding='3' cellspacing='1' style='border-collapse: collapse' border='1'>";
    echo "<tr><th>Table</th><th>Status</th></tr>";
    foreach ($arr as $t) {
        if (!in_array($t, $tables)) {
            echo "<tr><td>$t</td><td align='right'><font color='#ff0000'><b>MISSING</b></font></td></tr>";
        } else {
            echo "<tr><td>$t</td><td align='right'><font color='green'><b>OK</b></font></td></tr>";
        }
    }
    echo "</table>";
}
$link = null; ?>
   
   </div>

  <p class="text-center"><a href="https://torrenttrader.uk" target="_blank">torrenttrader.uk</a> -|- <a href="https://github.com/M-jay84/M-BiTsy" target="_blank">M-jay</a></p>

  </body>
</html>