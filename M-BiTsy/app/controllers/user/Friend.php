<?php
class Friend
{

    public function __construct()
    {
        Auth::user(0, 2);
    }

    public function index()
    {
        $userid = (int) Input::get('id');

        if (!Validate::Id($userid)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USERID"));
        }
        if (Users::get("view_users") == "no" && Users::get("id") != $userid) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        $user = DB::select('users', '*', ['id'=>$userid]);
        $friend = Friends::join($user['id'], 'friend');
        $enemy = Friends::join($user['id'], 'enemy');

        // Template
        $data = [
            'title' => "Friend Lists For ".Users::coloredname($userid)."",
            'sql' => $user,
            'username' => $user['username'],
            'userid' => $userid,
            'friend' => $friend,
            'enemy' => $enemy,
        ];
        View::render('friend/index', $data, 'user');
    }

    public function add()
    {
        $targetid = (int) Input::get('targetid');
        $type = Input::get('type');

        if (!Validate::Id($targetid)) {
            Redirect::autolink(URLROOT, Lang::T("INVALID_USERID"));
        }

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

    public function delete()
    {
        $targetid = (int) Input::get('targetid');
        $sure = htmlentities(Input::get('sure'));
        $type = htmlentities(Input::get('type'));

        if ($type != "block") {
            $typ = "friend from list";
        } else {
            $typ = "blocked user from list";
        }
        
        if (!Validate::Id($targetid)) {
            Redirect::autolink(URLROOT . "/friend?id=".Users::get('id')."]", "Invalid ID ".Users::get('id')."");
        }

        if (!$sure) {
            $msg = "<div style='margin-top:10px; margin-bottom:10px' align='center'>Do you really want to delete this $typ? &nbsp; \n" . "<a href=?id=".Users::get('id')."/delete&type=$type&targetid=$targetid&sure=1>Yes</a> | <a href=friends.php>No</a></div>";
            Redirect::autolink(URLROOT . "/profile?id=$targetid", $msg);
        }

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