<?php
class Topic
{

    public function __construct()
    {
        Auth::user(0, 1);
    }

    /**
     * Lets show the validation in one place so we dont have to repeat :)
     */
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


    /**
     * View Forum Topic.
     */
    public function index()
    {
        $this->validForumUser();
        $topicid = $_GET["topicid"];

        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . '/forum', "Topic Not Valid");
        }

        // Get topic info
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


        // Paginatation
        $count = DB::column('forum_posts', 'COUNT(*)', ['topicid'=>$topicid]);
        list($pagerbuttons, $limit) = Pagination::pager(15, $count, URLROOT . "/topic?topicid=$topicid&amp;");
        //$res = DB::raw('forum_posts', '*', ['topicid'=>$topicid], "ORDER BY id $limit");
		$query = "SELECT 
        forum_posts.id,
        forum_posts.topicid,
        forum_posts.userid,
        forum_posts.added,
        forum_posts.body,
        forum_posts.editedby,
        forum_posts.editedat,
        forum_posts.attachments,
        thanks.user,
        thanks.thanked,
        thanks.type
        
        FROM forum_posts

        LEFT JOIN thanks ON forum_posts.id = thanks.thanked

        WHERE forum_posts.topicid = $topicid
        
        GROUP BY forum_posts.id 

        ORDER BY forum_posts.id $limit";
        $res = DB::run($query);
        
        $title = Lang::T("View Topic: $subject");

        $data = [
            'forum' => $forum,
            'subject' => $subject,
            'forumid' => $forumid,
            'maypost' => $maypost,
            'locked' => $locked,
            'topicid' => $topicid,
            'pagerbuttons' => $pagerbuttons,
            'sticky' => $sticky,
            'title' => $title,
            'res' => $res,
            'count' => $count,
            'lpr' => $lpr
        ];
        View::render('forum/topic/index', $data, 'user');
    }

    /**
     * Post New Topic
     */
    public function add()
    {
        $this->validForumUser();
        $forumid = $_GET["forumid"];

        if (!Validate::Id($forumid)) {
            Redirect::autolink(URLROOT . "/forum", "No Forum ID $forumid");
        }

        $name = DB::column('forum_forums', 'name', ['id'=>$forumid]);

        $data = [
            'id' => $forumid,
            'name' => $name,
            'title' => Lang::T("New Post"),
        ];
        View::render('forum/topic/add', $data, 'user');
    }

    /**
     * Confirm Post/Reply. (function insert_compose_frame)
     */
    public function submit()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");

        if (!Validate::Id($forumid) && !Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . '/forum', Lang::T("FORUM_ERROR"));
        }

        $newtopic = $forumid > 0;
        $subject = $_POST["subject"];
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

        $body = htmlspecialchars_decode($_POST["body"]);
        if (!$body) {
            Redirect::autolink(URLROOT . '/forum', "No body text.");
        }

        if ($newtopic) { //Create topic
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

        //Insert the new post
        $body = htmlspecialchars_decode($body);
        DB::insert('forum_posts', ['topicid'=>$topicid, 'userid'=>Users::get("id"), 'added'=>TimeDate::get_date_time(), 'body'=>$body]);
        $postid = DB::lastInsertId();

        // attachments todo
        set_attachmnent($topicid, $postid);

        //Update topic last post
        update_topic_last_post($topicid);

        if ($newtopic) {
            $msg_shout = "New Forum Topic: [url=" . URLROOT . "/topic?topicid=" . $topicid . "]" . $subject . "[/url] posted by [url=" . URLROOT . "/profile?id=" . Users::get('id') . "]" . Users::get('username') . "[/url]";
            DB::insert('shoutbox', ['userid'=>0, 'date'=>TimeDate::get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
            Redirect::to(URLROOT . "/topic?topicid=$topicid&page=last");
        } else {
            Redirect::to(URLROOT . "/topic?topicid=$topicid&page=last#post$postid");
        }
    }

    /**
     * Delete a Topic.
     */
    public function delete()
    {
        $this->validForumUser();
        $topicid = Input::get("topicid");

        if (!Validate::Id($topicid) || Users::get("delete_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }
        $sure = Input::get("sure");
        if ($sure == "0") {
            Redirect::autolink(URLROOT . "/forum", "Sanity check: You are about to delete a topic. Click <a href='" . URLROOT . "/topic/delete?topicid=$topicid&sure=1'>here</a> if you are sure.");
        }

        Forums::deltopic($topicid);
        Redirect::autolink(URLROOT . "/forum", Lang::T("_SUCCESS_DEL_"));
    }

    /**
     * Rename a Topic.
     */
    public function rename()
    {
        $this->validForumUser();

        if (Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        $topicid = Input::get('topicid');
        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        $subject = Input::get('subject');
        if ($subject == '') {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_YOU_MUST_ENTER_NEW_TITLE"));
        }

        DB::update('forum_topics', ['subject'=>$subject], ['id'=>$topicid]);
        $returnto = Input::get('returnto');
        if ($returnto) {
            Redirect::to($returnto);
        }
    }

    /**
     * Move a Topic.
     */
    public function move()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");

        if (!Validate::Id($forumid) || !Validate::Id($topicid) || Users::get("delete_forum") != "yes" || Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", "Invalid ID - $topicid");
        }

        // Make sure topic and forum is valid
        $res = DB::raw('forum_forums', 'minclasswrite', ['id'=>$forumid]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_NOT_FOUND"));
        }
        $arr = $res->fetch(PDO::FETCH_LAZY);
        if (Users::get('class') < $arr[0]) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_NOT_ALLOWED"));
        }

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

    /**
     * Lock a Topic.
     */
    public function lock()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        if (!Validate::Id($topicid) || Users::get("delete_forum") != "yes" || Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        DB::update('forum_topics', ['locked'=>'yes'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

    /**
     * Unlock a Topic.
     */
    public function unlock()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        if (!Validate::Id($topicid) || Users::get("delete_forum") != "yes" || Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        DB::update('forum_topics', ['locked'=>'no'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

    /**
     * Set Topic Sticky.
     */
    public function setsticky()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        if (!Validate::Id($topicid) || (Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes")) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        DB::update('forum_topics', ['sticky'=>'yes'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

    /**
     * Unstick a Topic.
     */
    public function unsetsticky()
    {
        $this->validForumUser();
        $forumid = Input::get("forumid");
        $topicid = Input::get("topicid");
        $page = Input::get("page");

        if (!Validate::Id($topicid) || (Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes")) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        DB::update('forum_topics', ['sticky'=>'no'], ['id'=>$topicid]);
        Redirect::to(URLROOT . "/forum/view&forumid=$forumid&page=$page");
    }

}