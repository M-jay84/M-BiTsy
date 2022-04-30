<?php

class Shoutbox
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }

    // No Default Page
    public function index()
    {
        Redirect::to(URLROOT);
    }

    // Ajax Shoutbox Load
    public function chat()
    {
        $result = DB::raw('shoutbox', '*', ['staff' => 0], ' ORDER BY msgid DESC LIMIT 25');
        ?>
        <div class='shoutbox_contain'><table class='table table-striped'>
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
            <a class="pull-left"><b><?php echo Users::coloredname($row['userid']) ?>:</b></a>&nbsp;
            <?php echo nl2br(format_comment($row['message'])); ?>
            <?php
            if (Users::get("edit_users") =="yes") {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/delete?id=" . $row['msgid'] . "&sure=1'><i class='fa fa-remove' ></i></a>&nbsp";
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=" . $row['msgid'] . "''><i class='fa fa-pencil' ></i></a>&nbsp";
            }
            if (Users::get("edit_users") =="no" && Users::get('username') == $row['user']) {
                $ts = TimeDate::modify('date', $row['date'], "+1 day");
                if ($ts > TT_DATE) {
                echo "&nbsp<a href='" . URLROOT . "/shoutbox/edit?id=$row[msgid]&user=$row[userid]'><i class='fa fa-pencil' ></i></a>&nbsp";
                }
            } ?>
            </td>

            </tr>
            <?php 
        } ?>
        </table></div>
        <?php
    }

    // Add Shout Submit
    public function add()
    {
        if (Users::get("shoutboxpos") != 'yes' && $_SESSION['loggedin']) {
            //INSERT MESSAGE
            if (!empty(Input::get('message')) && $_SESSION['loggedin'] == true) {
                // Check User Input
                $message = Input::get('message');
                
                $row = Shoutboxs::checkFlood($message, Users::get('username'));
                if ($row[0] == '0') {
                    DB::insert('shoutbox', ['userid'=>Users::get('id'), 'date'=>TimeDate::get_date_time(), 'user'=>Users::get('username'), 'message'=>$message]);
                }
            }
        } else {
            Redirect::autolink(URLROOT, Lang::T("Shoutbox Banned"));
        }
    }

    // Delete Shout Submit
    public function delete()
    {
        // Check User Input
        $delete = Input::get('id');
        $sure = Input::get("sure");

        if ($sure == "1") {
            Redirect::autolink(URLROOT, "Sanity check: Click <a href='".URLROOT."/shoutbox/delete?id=$delete'>here</a> if you are sure.");
        }

        if ($delete) {
            if (is_numeric($delete)) {
                $row = DB::raw('shoutbox', '*', ['msgid'=>$delete])->fetch(PDO::FETCH_LAZY);
            } else {
                echo "Failed to delete, invalid msg id";
                exit;
            }
            if ($row && (Users::get("edit_users") == "yes" || Users::get('username') == $row[1])) {
                Logs::write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . Users::get('username') . "</b>");
                DB::delete('shoutbox', ['msgid'=>$delete]);
            }
        }
        Redirect::to(URLROOT);
    }

    // Edit Shout Default Page
    public function edit()
    {
        // Check User Input
        $user = Input::get('user');
        if (Users::get('class') > _UPLOADER || Users::get('id') == $user) {
            // Check User Input
            $id = Input::get('id');
            $message = $_POST['message'];

            // Update
            if ($message) {
                DB::update('shoutbox', ['message'=>$message], ['msgid'=>$id]);
                Redirect::autolink(URLROOT, Lang::T("Message edited"));
            }
            
            // Get Shoutbox Data
            $edit = DB::raw('shoutbox', '*', ['msgid'=>$id])->fetch(PDO::FETCH_LAZY);
            
            // Init Data
            $data = [
                'title' => 'Edit',
                'id' => $edit['msgid'],
                'message' => $edit['message'],
                'user' => $edit['userid'],
            ];

            // Load View
            View::render('shoutbox/edit', $data, 'user');
        } else {
            Redirect::autolink(URLROOT . '/logout', Lang::T("NO_PERMISSION"));
        }
    }

    // Shoutbox History Default Page
    public function history()
    {
        // Get Shoutbox Data
        $result = DB::raw('shoutbox', '*', ['staff' => 0], ' ORDER BY msgid DESC LIMIT 60');
        
        // Init Data
        $data = [
            'title' => 'History',
            'sql' => $result,
        ];

        // Load View
        View::render('shoutbox/history', $data, 'user');
    }

}