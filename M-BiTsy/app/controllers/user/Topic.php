<?php

class Topic
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // Check User
    private function validForumUser($extra = false)
    {
        if (!Config::get('FORUMS')) {
            Redirect::autolink(URLROOT, Lang::T("FORUM_AVAILABLE"));
        }
        if (!Config::get('FORUMS_GUESTREAD') && !$_SESSION['loggedin']) {
            Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION"));
        }
        if (Users::get("forumbanned") == "yes" || Users::get("view_forum") == "no") {
            Redirect::autolink(URLROOT, Lang::T("FORUM_BANNED"));
        }
    }


    // View Forum Topic Default Page
    public function index()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $topicid = $_GET["topicid"];

        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . '/forum', "Topic Not Valid");
        }

        // Get Topic Data
        $arr = DB::select('forum_topics', '*', ['id'=>$topicid]);
        $locked = ($arr["locked"] == 'yes');
        $subject = stripslashes($arr["subject"]);
        $sticky = $arr["sticky"] == "yes" ;
        $forumid = $arr["forumid"];

        // Update Topic Views
        $pvkey_var = "f_".$topicid;
        $views = $_SESSION[$pvkey_var] == 1 ? $arr["views"] + 0 : $arr["views"] + 1;
        $_SESSION[$pvkey_var] = 1;
        DB::update('forum_topics', ['views'=>$views], ['id'=>$topicid]);
		
        // Check if user has access to this forum
        $arr2 = DB::select('forum_forums', '*', ['id'=>$forumid]);
        if (!$arr2 || Users::get("class") < $arr2["minclassread"] && $arr2["guest_read"] == "no") {
            Redirect::autolink(URLROOT . '/forum', "You do not have access to the forum this topic is in.");
        }

        $forum = stripslashes($arr2["name"]);
        $levels = get_forum_access_levels($forumid) or die;
        $maypost = Users::get("class") >= $levels["write"] ? true : false;
        
        // Update Last Read
        if ($_SESSION['loggedin'] == true) {
            $r = DB::raw('forum_readposts', 'lastpostread', ['userid'=>Users::get('id'),'topicid'=>$topicid]);
            $a = $r->fetch(PDO::FETCH_LAZY);
            $lpr = $a[0];
            if (!$lpr) {
                DB::insert('forum_readposts', ['userid'=>Users::get('id'), 'topicid'=>$topicid]);
            }
        }

        // Get Topic Data
        $stmt = Forums::gettopicpages($topicid);
        
        $title = Lang::T("View Topic: $subject");

        // Init Data
        $data = [
            'forum' => $forum,
            'subject' => $subject,
            'forumid' => $forumid,
            'maypost' => $maypost,
            'locked' => $locked,
            'topicid' => $topicid,
            'pagerbuttons' => $stmt['pagerbuttons'],
            'sticky' => $sticky,
            'title' => $title,
            'res' => $stmt['res'],
            'count' => $stmt['count'],
            'lpr' => $lpr
        ];

        // Load View
        View::render('topic/index', $data, 'user');
    }

    // Post New Topic Default Page
    public function add()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = $_GET["forumid"];

        if (!Validate::Id($forumid)) {
            Redirect::autolink(URLROOT . "/forum", "No Forum ID $forumid");
        }

        // Get Forum Data
        $name = DB::column('forum_forums', 'name', ['id'=>$forumid]);

        // Init Data
        $data = [
            'id' => $forumid,
            'name' => $name,
            'title' => Lang::T("New Post"),
        ];

        // Load View
        View::render('topic/add', $data, 'user');
    }

    // Confirm Post/Reply Form Submit (function insert_compose_frame)
    public function submit()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $newtopic = $forumid > 0;
        $subject = $_POST["subject"];
        $body = htmlspecialchars_decode($_POST["body"]);
        
        // Check Correct Input
        if (!Validate::Id($forumid) && !Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . '/forum', Lang::T("FORUM_ERROR"));
        }
        
        if (!$body) {
            Redirect::autolink(URLROOT . '/forum', "No body text.");
        }

        if ($newtopic) {
            if (!$subject) {
                Redirect::autolink(URLROOT . '/forum', "You must enter a subject.");
            }
            $subject = trim($subject);
        } else {
            $forumid = get_topic_forum($topicid) or Redirect::autolink(URLROOT . '/forum', "Bad topic ID");
        }

        // Make sure sure user has write access in forum
        $arr = get_forum_access_levels($forumid) or Redirect::autolink(URLROOT . '/forum', "Bad forum ID");
        if (Users::get('class') < $arr["write"]) {
            Redirect::autolink(URLROOT . '/forum', Lang::T("FORUMS_NOT_PERMIT"));
        }

        // Create Topic / Reply
        if ($newtopic) {
            $subject = $subject;
            DB::insert('forum_topics', ['userid'=>Users::get('id'), 'forumid'=>$forumid, 'subject'=>$subject]);
            $topicid = DB::lastInsertId() or Redirect::autolink(URLROOT . '/forum', "Topics id n/a");
        } else {
            //Make sure topic exists and is unlocked
            $arr = DB::select('forum_topics', '*', ['id'=>$topicid]);
            if ($arr["locked"] == 'yes') {
                Redirect::autolink(URLROOT . '/forum', "Topic locked");
            }
            //Get forum ID
            $forumid = $arr["forumid"];
        }

        // Insert the new post
        DB::insert('forum_posts', ['topicid'=>$topicid, 'userid'=>Users::get("id"), 'added'=>TimeDate::get_date_time(), 'body'=>$body]);
        $postid = DB::lastInsertId();

        // attachments
        set_attachmnent($topicid, $postid);

        // Update topic last post
        update_topic_last_post($topicid);

        if ($newtopic) {
            $msg_shout = "New Forum Topic: [url=" . URLROOT . "/topic?topicid=" . $topicid . "]" . $subject . "[/url] posted by [url=" . URLROOT . "/profile?id=" . Users::get('id') . "]" . Users::get('username') . "[/url]";
            DB::insert('shoutbox', ['userid'=>0, 'date'=>TimeDate::get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
            Redirect::to(URLROOT . "/topic?topicid=$topicid&page=last");
        } else {
            Redirect::to(URLROOT . "/topic?topicid=$topicid&page=last#post$postid");
        }
    }

    // Delete a Topic Submit
    public function delete()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $topicid = Input::get("topicid");
        $sure = Input::get("sure");

        // Check Correct Input
        if (!Validate::Id($topicid) || Users::get("delete_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        if ($sure == "0") {
            Redirect::autolink(URLROOT . "/forum", "Sanity check: You are about to delete a topic. Click <a href='" . URLROOT . "/topic/delete?topicid=$topicid&sure=1'>here</a> if you are sure.");
        }

        Forums::deltopic($topicid);
        Redirect::autolink(URLROOT . "/forum", Lang::T("_SUCCESS_DEL_"));
    }

    // Rename a Topic Submit
    public function rename()
    {
        // Check User
        $this->validForumUser();

        if (Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        // Check User Input
        $topicid = Input::get('topicid');
        $subject = Input::get('subject');
        
        // Check Correct Input
        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        if ($subject == '') {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_YOU_MUST_ENTER_NEW_TITLE"));
        }

        // Update
        DB::update('forum_topics', ['subject'=>$subject], ['id'=>$topicid]);
        $returnto = Input::get('returnto');
        if ($returnto) {
            Redirect::to($returnto);
        }
    }

    // Move a Topic Submit
    public function move()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");

        // Check User
        if (!Validate::Id($forumid) || !Validate::Id($topicid) || Users::get("delete_forum") != "yes" || Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", "Invalid ID - $topicid");
        }

        // Check Permisson To View
        $res = DB::raw('forum_forums', 'minclasswrite', ['id'=>$forumid]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_NOT_FOUND"));
        }
        $arr = $res->fetch(PDO::FETCH_LAZY);
        if (Users::get('class') < $arr[0]) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_NOT_ALLOWED"));
        }

        // Move Topic
        $res = DB::raw('forum_topics', 'subject,forumid', ['id'=>$topicid]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_NOT_FOUND_TOPIC"));
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if ($arr["forumid"] != $forumid) {
            DB::update('forum_topics', ['forumid'=>$forumid, 'moved'=>'yes'], ['id'=>$topicid]);
        }

        Redirect::to(URLROOT . "/forum/view&forumid=$forumid");
    }

    // Lock a Topic Submit
    public function lock()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        // Check User
        if (!Validate::Id($topicid) || Users::get("delete_forum") != "yes" || Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        // Update
        DB::update('forum_topics', ['locked'=>'yes'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

    // Unlock Topic Submit
    public function unlock()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        // Check User
        if (!Validate::Id($topicid) || Users::get("delete_forum") != "yes" || Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        // Check User
        DB::update('forum_topics', ['locked'=>'no'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

    // Set Topic Sticky Submit
    public function setsticky()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        // Check User
        if (!Validate::Id($topicid) || (Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes")) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        // Update
        DB::update('forum_topics', ['sticky'=>'yes'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

    // Unstick Topic Submit
    public function unsetsticky()
    {
        // Check User
        $this->validForumUser();

        // Check User Input
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        // Check User
        if (!Validate::Id($topicid) || (Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes")) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        // Update
        DB::update('forum_topics', ['sticky'=>'no'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

}