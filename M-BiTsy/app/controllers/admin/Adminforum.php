<?php

class Adminforum
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Forum Default Page
    public function index()
    {
        // Get Forum Data
        $query = DB::raw('forumcats', '*', '', 'ORDER BY sort, name');
        $forumcat = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $forumcat[] = $row;
        }

        $query = DB::raw('forum_forums', '*', '', 'ORDER BY category, sub, sort');

        $query1 = DB::raw('forum_forums', '*', ['sub'=>0], 'ORDER BY sort, name');
        $forumforum = array();
        while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
                $forumforum[] = $row;
        }

        // Init Data
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'forumcat' => $forumcat,
            'forumforum' => $forumforum,
            'query' => $query,
        ];

        // Load View
        View::render('forum/forum', $data, 'admin');
    }

    // Add Forum Submit
    public function addforum()
    {
        // Check User Input
        $error_ac = "";
        $new_forum_name = $_POST["new_forum_name"];
        $new_desc = $_POST["new_desc"];
        $new_forum_sort = (int) $_POST["new_forum_sort"];
        $new_forum_cat = (int) $_POST["new_forum_cat"];
        $minclassread = (int) $_POST["minclassread"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $guest_read = $_POST["guest_read"];
        $new_forum_forum = (int) $_POST["new_forum_forum"] ?? 0; // sub forum mod

        // Check Correct Input
        if ($new_forum_name == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_NAME_WAS_EMPTY") . "</li>\n";
        }
        if ($new_desc == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_DESC_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forum_sort == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_SORT_ORDER_WAS_EMPTY") . "</li>\n";
        }
        if ($new_forum_cat == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CATAGORY_WAS_EMPTY") . "</li>\n";
        }

        // Insert Forum
        if ($error_ac == "") {
            $res = DB::insert('forum_forums', ['name'=>$new_forum_name, 'description'=>$new_desc, 'sort'=>$new_forum_sort, 'category'=>$new_forum_cat, 'minclassread'=> $minclassread, 'minclasswrite'=>$minclasswrite, 'guest_read'=> $guest_read, 'sub'=>$new_forum_forum]);
            if ($res) {
                Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_NEW_ADDED_TO_DB"));
            } else {
                Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_COULD_NOT_SAVE_TO_DB"));
            }
        } else {
            Redirect::autolink(URLROOT . "/adminforum", $error_ac);
        }
    }

    // Delete Forum Default Page
    public function deleteforum()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Get Forum Data
        $v = DB::raw('forum_forums', '*', ['id'=>$id])->fetch();
        if (!$v) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID"));
        }

        // Init Data
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'catid' => $v['sort'],
            'name' => $v['name'],
        ];

        // Load View
        View::render('forum/deleteforum', $data, 'admin');
    }

    // Delete Forum Submit
    public function deleteforumok()
    {
        DB::delete('forum_forums', ['id'=>$_POST['id']]);
        DB::delete('forum_topics', ['forumid'=>$_POST['id']]);
        DB::delete('forum_posts', ['topicid'=>$_POST['id']]);
        DB::delete('forum_readposts', ['topicid'=>$_POST['id']]);
        Redirect::autolink(URLROOT . "/adminforum", Lang::T("CP_FORUM_DELETED"));
    }

    // Edit Forum Default Page
    public function editforum()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Get Forum Data
        $r = DB::raw('forum_forums', '*', ['id'=>$id])->fetch();
        if (!$r) {
            Redirect::autolink(URLROOT . "/adminforum", Lang::T("FORUM_INVALID"));
        }

        $query = DB::raw('forumcats', '*', '', 'ORDER BY sort, name');

        // Init Data
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'sort' => $r['sort'],
            'name' => $r['name'],
            'sub' => $r['sub'],
            'description' => $r['description'],
            'guest_read' => $r['guest_read'],
            'query' => $query,
        ];

        // Load View
        View::render('forum/editforum', $data, 'admin');
    }

    // Edit Forum Submit
    public function saveeditforum()
    {
        // Check User Input
        $id = (int) $_POST["id"];
        $changed_sort = (int) $_POST["changed_sort"];
        $changed_forum = $_POST["changed_forum"];
        $changed_forum_desc = $_POST["changed_forum_desc"];
        $changed_forum_cat = (int) $_POST["changed_forum_cat"];
        $minclasswrite = (int) $_POST["minclasswrite"];
        $minclassread = (int) $_POST["minclassread"];
        $guest_read = $_POST["guest_read"];
        $changed_sub = (int) $_POST["changed_sub"];

        DB::update('forum_forums', ['sort' =>$changed_sort, 'name' =>$changed_forum, 'description' =>$changed_forum_desc, 'category' =>$changed_forum_cat, 'minclassread'=>$minclassread, 'minclasswrite'=>$minclasswrite, 'guest_read'=>$guest_read, 'sub'=>$changed_sub], ['id' => $id]);
        Redirect::autolink(URLROOT . "/adminforum", "<center><b>" . Lang::T("CP_UPDATE_COMPLETED") . "</b></center>");
    }

}