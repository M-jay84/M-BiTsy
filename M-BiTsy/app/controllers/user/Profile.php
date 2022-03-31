<?php
class Profile
{
    public function __construct()
    {
        Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USER_ID"));
        }
        // can view own but not others
        if (Users::get("view_users") == "no" && Users::get("id") != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        $user = DB::raw('users', '*', ['id'=>$id])->fetch();
        if (!$user) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_WITH_ID") . " $id.");
        }
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && Users::get("edit_users") == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_ACCESS_ACCOUNT_DISABLED"));
        }

        // Start Blocked Users
        $blocked = DB::raw('friends', 'id', ['userid'=>$user['id'], 'friend'=>'enemy', 'friendid'=>Users::get('id')]);
        $show = $blocked->rowCount();
        if ($show != 0 && Users::get("control_panel") != "yes") {
            Redirect::autolink(URLROOT, "You're blocked by this member and you can not see his profile!");
        }

        $country = Countries::getCountryName($user['country']);
        $userratio = $user["downloaded"] > 0 ? number_format($user["uploaded"] / $user["downloaded"], 1) : "---";
        $numtorrents = get_row_count("torrents", "WHERE owner = $id");
        $numcomments = get_row_count("comments", "WHERE user = $id");
        $numforumposts = get_row_count("forum_posts", "WHERE userid = $id");
        $numhnr = DB::column('snatched', 'COUNT(`hnr`)', ['uid'=>$id]);
        $avatar = htmlspecialchars($user["avatar"]) ? htmlspecialchars($user["avatar"]) : URLROOT . "/assets/images/misc/default_avatar.png";
        $usersignature = stripslashes($user["signature"]);
        
        // Friend/Block List
        $arr = Friends::countFriendAndEnemy(Users::get('id'), $id);
        $friend = $arr['friend'];
        $block = $arr['enemy'];

        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));

        $user1 = DB::all('users', '*', ['id'=>$id]);
        
        $data = [
            'title' => $title,
            'id' => $id,
            'friend' => $friend,
            'block' => $block,
            'country' => $country,
            'userratio' => $userratio,
            'numhnr' => $numhnr,
            'avatar' => $avatar,
            'numtorrents' => $numtorrents,
            'numcomments' => $numcomments,
            'numforumposts' => $numforumposts,
            'usersignature' => $usersignature,
            'selectuser' => $user1,
        ];
        View::render('profile/index', $data, 'user');
    }

    public function edit()
    {
        $id = (int) Input::get("id");

        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }

        $user = DB::raw('users', '*', ['id'=>$id])->fetch();
        
        $stylesheets = Stylesheets::getStyleDropDown($user['stylesheet']);
        $countries = Countries::pickCountry($user['country']);
        $tz = TimeDate::timeZoneDropDown($user['tzoffset']);
        $teams = Teams::dropDownTeams($user['team']);
        $gender = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . Lang::T("MALE") . "</option>\n"
                . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . Lang::T("FEMALE") . "</option>\n";

        $user1 = DB::all('users', '*', ['id'=>$id]);

        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));

        $data = [
            'title' => $title,
            'stylesheets' => $stylesheets,
            'countries' => $countries,
            'teams' => $teams,
            'tz' => $tz,
            'gender' => $gender,
            'id' => $id,
            'selectuser' => $user1,
        ];
        View::render('profile/edit', $data, 'user');
    }

    public function submit()
    {
        $id = (int) Input::get("id");

        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }

        if (Input::exist()) {
            $data = [
            'privacy' => $_POST["privacy"],
            'notifs' => $_POST["pmnotif"] == 'yes' ? "[pm]" : "",
            'stylesheet' => $_POST["stylesheet"],
            'client' => $_POST["client"],
            'age' => $_POST["age"],
            'gender' => $_POST["gender"],
            'country' => $_POST["country"],
            'team' => $_POST["teams"],
            'avatar' => $_POST["avatar"],
            'title' => $_POST["title"],
            'hideshoutbox' => ($_POST["hideshoutbox"] == "yes") ? "yes" : "no",
            'tzoffset' => (int) $_POST['tzoffset'],
            'signature' => $_POST["signature"]
            ];
            
            if ($_POST['resetpasskey']) {
                $data = ['passkey' => ''];
            }
    
            DB::update("users", $data, ['id'=>$id]);
            Redirect::autolink(URLROOT . "/profile/edit?id=$id", Lang::T("User Edited"));
        }
    }

    public function admin()
    {
        $id = (int) Input::get("id");

        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }

        $user1 = DB::raw('users', '*', ['id'=>$id])->fetch();
        
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));
        $user = DB::all('users', '*', ['id'=>$id]);

        $data = [
            'id' => $id,
            'title' => $title,
            'selectuser' => $user,
        ];
        View::render('profile/admin', $data, 'user');
    }

    public function submited()
    {
        $id = (int) Input::get("id");

        if (Users::get('class') < 5 && $id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("You dont have permission"));
        }

        if (!Validate::Email(Input::get("email"))) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("EMAIL_ADDRESS_NOT_VALID"));
        }

        $data = [
            'downloaded' => strtobytes(Input::get("downloaded")),
            'uploaded' => strtobytes(Input::get("uploaded")),
            'ip' => Input::get("ip"),
            'class' => (int) Input::get("class") ?? 0,
            'donated' => (float) Input::get("donated"),
            'password' => Input::get("password"),
            'warned' => Input::get("warned"),
            'downloadbanned' => Input::get("downloadbanned"),
            'shoutboxpos' => Input::get("shoutboxpos"),
            'modcomment' => Input::get("modcomment"),
            'enabled' => Input::get("enabled"),
            'invites' => (int) Input::get("invites"),
            'email' => Input::get("email"),
            'seedbonus' => Input::get("bonus"),
        ];
        
        if ($data['class'] != 0 && $data['class'] != Users::get('class')) {
            // change user class
            $arr = DB::raw('users', 'class', ['id' => $id])->fetch();
            $uc = $arr['class'];
            // skip if class is same as current
            if ($uc != $data['class'] && $uc > Users::get('class')) {
                Redirect::autolink(URLROOT . "/admin?id=$id", Lang::T("YOU_CANT_DEMOTE_YOURSELF"));
            } elseif ($uc == Users::get('class')) {
                Redirect::autolink(URLROOT . "/admin?id=$id", Lang::T("YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"));
            } else {
                DB::update('users', ['class' =>$data['class']], ['id' => $id]);
                // Notify user
                $prodemoted = ($data['class'] > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . Groups::get_user_class_name($data['class']) . " by " . Users::get("username") . "";
                DB::insert('messages', ['sender'=>0, 'receiver'=>$id, 'added'=>TimeDate::get_date_time(), 'msg'=>$msg]);
            }
        }
        
        // Reset Passkey Check
        if (Input::get('resetpasskey') == 'yes') {
            $data = ['passkey' =>''];
        }
        // Change Password
        $chgpasswd = Input::get('chgpasswd') == 'yes' ? true : false;
        if ($chgpasswd) {
            $passres = DB::raw('users', 'password', ['id' => $id])->fetch();
            if ($data['password'] != $passres['password']) {
                $password = password_hash($data['password'], PASSWORD_BCRYPT);
                $data = ['password' =>$password];
                Logs::write(Users::get('username') . " has changed password for user: $id");
            }
        }

        DB::update("users", $data, ['id'=>$id]);
        Logs::write(Users::get('username') . " has edited user: $id details");
        Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("User Edited"));
    }

    public function delete()
    {
        $userid = (int) Input::get("userid");
        $username = Input::get("username");
        $delreason = Input::get("delreason");

        if (Users::get("delete_users") != "yes") {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("TASK_ADMIN"));
        }
        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("INVALID_USERID"));
        }
        if (Users::get("id") == $userid) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", "Staff cannot delete themself. Please PM a admin.");
        }
        if (!$delreason) {
            Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("MISSING_FORM_DATA"));
        }

        Users::deleteuser($userid);
        Logs::write(Users::get('username') . " has deleted account: $username");
        Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("USER_DELETE"));
    }

}