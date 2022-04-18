<?php
class Friends
{

    public static function countFriendAndEnemy($userid, $id)
    {
        $r = DB::run("SELECT id FROM friends WHERE userid=? AND friend=? AND friendid=?", [$userid, 'friend', $id]);
        $friend = $r->rowCount();
        $r = DB::run("SELECT id FROM friends WHERE userid=? AND friend=? AND friendid=?", [$userid, 'enemy', $id]);
        $block = $r->rowCount();

        $arr = [
            'friend' => $friend,
            'enemy' => $block
        ];
        return $arr;
    }

    public static function getall($userid, $type)
    {
        $arr = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access 
                               FROM friends AS f 
                               LEFT JOIN users as u ON f.friendid = u.id 
                               WHERE userid=? AND friend=? ORDER BY name", [$userid, $type]);
        $count = $arr->rowCount();
        $data = [
            'count' => $count,
            '$arr' => $arr,
        ];

        return $data;
    }

    public static function getalltype($userid, $type)
    {
        $stmt = DB::run("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=? AND friend=? ORDER BY name", [$userid, $type])->fetch();
        return $stmt;
    }

    public static function join($user, $type)
    {
        
        $stmt = DB::run("SELECT f.friendid as id, 
        u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access 
        FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id 
        WHERE userid=? AND friend=? ORDER BY name", [$user, $type]);
        return $stmt;
    }

    public static function add($targetid, $type = '')
    {
        if ($type == 'friend') {
            $r = DB::raw('friends', 'id', ['userid'=>Users::get('id'),'userid'=>$targetid]);
            if ($r->rowCount() == 1) {
                Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."", "User ID $targetid is already in your friends list.");
            }
            DB::insert('friends', ['id'=>0, 'userid'=>Users::get('id'), 'friendid'=>$targetid, 'friend'=>'friend']);
            Redirect::to(URLROOT . "/friend?id=".Users::get('id')."");
        } elseif ($type == 'block') {
            $r = DB::raw('friends', 'id', ['userid'=>Users::get('id'),'userid'=>$targetid]);
            if ($r->rowCount() == 1) {
                Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."", "User ID $targetid is already in your friends list.");
            }
            DB::insert('friends', ['id'=>0, 'userid'=>Users::get('id'), 'friendid'=>$targetid, 'friend'=>'enemy']);
            Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."", "Success");
        } else {
            Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."", "Unknown type $type");
        }
    }

    public static function delete($targetid, $type = '')
    {
        if ($type == 'friend') {
            $stmt = DB::delete('friends', ['userid' =>Users::get('id'), 'friendid' => $targetid, 'friend'=>'friend']);
            if ($stmt == 0) {
                Redirect::autolink(URLROOT . "/profile?id=$targetid", "No friend found with ID $targetid");
            }
            $frag = "friends";
        } elseif ($type == 'block') {
            $stmt = DB::delete('friends', ['userid' =>Users::get('id'), 'friendid' => $targetid, 'friend'=>'enemy']);
            if ($stmt == 0) {
                Redirect::autolink(URLROOT . "/profile?id=$targetid", "No block found with ID $targetid");
            }
            $frag = "blocked";
        } else {
            Redirect::autolink(URLROOT . "/profile?id=$targetid", "Unknown type $type");
        }
        Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."#$frag", "Success");
    }

}