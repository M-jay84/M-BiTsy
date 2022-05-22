<?php
class Bonuses
{

    // Get Bonus by Post Id
    public static function getBonusByPost($id)
    {
        $res = DB::raw('bonus', 'type, value, cost', ['id' =>$id]);
        $row = $res->fetch(PDO::FETCH_LAZY);
        return $row;
    }
    
    public static function switch($row)
    {
        switch ($row['type']) {
            case 'invite':
                DB::run("UPDATE `users` SET `invites` = `invites` + '$row[value]' WHERE `id` = ".Users::get('id')."");
                break;
            case 'traffic':
                DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row[value]' WHERE `id` = ".Users::get('id')."");
                break;
            case 'HnR':
                $uid = Users::get("id");
                if (empty($uid)) {
                    Redirect::autolink(URLROOT, "You are not a member ?.");
                }
                if (isset($uid)) {
                    $res = DB::raw('snatched', '*', ['uid' => $uid, 'hnr' => 'yes']);
                    if ($res->rowCount() == 0) {
                        Redirect::autolink("bonus", "No HnR found for this user.");
                    }
                    Logs::write("A HnR for <a href='profile?id=" . $uid . "'>" . Users::coloredname(Users::get('id')) . "</a> has been removed");
                    $new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
                    $new_modcomment .= "" . Users::coloredname(Users::get('id')) . " has cleared H&R for " . $row['cost'] . " points \n";
                    $modcom = $new_modcomment;
                    DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = ?", [$uid]);
                    DB::update('snatched', ['last_time' =>129600, 'hnr' =>'no'], ['uid' => $uid]);
                }
                break;
            case 'other':
                break;
            case 'VIP':
                $days = $row['value'];
                $vipuntil = (Users::get("vipuntil") != null) ? $vipuntil = TimeDate::get_date_time(strtotime(Users::get("vipuntil")) + (30 * 86400)) : $vipuntil = TimeDate::get_date_time(TimeDate::gmtime() + (30 * 86400));
                $oldclass = (Users::get("vipuntil") != null) ? $oldclass = Users::get("oldclass") : $oldclass = Users::get("class");
                DB::update('users', ['class' =>3, 'oldclass' =>$oldclass,'vipuntil' =>$vipuntil], ['id' => Users::get('id')]);
                break;
        }
    }

}