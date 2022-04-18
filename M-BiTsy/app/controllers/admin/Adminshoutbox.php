<?php

class Adminshoutbox
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Shoutbox Default Page
    public function index()
    {
        // Init Data
        $data = [
            'title' => 'Staff Chat',
        ];

        // Load View
        View::render('shoutbox/index', $data, 'admin');
    }

    // Shoutbox History Default Page
    public function history()
    {
        // Get Shoutbox Data
        $result = DB::select('shoutbox', '*', ['staff' =>1], 'ORDER BY msgid DESC LIMIT 80');

        // Init Data
        $data = [
            'title' => 'Staff History',
            'sql' => $result,
        ];

        // Load View
        View::render('shoutbox/history', $data, 'admin');
    }

    // Ajax Shoutbox Load
    public function loadchat()
    {
        // Get Shoutbox Data
        $result = DB::raw('shoutbox', '*', ['staff' =>1], 'ORDER BY msgid DESC LIMIT 20');
        ?>
        <div class='shoutbox_contain'><table class='table table-striped'>
        <tr>
        <?php
        while ($row = $result->fetch(PDO::FETCH_LAZY)) {
            $ol3 = DB::select('users', 'avatar', ['id'=>$row["userid"]]);
            $av = $ol3['avatar'];
            if (!empty($av)) {
                $av = "<img src='" . $ol3['avatar'] . "' alt='my_avatar' width='20' height='20'>";
            } else {
                $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.png' alt='my_avatar' width='20' height='20'>";
            }
            if ($row['userid'] == 0) {
                $av = "<img src='" . URLROOT . "/assets/images/misc/default_avatar.png' alt='default_avatar' width='20' height='20'>";
            }
            ?>
            
            <tr>
            <td class="shouttable">
            <small class="pull-left time d-none d-sm-block" style="width:99px;font-size:11px"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('jS M,  g:ia', TimeDate::utc_to_tz_time($row['date'])); ?></small>
            <a class="pull-left d-none d-sm-block"><?php echo $av ?></a>&nbsp;
            <a class="pull-left"><b><?php echo Users::coloredname($row["userid"]) ?>:</b></a>&nbsp;
            <?php echo nl2br(format_comment($row['message'])); ?>
            </td>
            </tr>
            
            <?php 
        } ?>
        
        </table></div>
        <?php
    }

    // Add Shout Form Submit
    public function add()
    {
        if (Users::get("shoutboxpos") == 'no') {
            //INSERT MESSAGE
            if (!empty($_POST['message']) && $_SESSION['loggedin'] == true) {
                $_POST['message'] = $_POST['message'];

                $row = DB::run("SELECT COUNT(*) FROM shoutbox WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) < ?", [$_POST['message'], Users::get('username'), TimeDate::get_date_time(), 30])->fetchColumn();
                if ($row == '0') {
                    DB::insert('shoutbox', ['msgid'=>0, 'user'=>Users::get('username'), 'message'=>$_POST['message'], 'date'=>TimeDate::get_date_time(), 'userid'=>Users::get('id'), 'staff'=>1]);
                }
            }
        }
        Redirect::to(URLROOT . '/adminshoutbox');
    }

    // Clear Shoutbox Default Page
    public function clear()
    {
        // Check User Input
        $do = $_GET['do'];

        // Clear All Shouts
        if ($do == "delete") {
            DB::truncate('shoutbox');
            Logs::write("Shoutbox cleared by ".Users::get('username')."");
            $msg_shout = "[color=#ff0000]" . Lang::T("SHOUTBOX_CLEARED_MESSAGE") . "[/color]";
            DB::insert('shoutbox', ['userid'=>0, 'date'=>TimeDate::get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
            Redirect::autolink(URLROOT . "/admincp", "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
        }

        // Init Data
        $data = [
            'title' => Lang::T("CLEAR_SHOUTBOX"),
        ];

        // Load View
        View::render('shoutbox/clear', $data, 'admin');
    }

}