<?php

class Profile
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Profile Default Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USER_ID"));
        }
        
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

        // Get User Data
        $user1 = DB::all('users', '*', ['id'=>$id]);
        
        // Init Data
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

        // Load View
        View::render('profile/index', $data, 'user');
    }

    // Edit Profile Default Page
    public function edit()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }

        // Get User Data
        $user = DB::raw('users', '*', ['id'=>$id])->fetch();
        $stylesheets = Stylesheets::getStyleDropDown($user['stylesheet']);
        $countries = Countries::pickCountry($user['country']);
        $tz = TimeDate::timeZoneDropDown($user['tzoffset']);
        $teams = Teams::dropDownTeams($user['team']);
        $gender = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . Lang::T("MALE") . "</option>\n"
                . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . Lang::T("FEMALE") . "</option>\n";

        $user1 = DB::all('users', '*', ['id'=>$id]);

        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));

        // Init Data
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

        // Load View
        View::render('profile/edit', $data, 'user');
    }

    // Edit Profile Form Submit
    public function submit()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }

        if (Input::exist()) {
            // Init Data
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
                $rand = array_sum(explode(" ", microtime()));
                $passkey = md5(Users::get('username') . $rand . Users::get('secret') . ($rand * mt_rand()));
                $data['passkey'] = $passkey;
            }
    
            // Update
            DB::update("users", $data, ['id'=>$id]);
            Redirect::autolink(URLROOT . "/profile/edit?id=$id", Lang::T("User Edited"));
        }
    }

    // Admin Edit Profile Default Page
    public function admin()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if (Users::get('class') < _MODERATOR && $id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"));
        }

        // Get User Data
        $user1 = DB::raw('users', '*', ['id'=>$id])->fetch();
        
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($id));
        $user = DB::all('users', '*', ['id'=>$id]);

        // Init Data
        $data = [
            'id' => $id,
            'title' => $title,
            'selectuser' => $user,
        ];

        // Load View
        View::render('profile/admin', $data, 'user');
    }

    // Admin Edit Profile Form Submit
    public function submited()
    {
        // Check User Input
        $id = (int) Input::get("id");

        if (!Validate::Email(Input::get("email"))) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("EMAIL_ADDRESS_NOT_VALID"));
        }

        // Check User
        if (Users::get('class') < 5 && $id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("You dont have permission"));
        }
        
        // Init Data
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
        
        // Change User Class
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
                DB::insert('messages', ['sender'=>0, 'receiver'=>$id, 'added'=>TimeDate::get_date_time(), 'subject'=>'Group Change', 'msg'=>$msg]);
            }
        }
        
        // Reset Passkey Check
        if (Input::get('resetpasskey') == 'yes') {
            $rand = array_sum(explode(" ", microtime()));
            $passkey = md5(Users::get('username') . $rand . Users::get('secret') . ($rand * mt_rand()));
            $data = ['passkey' => $passkey];
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

        // Update
        DB::update("users", $data, ['id'=>$id]);
        Logs::write(Users::get('username') . " has edited user: $id details");
        Redirect::autolink(URLROOT . "/profile?id=$id", Lang::T("User Edited"));
    }

    // Delete Profile Default Page
    public function delete()
    {
        // Check User Input
        $userid = (int) Input::get("userid");
        $delreason = Input::get("delreason");
        $sure = Input::get("sure") ?? 0;

        // Check User
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

        if ($sure == "0") {
            Redirect::autolink(URLROOT . "/profile/delete", "Sanity check: You are about to delete user. Click <a href='" . URLROOT . "/profile/delete?sure=1'>here</a> if you are sure.");
        }

        // Delete Profile
        Users::deleteuser($userid);
        Logs::write(Users::get('username') . " has deleted account: " . Users::coloredname($userid) . "");
        Redirect::autolink(URLROOT . "/profile?id=$userid", Lang::T("USER_DELETE"));
    }

}