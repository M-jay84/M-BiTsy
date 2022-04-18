<?php

class Forum
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // Validate User/Guest
    private function validForumUser()
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

    // Forum Default Page
    public function index()
    {
        // Validate User/Geust
        $this->validForumUser();

        // Mark All Post Read
        if (isset($_GET["do"]) == 'catchup') {
            catch_up();
        }

        // Get Forum Data
        $forums_res = Forums::getIndex();
        if ($forums_res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("FORUM_AVAILABLE"));
        }
        $subforums_res = Forums::getsub();

        // Count Topis/Posts
        $postcount = number_format(get_row_count("forum_posts"));
        $topiccount = number_format(get_row_count("forum_topics"));

        // Init Data
        $data = [
            'title' => Lang::T("Forums"),
            'mainquery' => $forums_res,
            'mainsub' => $subforums_res,
            'postcount' => $postcount,
            'topiccount' => $topiccount,
        ];
        
        // Load View
        View::render('forum/index', $data, 'user');
    }

    // Search Forum Default Page
    public function search()
    {
        // Validate User/Geust
        $this->validForumUser();

        // Init Data
        $data = [
            'title' => Lang::T("Search Forums"),
        ];
        
        // Load View
        View::render('forum/search', $data, 'user');
    }

    // Search Forum Results Page
    public function result()
    {
        // Validate User/Geust
        $this->validForumUser();

        // Check User Input
        $keywords = Input::get("keywords");
        $type = Input::get("type");;

        // Search Match
        if (!$keywords == '') {
            $res = Forums::search($keywords, $type);
            if ($res['count'] > 0) {
                
                // Init Data
                $data = [
                    'res' => $res['res'],
                    'keywords' => $keywords,
                    'title' => 'Forums',
                    'count' => $res['count'],
                    'pagerbuttons' => $res['pager'],
                ];
        
                // Load View
                View::render('forum/result', $data, 'user');

            } else {
                Redirect::autolink(URLROOT . '/forum/search', Lang::T("NOTHING_FOUND"));
            }

        } else {
            Redirect::autolink(URLROOT . '/forum/search', Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }
    }

    // View Unread Topics Default Page
    public function viewunread()
    {
        // Validate User/Geust
        $this->validForumUser();

        // Get Data
        $res = DB::run("SELECT id, forumid, subject, lastpost FROM forum_topics ORDER BY lastpost DESC");
        
		// Init Data
        $data = [
            'res' => $res,
            'title' => Lang::T("Forums"),
        ];

        // Load View
        View::render('forum/viewunread', $data, 'user');
    }

    // View Forum Default Page
    public function view()
    {
        // Validate User/Geust
        $this->validForumUser();

        // Check Input
        $forumid = Input::get("forumid");
        if (!Validate::Id($forumid)) {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_DENIED"));
        }

        // Get Forum Data
        $arr = DB::select('forum_forums', 'name, minclassread, guest_read', ['id'=>$forumid]);
        $forumname = $arr["name"];
        if (!$forumname || Users::get('class') < $arr["minclassread"] && $arr["guest_read"] == "no") {
            Redirect::autolink(URLROOT . "/forum", Lang::T("FORUMS_NOT_PERMIT"));
        }

        // Pagination
        $count = get_row_count("forum_topics", "WHERE forumid=$forumid");
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT . "/forum/view&forumid=$forumid&");
        $topicsres = DB::all('forum_topics', '*', ['forumid'=>$forumid], 'ORDER BY sticky, lastpost', "DESC $limit");

        $test = DB::raw('forum_forums', 'sub', ['id'=>$forumid])->fetch(); // sub forum mod
        $test1 = DB::raw('forum_forums', 'name,id', ['id'=>$test['sub']])->fetch(); // sub forum mod
        $subforum = $test1['name'] ?? '';
        $subforumid = $test1['id'] ?? 0;
        $testz = DB::all('forum_forums', '*', ['sub'=>$forumid]); // sub forum mod

        // Init Data
        $data = [
            'title' => Lang::T("View Forums"),
            'topicsres' => $topicsres,
            'forumname' => $forumname,
            'forumid' => $forumid,
            'pagerbuttons' => $pagerbuttons,

            'testz' => $testz,
            'subforum' => $subforum,
            'subforumid' => $subforumid,
        ];

        // Load View
        View::render('forum/viewforum', $data, 'user');
    }

}