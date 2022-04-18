<?php

class Users
{

    public static function updatesecret($newsecret, $id, $oldsecret)
    {
        $stmt = DB::run("UPDATE `users` SET `secret` =?, `status` =? WHERE `id` =? AND `secret` =? AND `status` =?", [$newsecret, 'confirmed', $id, $oldsecret, 'pending']);
        $count = $stmt->rowCount();
        return $count;
    }

    public static function updateUserEmailResetEditsecret($email, $id, $editsecret)
    {
        DB::run("UPDATE `users` SET `editsecret` =?, `email` =? WHERE `id` =? AND `editsecret` =?", ['', $email, $id, $editsecret]);
    }

    public static function getEditsecret($id)
    {
        $row = DB::run("SELECT `editsecret` FROM `users` WHERE `enabled` =? AND `status` =? AND `editsecret` !=?  AND `id` =?", ['yes', 'confirmed', '', $id])->fetch();
        return $row;
    }

    // Update User pass & secret
    public static function recoverUpdate($wantpassword, $newsec, $pid, $psecret)
    {
        $row = DB::run("UPDATE `users` SET `password` =?, `secret` =? WHERE `id`=? AND `secret` =?", [$wantpassword, $newsec, $pid, $psecret]);
    }

    // Function That Removes All From An Account
    public static function deleteuser($userid)
    {
        DB::delete('users', ['id' => $userid]);
        DB::delete('warnings', ['userid' => $userid]);
        DB::delete('ratings', ['user' => $userid]);
        DB::delete('peers', ['userid' => $userid]);
        DB::delete('completed', ['userid' => $userid]);
        DB::delete('reports', ['addedby' => $userid]);
        DB::delete('reports', ['votedfor' =>$userid, 'type' => 'user']);
        DB::delete('forum_readposts', ['userid' => $userid]);
        DB::delete('pollanswers', ['userid' => $userid]);
        DB::delete('snatched', ['uid' => $userid]);
    }

    public static function coloredname($id, $array = null)
    {
        $classy = DB::run("SELECT u.class, u.id, u.username, u.last_access, u.country, u.age, u.gender, u.avatar,  u.donated, u.warned, u.enabled, g.Color, g.level, u.uploaded, u.downloaded FROM `users` `u` INNER JOIN `groups` `g` ON g.group_id=u.class WHERE id ='" . $id . "'")->fetch();

        if ($classy) {
            $gcolor = $classy['Color'];
            if ($classy['donated'] > 0) {
                $star = "<i class='fa fa-star' aria-hidden='true' style='color:orange' title='Donated'></i>";
            } else {
                $star = "";
            }
            if ($classy['warned'] == "yes") {
                $warn = "<img src='" . URLROOT . "/assets/images/warn.png' alt='Warn' border='0'>";
            } else {
                $warn = "";
            }
            if ($classy['enabled'] == "no") {
                $disabled = "<img src='" . URLROOT . "/assets/images/disabled.png' title='Disabled' border='0'>";
            } else {
                $disabled = "";
            }

            $name = htmlspecialchars($classy["username"]);
            
            $avatar = htmlspecialchars($classy["avatar"]);
            if (!$avatar) {
                $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
            }

            if ($array != null && Users::get('view_users') == 'yes') {
                return stripslashes("<a href='".URLROOT."/profile?id=".$classy['id']."'  onMouseover=\"return overlib('<table class=ballooncolor border=1 width=300px align=center><tr valign=top><td class=ballooncolor align=center><img border=0 height=200 width=120 src=$avatar></td><td width=80%  class=ballooncolor><div align=left><b>Class: </b><font color=" . $gcolor . ">" . $classy["level"] . "</font><br /><b>Donated: </b>" . $classy["donated"] . "<br /><b>Age: </b>" . $classy["age"] . "<br /><b>Last Visit: </b><font color=green>" . TimeDate::utc_to_tz($classy["last_access"]) . "</font><br /><b>Downloaded: </b><font color=red>" . mksize($classy["downloaded"]) . "</font><br /><b> Uploaded: </b>" . mksize($classy['uploaded']) . "</div></td></tr></table>', CENTER, HEIGHT, 200, WIDTH, 300)\"; onMouseout=\"return nd()\"><font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font></a>");
            } else {
                return stripslashes("<a href='".URLROOT."/profile?id=".$classy['id']."'><font color='" . $gcolor . "'>" . $name . "" . $star . "" . $warn . "" . $disabled . "</font></a>");
            }

        } else {
            return 'System';
        }
    }

    public static function where($where, $userid, $update = 1)
    {
        if (!Validate::ID($userid)) {
            die;
        }
        if (empty($where)) {
            $where = "Unknown Location...";
        }
        if ($update) {
            DB::update('users', ['page'=>$where], ['id'=>$userid]);
        }
        if (!$update) {
            return $where;
        } else {
            return;
        }
    }

    public static function echouser($id)
    {
        if ($id != '') {
            $username = DB::column('users', 'username', ['id'=>$id]);
            $user = "<option value=\"$id\">$username</option>\n";
        } else {
            $user = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        }
        $stmt = DB::all('users', '*', '');
        foreach ($stmt as $arr) {
            $user .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
        }
        echo $user;
    }

	public static function get($name)
    {
        global $CURRENTUSER;
		if (isset($CURRENTUSER[$name])) {
            $CURRENTUSER['loggedin'] = true;
            return $CURRENTUSER[$name];
        } else {
            return false;
        }
	}

	// Login If User Allowed
    public static function login($username, $password)
    {
        // Select User
        $sql = DB::raw('users', 'id, password, secret, status, enabled', ['username' => $username])->fetch();
        
        // Run Some Checks
        if (!$sql || !password_verify($password, $sql['password'])) {
            Redirect::autolink(URLROOT . "/logout", Lang::T("LOGIN_INCORRECT"));
        } elseif ($sql['status'] == "pending") {
            Redirect::autolink(URLROOT . "/logout", Lang::T("ACCOUNT_PENDING"));
        } elseif ($sql['enabled'] == "no") {
            Redirect::autolink(URLROOT . "/logout", Lang::T("ACCOUNT_DISABLED"));
        }

        // Set Cookies
        Cookie::setAll($sql['id'], $sql['password']);
        
        // Update User
        DB::update('users', ['last_login' => TimeDate::get_date_time()], ['id' => $sql['id']]);
        
        // Success Visit Home
        Redirect::to(URLROOT);
	}

    
    public static function search($search, $class, $letter) {
        $q = $query = null;
        if ($search) {
            $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
            if ($search) {
                $q = "search=" . htmlspecialchars($search);
            }
        } elseif ($letter) {
            if (strlen($letter) > 1) {
                unset($letter);
            }
            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                unset($letter);
            } else {
                $query = "username LIKE '$letter%' AND status='confirmed'";
            }
            $q = "letter=$letter";
        }
        if (!$query) {
            $query = "status='confirmed'";
        }
        if (!$class) {
            unset($class);
        } else {
            $query .= " AND class=$class";
            $q .= ($q ? "&amp;" : "") . "class=$class";
        }

        $var = ['query'=>$query, 'q'=>$q];

        return $var;
    }
}