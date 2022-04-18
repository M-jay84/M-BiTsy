<?php

class AdminforumCat
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Forum Cat Default Page
    public function index()
    {
        // Get Forum Cat Data
        $query = DB::raw('forumcats', '*', '', 'ORDER BY sort, name');
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $forumcat[] = $row;
        }

        // Init Data
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'forumcat' => $forumcat,
        ];

        // Load View
        View::render('forumcat/index', $data, 'admin');
    }

    // Add Forum Cat Submit
    public function addcat()
    {
        // Check User Input
        $error_ac = "";
        $new_forumcat_name = $_POST["new_forumcat_name"];
        $new_forumcat_sort = $_POST["new_forumcat_sort"];

        if ($new_forumcat_name == "") {
            $error_ac .= "<li>" . Lang::T("CP_FORUM_CAT_NAME_WAS_EMPTY") . "</li>\n";
        }

        // Insert Forum Cat
        if ($error_ac == "") {
            $res = DB::insert('forumcats', ['name'=>$new_forumcat_name, 'sort'=>intval($new_forumcat_sort)]);
            if ($res) {
                Redirect::autolink(URLROOT . "/adminforumcat", "Thank you, new forum cat added to db ...");
            } else {
                Redirect::autolink(URLROOT . "/adminforumcat", Lang::T("CP_COULD_NOT_SAVE_TO_DB"));
            }
        } else {
            Redirect::autolink(URLROOT . "/adminforumcat", $error_ac);
        }
    }

    // Delete Forum Cat Default Page
    public function delcat()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Get Forum Cat Data
        $v = DB::select('forumcats', '*', ['id'=>$id]);
        if (!$v) {
            Redirect::autolink(URLROOT . "/adminforumcat", Lang::T("FORUM_INVALID_CAT"));
        }

        // Init Data
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'catid' => $v['id'],
            'name' => $v['name'],
        ];

        // Load View
        View::render('forumcat/deletecat', $data, 'admin');
    }

    // Delete Forum Cat Submit
    public function deleteforumcat()
    {
        DB::delete('forumcats', ['id'=>$_POST['id']]);
        $res = DB::raw('forum_forums', 'id', ['category' => $_POST['id']]);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $res2 = DB::raw('forum_topics', 'id', ['forumid'=>$row['id']]);
            while ($arr = $res2->fetch(PDO::FETCH_ASSOC)) {
                DB::delete('forum_posts', ['topicid'=>$arr['id']]);
                DB::delete('forum_readposts', ['topicid'=>$arr['id']]);
            }
            DB::delete('forum_topics', ['forumid'=>$row['id']]);
            DB::delete('forum_forums', ['id'=>$row['id']]);
        }
        Redirect::autolink(URLROOT . "/adminforumcat", Lang::T("CP_FORUM_CAT_DELETED"));
    }

    // Edit Forum Cat Default Page
    public function editcat()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        // Get Forum Cat Data
        $r = DB::raw('forumcats', '*', ['id'=>$id])->fetch();
        if (!$r) {
            Redirect::autolink(URLROOT . "/adminforumcat", Lang::T("FORUM_INVALID_CAT"));
        }

        // Init Data
        $data = [
            'title' => Lang::T("FORUM_MANAGEMENT"),
            'id' => $id,
            'sort' => $r['sort'],
            'name' => $r['name'],
        ];

        // Load View
        View::render('forumcat/editcat', $data, 'admin');
    }

    // Delete Forum Cat Submit
    public function saveeditcat()
    {
        // Check User Input
        $id = (int) $_POST["id"];
        $changed_sortcat = (int) $_POST["changed_sortcat"];

        DB::update('forumcats', ['sort'=>$changed_sortcat, 'name'=>$_POST["changed_forumcat"]], ['id' => $id]);
        Redirect::autolink(URLROOT . "/adminforumcat", "<center><b>" . Lang::T("CP_UPDATE_COMPLETED") . "</b></center>");
    }

}