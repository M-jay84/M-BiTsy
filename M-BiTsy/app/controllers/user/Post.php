<?php
class Post
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

    public function index()
    {
        Redirect::to(URLROOT . "/forum");
    }

    /**
     *Reply To Post.
     */
    public function reply()
    {
        $this->validForumUser();
        $topicid = Input::get("topicid");

        if (!Validate::Id($topicid)) {
            Redirect::autolink(URLROOT . "/forum", sprintf(Lang::T("FORUMS_NO_ID_FORUM")));
        }

        $arr = DB::all('forum_topics', '*', ['id'=>$topicid]);

        $data = [
            'title' => Lang::T("Reply"),
            'topicid' => $topicid,
            'arr' => $arr,
        ];
        View::render('forum/post/reply', $data, 'user');
    }

    /**
     * Edit a Post.
     */
    public function edit()
    {
        $this->validForumUser();
        $postid = Input::get("postid");

        if (!Validate::Id($postid)) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        $res = DB::raw('forum_posts', '*', ['id'=>$postid]);
        if ($res->rowCount() != 1) {
            Redirect::autolink(URLROOT . "/forum", "Where is id $postid");
        }
        $arr = $res->fetch(PDO::FETCH_ASSOC);
        if (Users::get("id") != $arr["userid"] && Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        $data = [
            'title' => Lang::T('Edit Post'),
            'postid' => $postid,
            'body' => $arr['body'],
        ];
        View::render('forum/post/edit', $data, 'user');
    }

    /**
     * Edit Submit.
     */
    public function submit()
    {
        $this->validForumUser();
        $postid = Input::get("postid");

        if (Input::exist()) {
            $body = $_POST['body'];
            if ($body == "") {
                Redirect::autolink(URLROOT . "/forum", "Body cannot be empty!");
            }

            $res = DB::raw('forum_posts', '*', ['id'=>$postid]);
            if ($res->rowCount() != 1) {
                Redirect::autolink(URLROOT . "/forum", "Where is this id $postid");
            }
            $arr = $res->fetch(PDO::FETCH_ASSOC);
            if (Users::get("id") != $arr["userid"] && Users::get("delete_forum") != "yes" && Users::get("edit_forum") != "yes") {
                Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
            }

            $body = htmlspecialchars_decode($body);
            $editedat = TimeDate::get_date_time();
            DB::update('forum_posts', ['body'=>$body, 'editedat'=>$editedat, 'editedby'=>Users::get('id')], ['id'=>$postid]);
            set_attachmnent($arr['topicid'], $postid);
            Redirect::autolink(URLROOT . "/topic?topicid=$arr[topicid]&page=$_POST[page]#post$postid", "Post was edited successfully.");
        } else {
            Redirect::autolink(URLROOT, Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }
    }

    
    /**
     * Delete a Post.
     */
    public function delete()
    {
        $this->validForumUser();
        $postid = Input::get("postid");
        $sure = Input::get("sure");

        if (Users::get("delete_forum") != "yes" || !Validate::Id($postid)) {
            Redirect::autolink(URLROOT . '/forum', Lang::T("FORUMS_DENIED"));
        }
        
	    if ($sure == "0") {
		    Redirect::autolink(URLROOT . '/forum', "Sanity check: You are about to delete a post. Click <a href='" . URLROOT . "/post/delete?postid=$postid&sure=1'>here</a> if you are sure.");
        }

        // Get topic id
        $arr = DB::raw('forum_posts', 'topicid', ['id'=>$postid])->fetch(PDO::FETCH_LAZY) ;
        $topicid = $arr[0];
        // We can not delete the post if it is the only one of the topic
        $arr = DB::column('forum_posts', 'COUNT(*)', ['topicid'=>$topicid]);
        if ($arr < 2) {
            $msg = sprintf(Lang::T("FORUMS_DEL_POST_ONLY_POST"), $topicid);
            Redirect::autolink(URLROOT . '/forum', $msg);
        }

        // Delete post
        DB::delete('forum_posts', ['id'=>$postid]);
        // Delete attachment todo
        $sql = DB::raw('attachments', '*', ['content_id'=>$postid]);
        if ($sql->rowCount() != 0) {
            foreach ($sql as $row7) {
                $daimage = UPLOADDIR . "/attachment/$row7[file_hash].data";
                if (file_exists($daimage)) {
                    if (unlink($daimage)) {
                        DB::delete('attachments', ['content_id' =>$postid]);
                    }
                }
                $extension = substr($row7['filename'], -3);
                if ($extension != 'zip') {
                    $dathumb = "uploads/thumbnail/$row7[file_hash].jpg";
                    if (!unlink($dathumb)) {
                        Redirect::autolink(URLROOT . "/topic?topicid=$topicid", "Could not remove thumbnail = $row7[file_hash].jpg");
                    }
                }
            }
        }

        // Update topic
        update_topic_last_post($topicid);
        Redirect::autolink(URLROOT . "/topic?topicid=$topicid", Lang::T("_SUCCESS_DEL_"));
    }

    public function user()
    {
        // Get Id
        $id = (int) Input::get("id");

        // Run Some Checks
        if (!isset($id) || !$id) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }
        if (Users::get("view_users") == "no" && Users::get("id") != $id) {
            Redirect::autolink(URLROOT, Lang::T("NO_USER_VIEW"));
        }

        // Count For Pager
        $count = DB::column('forum_posts', 'count(*)', ['userid' => $id]);
        list($pager, $limit) = Pagination::pager(20, $count, URLROOT . "/post/user?id=$id&");
        
        // Grab All Data
        $row = Forums::getUsersPost($id, $limit);
        //echo '<pre>'.print_r($row, true).'</pre>';  

        // Check If Data Returned
        if (!$row) {
            Redirect::autolink(URLROOT, "User has not posted in forum");
        }
 
        // Send Data To View
        $data = [
            'title' => Lang::T("Search Users Post"),
            'id' => $id,
            'res' => $row,
            'pager' => $pager,
        ];
        View::render('forum/post/user', $data, 'user');
    }
    
}