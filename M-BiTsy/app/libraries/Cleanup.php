<?php

class Cleanup
{
    // Automatic System Update Function
    public static function autoclean()
    {
        $row = DB::select('tasks', 'last_time', ['task'=>'cleanup']);
        if (!$row) {
            DB::insert('tasks', [ 'task'=>'cleanup','last_time'=>TimeDate::gmtime()]);
            return;
        }

        if ($row['last_time'] + Config::get('AUTOCLEANINTERVAL') > TimeDate::gmtime()) {
            return;
        }

        $planned_clean = DB::run("UPDATE tasks SET last_time=? WHERE task=? AND last_time =?", [TimeDate::gmtime(), 'cleanup', $row['last_time']]);
        if (!$planned_clean) {
            return;
        }

        self::run();
    }

    // Run Cleanup Operations
    public static function run() {
        self::deletepeers();
        self::makeinvisible();
        self::bonus();
        self::vipuntil();
        self::pendinguser();
        self::deletelogs();
        self::freeleech();
        if (Config::get('RATIOWARNENABLE')) {
            self::ratiowarn();
        }
        self::expiredwarn();
        self::iswarned();
        self::autoinvite();
        if (Config::get('HNR_ON')) {
            self::hitnrun();
        }
        //self::autopromote(); todo
    }

    // LOCAL TORRENTS - DELETE OLD NON-ACTIVE PEERS
    public static function deletepeers()
    {
        $deadtime = TimeDate::get_date_time(TimeDate::gmtime() - Config::get('ANNOUNCEINTERVAL'));
        DB::run("DELETE FROM peers WHERE last_action < ?", [$deadtime]);
    }

    // LOCAL TORRENTS - MAKE NON-ACTIVE/OLD TORRENTS INVISIBLE
    public static function makeinvisible()
    {
        $deadtime = TimeDate::gmtime() - Config::get('MAXDEADTORRENTTIMEOUT');
        DB::run("UPDATE torrents SET visible=?
                 WHERE visible=? AND last_action < FROM_UNIXTIME(?) AND seeders = ? AND leechers = ? AND external !=?",
                 ['no', 'yes', $deadtime, 0, 0, 'yes']);
    }

    // Set Bonus every hour
    public static function bonus()
    {
        $row = DB::select('tasks', 'last_time', ['task'=>'bonus']);
        if (!$row) {
            DB::insert('tasks', [ 'task'=>'bonus','last_time'=>TimeDate::gmtime()]);
        }
        if ($row['last_time'] + Config::get('ADDBONUS') < TimeDate::gmtime()) {
            $res1 = DB::run("SELECT DISTINCT userid as peer, (
                                SELECT DISTINCT COUNT( torrent )
                             FROM peers
                             WHERE seeder = ?  AND userid = peer) 
                             AS count
                             FROM peers WHERE seeder = ?", ['yes', 'yes'])->fetchAll();
            foreach ($res1 as $row) {
                DB::run("UPDATE users SET seedbonus = seedbonus + '" . (Config::get('BONUSPERTIME') * $row['count']) . "' WHERE id = ?", [$row['peer']]);
                DB::update(' tasks', ['last_time'=>TimeDate::gmtime()], ['task'=>'bonus']);
            }
        }
    }

    // Reset VIP Until
    public static function vipuntil()
    {
        $rowv1 = DB::run("SELECT id, oldclass FROM users WHERE vipuntil < ? AND oldclass != ?", [TimeDate::get_date_time(), 0])->fetchAll();
        foreach ($rowv1 as $rowv) {
            DB::insert('messages', ['sender'=>0, 'receiver'=>$rowv['id'], 'added'=>TimeDate::get_date_time(), 'subject'=> 'Your VIP class stay has just expired', 'msg'=>'Your VIP class stay has just expired']);
            DB::run("UPDATE users SET class =?, oldclass=?, vipuntil =? WHERE vipuntil < ?", [$rowv['oldclass'], 0, null, TimeDate::get_date_time()]);
        }
    }

    // DELETE PENDING USER ACCOUNTS OVER TIMOUT AGE
    public static function pendinguser()
    {
        $deadtime = TimeDate::gmtime() - Config::get('SIGNUPTIMEOUT');
        DB::run("DELETE FROM users WHERE status = ? AND added < FROM_UNIXTIME(?)", ['pending', $deadtime]);
    }

    // Delete Old Logs
    public static function deletelogs()
    {
        $ts = TimeDate::gmtime() - Config::get('LOGCLEAN')  * 86400;
        DB::run("DELETE FROM log WHERE added < FROM_UNIXTIME(?)", [$ts]);
    }

    // Set Free Leech
    public static function freeleech()
    {
        if (Config::get('FREELEECHGBON'));{
            $query = DB::run("SELECT `id`, `name` FROM `torrents` WHERE `banned` = ? AND `freeleech` = ? AND `size` >= ?", ['no', 0, Config::get('FREELEECHGB')])->fetchAll();
            if ($query) {
                foreach ($query as $row) {
                    DB::insert('torrents', ['freeleech'=>1], ['id'=>$row['id']]);
                    Logs::write("Freeleech added on  <a href='torrent?id=$row[id]'>$row[name]</a> because it is bigger than " . Config::get('FREELEECHGB') . "");
                }
            }
        }
    }

    // LEECH WARN USERS WITH LOW RATIO
    public static function ratiowarn()
    {
        $downloaded = Config::get('RATIOWARN_MINGIGS') * 1024 * 1024 * 1024;
        // ADD RATIO WARNING
        $res = DB::run("SELECT id,username FROM users WHERE class <= ? AND warned = ? AND enabled= ? AND uploaded / downloaded < ? AND downloaded >= ?", [_UPLOADER, 'no', 'yes', Config::get('RATIOWARNMINRATIO'), $downloaded])->fetchAll();
        if ($res) {
            $reason = "You have been warned because of having low ratio. You need to get a " . Config::get('RATIOWARNMINRATIO') . " before next " . Config::get('RATIOWARN_DAYSTOWARN') . " days or your account may be banned.";
            $expiretime = gmdate("Y-m-d H:i:s", TimeDate::gmtime() + (86400 * Config::get('RATIOWARN_DAYSTOWARN')));
            foreach ($res as $arr) {
                DB::insert('warnings', ['userid'=>$arr['id'], 'reason'=>$reason, 'added'=>TimeDate::get_date_time(),'expiry'=>$expiretime,'warnedby'=>0,'type'=>'Poor Ratio']);
                DB::insert('users', ['warned'=>'yes'], ['id'=>$arr["id"]]);
                DB::insert('messages', ['sender'=>0, 'receiver'=>$arr['id'], 'added'=>TimeDate::get_date_time(), 'msg'=> $reason]);
                Logs::write("Auto Leech warning has been <b>added</b> for: <a href='" . URLROOT . "/profile?id=" . $arr["id"] . "'>" . Users::coloredname($arr["id"]) . "</a>");
            }
        }
        // REMOVE RATIO WARNING
        $res1 = DB::run("SELECT users.id, users.username FROM users INNER JOIN warnings ON users.id=warnings.userid
                         WHERE type=? AND active = ? AND warned = ?  AND enabled=? AND uploaded / downloaded >= ? AND downloaded >= ?", ['Poor Ratio', 'yes', 'yes', 'yes', Config::get('RATIOWARNMINRATIO'), $downloaded])->fetchAll();
        if ($res1) {
            $reason = "Your warning of low ratio has been removed. We highly recommend you to keep a your ratio up to not be warned again.\n";
            foreach ($res1 as $arr1) {
                Logs::write("Auto Leech warning has been removed for: <a href='" . URLROOT . "/profile?id=" . $arr1["id"] . "'>" . Users::coloredname($arr1["id"]) . "</a>");
                DB::update(' users', ['warned' => 'no'], ['id' => $arr1['id']]);
                DB::update('warnings', ['expiry' => TimeDate::get_date_time(), 'active' =>'no'], ['userid' => $arr['id']]);
                DB::insert('messages', ['sender'=>0, 'receiver'=>$arr1['id'], 'added'=>TimeDate::get_date_time(), 'msg'=> $reason]);
            }
        }
        // BAN RATIO WARNED USERS
        $res = DB::run("SELECT users.id, users.username, UNIX_TIMESTAMP(warnings.expiry) AS expiry FROM users INNER JOIN warnings ON users.id=warnings.userid
                        WHERE type=? AND active = ? AND class = ? AND enabled=? AND warned = ? AND uploaded / downloaded < ? AND downloaded >= ?", ['Poor Ratio', 'yes', 1, 'yes', 'yes', Config::get('RATIOWARNMINRATIO'), $downloaded])->fetchAll();
        if ($res) {
            foreach ($res as $arr) {
                if (TimeDate::gmtime() - $arr["expiry"] >= 0) {
                    DB::update(' users', ['warned' => 'no', 'enabled' =>'no'], ['id' => $arr['id']]);
                    Logs::write("User <a href='" . URLROOT . "/profile?id=" . $arr["id"] . "'>" . Users::coloredname($arr["id"]) . "</a> has been banned (Auto Leech warning).");
                }
            }
        }
    }

    // Warning Expired
    public static function expiredwarn()
    {
        // REMOVE EXPIRED WARNINGS
        $res = DB::run("SELECT users.id, users.username, warnings.expiry FROM users INNER JOIN warnings ON users.id=warnings.userid
                    WHERE type != ? AND warned = ?  AND enabled=? AND warnings.active = ? AND warnings.expiry < ?", ['Poor Ratio', 'yes', 'yes', 'yes', TimeDate::get_date_time()])->fetchAll();
        if ($res) {
            foreach ($res as $arr1) {
                DB::update(' users', ['warned' => 'no'], ['id' => $arr1['id']]);
                DB::run("UPDATE warnings SET active = ? WHERE userid = ? AND expiry < ?", ['no', $arr1['id'], TimeDate::get_date_time()]);
                Logs::write("Removed warning for $arr1[username]. Expiry: $arr1[expiry]");
            }
        }
    }

    // Set User Warned
    public static function iswarned()
    {
        // UPDATE USERS THAT STILL HAVE ACTIVE WARNINGS
        DB::run("UPDATE users SET warned = 'yes' WHERE warned = 'no' AND id IN (SELECT userid FROM warnings WHERE active = 'yes')");
    }


    // Invite update function
    public static function autoinvites($interval, $minlimit, $maxlimit, $minratio, $invites, $maxinvites)
    {
        $time = TimeDate::gmtime() - ($interval * 86400);
        $minlimit = $minlimit * 1024 * 1024 * 1024;
        $maxlimit = $maxlimit * 1024 * 1024 * 1024;
        $res = DB::run("SELECT id, username, class, invites FROM users WHERE enabled = 'yes' AND status = 'confirmed' AND downloaded >= $minlimit AND downloaded < $maxlimit AND uploaded / downloaded >= $minratio AND warned = 'no' AND UNIX_TIMESTAMP(invitedate) <= $time");
        if ($res->rowCount() > 0) {
            while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                $maxninvites = $maxinvites[$arr['class']];
                if ($arr['invites'] >= $maxninvites) {
                    continue;
                }
                if (($maxninvites - $arr['invites']) < $invites) {
                    $invites = $maxninvites - $arr['invites'];
                }
                DB::run("UPDATE users SET invites = invites+$invites, invitedate = NOW() WHERE id=$arr[id]");
                Logs::write("Gave $invites invites to '$arr[username]' - Class: " . Groups::get_user_class_name($arr['class']) . "");
            }
        }
    }

    // GIVE INVITES ACCORDING TO RATIO/GIGS (max 20)
    public static function autoinvite()
    {
        self::autoinvites(14, 1, 4, 0.90, 2, 20);
    }

    // Set Hit & Runs
    public static function hitnrun()
    {
        $timenow = TimeDate::gmtime();
        $length = Config::get('HNR_DEADLINE') * 86400; // 7 days
        $seedtime = Config::get('HNR_SEEDTIME') * 3600; // 48 hours
        // Update Snatched
        DB::run("UPDATE snatched SET hnr = ? WHERE completed = ? AND hnr = ? AND upload < download AND $timenow - $length > start_time AND $seedtime > last_time AND done=?", ['yes', 'yes', 'no', 'no']);
        DB::run("UPDATE `snatched` SET `hnr` = ? WHERE `hnr` = ? AND upload >= download", ['no', 'yes']);
        DB::run("UPDATE `snatched` SET `hnr` = ? WHERE `hnr` = ? AND last_time >= ?", ['no', 'yes', $seedtime]);
        // Do We Have Snatched
        $a = DB::run("SELECT DISTINCT uid FROM snatched WHERE hnr = ? AND done= ?", ['yes', 'no']);
        if ($a->rowCount() > 0):
            while ($b = $a->fetch(PDO::FETCH_ASSOC)):
                // Check User
                $user = $b['uid'];
                $count = DB::run("SELECT COUNT( hnr ) FROM snatched WHERE uid = ? AND hnr = ?", [$user, 'yes'])->fetchColumn();
                $expiretime = gmdate("Y-m-d H:i:s", $timenow + $length);
                // Check Wrning
                $f = DB::select('warnings', 'type, active', ['userid'=>$user]);
                $type = $f['type'];
                // Warn
                if ($count >= Config::get('HNR_WARN') && $type != "HnR"):
                    $reason = "" . Lang::T("CLEANUP_WARNING_FOR_ACCUMULATING") . " " . Config::get('HNR_WARN') . " H&R.";
                    $subject = "" . Lang::T("CLEANUP_WARNING_FOR_H&R") . "";
                    $msg = "" . Lang::T("CLEANUP_YOU_HAVE_BEEN_WARNEWD_ACCUMULATED") . " " . Config::get('HNR_WARN') . " " . Lang::T("CLEANUP_H&R_INVITE_CHECK_RULE") . "\n[color=red]" . Lang::T("CLEANUP_MSG_WARNING_7_DAYS_BANNED") . "[/color]";
                    $rov = DB::select('users', 'enabled', ['id'=>$user]);

                    if ($rov["enabled"] == "yes"):
                        DB::update(' users', ['warned' => 'yes'], ['id' => $user]);
                        DB::insert('warnings', ['userid'=>$user, 'reason'=>$reason, 'added'=>TimeDate::get_date_time(),'expiry'=>$expiretime,'warnedby'=>0,'type'=>'HnR']);
                        DB::insert('messages', ['sender'=>0, 'receiver'=>$user, 'added'=>TimeDate::get_date_time(),'msg'=>$msg,'subject'=>$subject,'poster'=>1]);
                    endif;
                endif;
                // Unwarn
                if ($count < Config::get('HNR_WARN') && $type == "HnR"):
                    $subject = "" . Lang::T("CLEANUP_REMOVAL_OF_H&R_WARNING") . "";
                    $msg = "" . Lang::T("CLEANUP_YOU_NOW_HAVE_LESS_THAN") . " " . Config::get('HNR_WARN') . " H&R.\n" . Lang::T("CLEANUP_YOUR_WARNING_FOR_H&R_HAS_REMOVED") . "";
                    DB::update(' users', ['warned' => 'no'], ['id' => $user]);
                    DB::delete('warnings', ['userid' =>$user, 'type' => 'HnR']);
                    DB::insert('messages', ['sender'=>0, 'receiver'=>$user, 'added'=>TimeDate::get_date_time(),'msg'=>$msg,'subject'=>$subject,'poster'=>1]);
                endif;
                // Ban
                if ($count >= Config::get('HNR_BAN')):
                    $h = DB::select('users', 'username, email, modcomment', ['id'=>$user]);
                    $modcomment = $h[2];
                    $modcomment = gmdate("d/m/Y") . " - " . Lang::T("CLEANUP_BANNED_FOR") . " " . $count . " H&R.\n " . $modcomment;
                    DB::update(' users', ['enabled' => 'no', 'warned' =>'no','modcomment' =>$modcomment], ['id' => $user]);
                    DB::delete('warnings', ['userid' =>$user, 'type' => 'HnR']);
                    Logs::write(Lang::T("CLEANUP_THE_MEMBER") . " <a href='account-details.php?id=" . $user . "'>" . $h[0] . "</a> " . Lang::T("CLEANUP_HAS_BEEN_BANNED_REASON") . " " . $count . " H&R.");
                    $sitename = URLROOT;
                    $body = file_get_contents(APPROOT . "/views/user/email/hitnrunban.php");
                    $body = str_replace("%count%", $count, $body);
                    $body = str_replace("%sitename%", $sitename, $body);
                    $TTMail = new TTMail();
                    $TTMail->Send($h[1], Lang::T("CLEANUP_HAS_BEEN_DISABLED"), "$body", "From: " . Config::get('SITEEMAIL') . "", "-f" . Config::get('SITEEMAIL') . "");
                endif;
                // Download Ban
                if ($count >= Config::get('HNR_STOP_DL')){
                    DB::update(' users', ['downloadbanned' => 'yes'], ['id' => $user]);
                }
            endwhile;
        endif;
    }
    
    // Set Auto Promotion / Demotion
    public static function autopromote() {
        $minratio = 0.9; # ratio for demotion to LEECHE
        $gigs = 50 * 1073741824; # 50 GB
        $delay2 = TimeDate::get_date_time(TimeDate::gmtime() - 1 * 1); # Joined > 1 month
        
        // auto promote by gb
        $res = DB::run("SELECT id, username FROM users WHERE class = 1 AND uploaded / downloaded > $minratio");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $res_classname = DB::raw('groups', 'level', ['group_id'=>_POWERUSER], 'LIMIT 1');
            if ($res_classname->rowCount() == 1) {
                $arr_classname = $res_classname->fetch(PDO::FETCH_ASSOC);
                $new_classname = "$arr_classname[level]";
            }
            $username = $arr['username'];
            DB::update(' users', ['class' => _POWERUSER], ['id' => $arr['id']]);
            $msgg = '[b]Congratulations[/b], you were automatically promoted to [b]Member[/b] class. Please note that if your ratio drops below [b]" . $minratio . "[/b] at any time,  you will be demoted to [b]Leecher[/b]';
            DB::insert('messages', ['sender'=>0, 'receiver'=>$arr['id'], 'added'=>TimeDate::get_date_time(),'msg'=>$msgg,'subject'=>"You have been promoted as " . $new_classname . ""]);
            unset($res, $arr, $res_classname, $arr_classname,$new_classname);
        }

        // auto demote to leecher
        $res = DB::run("SELECT id, username, modcomment FROM users WHERE class < 4 AND uploaded / downloaded < $minratio");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $username = $arr['username'];
            $modcomment = $arr['modcomment'];
            $modcomment2 = gmdate("d-M-Y") . " - Has been demoted by System to Leecher \n";
            $modcomment = $modcomment2 . "" . $modcomment;
            $modcom = sqlesc($modcomment);
            DB::run("UPDATE users SET class = 1, modcomment = CONCAT($modcom,modcomment) WHERE id = $arr[id]");
            $msgg = 'You were automatically demoted to [b]Leecher[/b]. That happened because your ratio dropped below [b]" . $minratio . "[/b]';
            DB::insert('messages', ['sender'=>0, 'receiver'=>$arr['id'], 'added'=>TimeDate::get_date_time(),'msg'=>$msgg,'subject'=> 'You have been demoted to Leecher']);
            Logs::write("<a href=/account-details.php?id=$arr[id]><b>$username</b></a> has been demoted by System to <b>Leecher</b> class");
        }
        
        // auto promote to class 3
        $res = DB::run("SELECT id, username FROM users WHERE (class = 1 || class = 2) AND warned = 'no' AND added < $delay2 AND uploaded >= $gigs AND uploaded >= downloaded");
        while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
            $res_classname = DB::raw('groups', 'level', ['group_id'=>3], 'LIMIT 1');
            if ($res_classname->rowCount() == 1) {
                $arr_classname = $res_classname->fetch(PDO::FETCH_ASSOC);
                $new_classname = "$arr_classname[level]";
            }
            $username = $arr['username'];
            DB::update(' users', ['class' => 3], ['id' => $arr['id']]);
            $msgg = '[b]Congratulations[/b], you were automatically promoted to [b]Power User[/b] class. Please note that if your ratio drops below [b]" . $minratio . "[/b] at any time,  you will be demoted to [b]Leecher[/b]';
            DB::insert('messages', ['sender'=>0, 'receiver'=>$arr['owner'], 'added'=>TimeDate::get_date_time(),'msg'=>$msgg,'subject'=> '[b]Congratulations[/b]']);
            unset($res, $arr, $res_classname, $arr_classname,$new_classname);
        }

        // uploader mod test
        $query = DB::run('SELECT torrents.owner, COUNT(*)  AS counta FROM torrents INNER JOIN users ON (torrents.owner=users.id) WHERE users.class < "2" and users.donated = "0" and torrents.banned = "no" and torrents.added > DATE_SUB(NOW(), INTERVAL 15 DAY) GROUP BY torrents.owner');
        while ($UP = $query->fetch(PDO::FETCH_ASSOC)){
            if ($UP['counta'] > 3){
                DB::update(' users', ['class' => 3], ['id' => $UP['owner']]);
                $subject = 'Automatic Promotion To Uploader Status';
                $msg = 'Hello you did 1 upload, you are promoted to Uploader, Bravo !!! ';
                DB::insert('messages', ['sender'=>0, 'receiver'=>$UP['owner'], 'added'=>TimeDate::get_date_time(), 'subject'=> $subject,'msg'=>$msg, 'unread'=>'yes', 'location'=>'in']);
            }
        }

        // Uploader demote if he did not upload 1 torrent over 2 week
        while ($up = $query->fetch(PDO::FETCH_ASSOC)){
            $query2 = DB::run('SELECT name, added, DATE_SUB(NOW(), INTERVAL 15 DAY) AS date_expiration FROM torrents WHERE owner = '.$up['id'].'');
            while ($up2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                if ($up2["added"] > $up2["date_expiration"]){
                    $nbre = 0 + 1;
                } else {
                    $nbre = 0;
                }
            }
        }
        if ($nbre < 1) {
            DB::update(' users', ['class' => 2], ['id' => $up['id']]);
            $subject = 'Automatic Downgrading to Member Status';
            $msg = 'You have not uploaded in the last fortnight so you have been demoted from uploader.';
            DB::insert('messages', ['sender'=>0, 'receiver'=>$up['id'], 'added'=>TimeDate::get_date_time(), 'subject'=> $subject,'msg'=>$msg, 'unread'=>'yes', 'location'=>'in']);
        }
    }

}