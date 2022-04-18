<?php

class Style
{

    // Default Header
    public static function header($title = "")
    {
        if (!isset($_SESSION['loggedin'])) {
            Guests::guestadd();
        }
        if ($title == "") {
            $title = Config::get('SITENAME');
        } else {
            $title = Config::get('SITENAME') . " : " . htmlspecialchars($title);
        }
        require_once "assets/themes/" . (Users::get('stylesheet') ?: Config::get('DEFAULTTHEME')) . "/header.php";
    }
    
    // Default Footer
    public static function footer()
    {
        require_once "assets/themes/" . (Users::get('stylesheet') ?: Config::get('DEFAULTTHEME')) . "/footer.php";
    }
    
    // Default Block Header
    public static function begin($caption = "-")
    {
        $blockId = 'f-' . sha1($caption);
        ?>
        <div class="card">
            <div class="card-header text-center frame-header">
                <?php echo $caption ?>
                <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
            </div>
            <div class="card-body frame-body slidingDiv<?php echo $blockId; ?>">
        <?php
    }
    
    // Default Block Footer
    public static function end()
    {
        ?>
            </div>
        </div>
        <div class="block-footer"></div>
        <?php
    }


    // Default Admin Header
    public static function adminheader($title = "")
    {
        if ($title == "") {
            $title = Config::get('SITENAME');
        } else {
            $title = Config::get('SITENAME') . " : " . htmlspecialchars($title);
        }
        require_once APPROOT . "/views/admin/admincp/header.php";
    }
    
    // Default Admin Footer
    public static function adminfooter()
    {
        require_once APPROOT . "/views/admin/admincp/footer.php";
    }

    // Default Admin Home
    public static function adminnavmenu()
    {
        //Get Last Cleanup
        $row = DB::column('tasks', 'last_time', ['task'=>'cleanup']);
        if (!$row) {
            $lastclean = "never done...";
        } else {
            $lastclean = TimeDate::get_elapsed_time($row);
        } ?><br>
        <div class="card w-100 ">
        <div class="border ttborder">
        <?php
        echo "<center>Last cleanup performed: " . $lastclean . " ago [<a href='" . URLROOT . "/admintask/cleanup'><b>" . Lang::T("FORCE_CLEAN") . "</b></a>]</center>";

        $row = DB::run("SELECT VERSION() AS version")->fetch();
        $mysqlver = $row['version'];
        function apache_version()
        {
            $ver = explode(" ", $_SERVER["SERVER_SOFTWARE"], 3);
            return ($ver[0] . " " . $ver[1]);
        }
        $newstaffmessage = get_row_count("staffmessages", "WHERE answered = '0'");
        echo "<center><b>" . Lang::T("New Staff Messages") . ":</b> <a href='" . URLROOT . "/admincontact'><b>($newstaffmessage)</b></a></center>";
        $pending = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        echo "<center><b>" . Lang::T("USERS_AWAITING_VALIDATION") . ":</b> <a href='" . URLROOT . "/Adminuser/confirm'><b>($pending)</b></a></center>";
        echo "<center>" . Lang::T("VERSION_MYSQL") . ": <b>" . $mysqlver . "</b>&nbsp;-&nbsp;" . Lang::T("VERSION_PHP") . ": <b>" . phpversion() . "</b>&nbsp;-&nbsp;" . Lang::T("Apache Version") . ": <b>" . apache_version() . "</b></center>";
        echo "<center><a href=" . URLROOT . "/admintask/cache><b>Purge Cache</b></a><br></center>";
        echo '</div></div><br>';
    }

    // Default Sidebar Block Header
    public static function block_begin($caption = "-")
    {
        $blockId = 'b-' . sha1($caption); ?>
        <div class="card">
            <div class="card-header text-center block-header">
                <?php echo $caption ?>
                <a data-toggle="collapse" href="#" class="showHide" id="<?php echo $blockId; ?>" style="float: right;"></a>
            </div>
            <div class="card-body block-body slidingDiv<?php echo $blockId; ?>"> <?php
    }

    // Default Sidebar Block Footer
    public static function block_end()
    {
            ?>
            </div>
        </div>
        <div class="block-footer"></div> <?php
    }

    // Default Error Header
    public static function error_header($title = "")
    {
        if ($title == "") {
            $title = Config::get('SITENAME');
        } else {
            $title = Config::get('SITENAME') . " : " . htmlspecialchars($title);
        }
        require_once APPROOT . "/views/user/error/error_header.php";
    }
    
    // Default Error Footer
    public static function error_footer()
    {
        require_once APPROOT . "/views/user/error/error_footer.php";
    }
    
}