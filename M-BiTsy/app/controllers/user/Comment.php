<?php
class Comment
{
    public function __construct()
    {
        Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");

        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }

        if ($type == "news") {
            $row = DB::raw('news', '*', ['id'=>$id])->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Redirect::autolink(URLROOT . "/comment?type=news&id=$id", Lang::T("INVALID_ID"));
            }
            $title = Lang::T("NEWS");
        }

        if ($type == "torrent") {
            $row = DB::raw('torrents', 'id, name', ['id'=>$id])->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='torrent?id=" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "req") {
            $row = DB::select('comments', '*', ['req'=>$id])->fetch(PDO::FETCH_LAZY);
            if (!$row) {
                Redirect::autolink(URLROOT, Lang::T("INVALID_ID"));
            }
            $title = Lang::T("COMMENTSFOR") . "<a href='" . URLROOT . "/request'>" . htmlspecialchars($row['name']) . "</a>";
        }

        $pager = Comments::commentPager($id, $type);

        $data = [
            'title' => $title,
            'pagerbuttons' => $pager['pagerbuttons'],
            'commres' => $pager['commres'],
            'pagerbuttons' => $pager['pagerbuttons'],
            'limit' => $pager['limit'],
            'commcount' => $pager['commcount'],
            'row' => $row,
            'newsbody' => $row['body'],
            'newstitle' => $row['title'],
            'type' => $type,
            'id' => $id,
        ];
        View::render('comment/index', $data, 'user');
    }

    public function add()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");

        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }

        $data = [
            'title' => 'Add Comment',
            'id' => $id,
            'type' => $type,
        ];
        View::render('comment/add', $data, 'user');
    }

    public function edit()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");

        if (!isset($id) || !$id || ($type != "torrent" && $type != "news" && $type != "req")) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }

        $arr = DB::raw('comments', '*', ['id'=>$id])->fetch();
        if (($type == "torrent" && Users::get("edit_torrents") == "no" || $type == "news" && Users::get("edit_news") == "no") && Users::get('id') != $arr['user'] || $type == "req" && Users::get('id') != $arr['user']) {
            Redirect::autolink(URLROOT, Lang::T("ERR_YOU_CANT_DO_THIS"));
        }

        $save = (int) Input::get("save");
        if ($save) {
            $text = $_POST['text'];
            DB::update('comments', ['text'=>$text], ['id'=>$id]);
            Logs::write(Users::coloredname(Users::get('username')) . " has edited comment: ID:$id");
            Redirect::autolink(URLROOT."/comment?type=$type&id=$id", Lang::T("_SUCCESS_UPD_"));
        }

        $data = [
            'title' => 'Edit Comment',
            'text' => $arr['text'],
            'id' => $id,
            'type' => $type,
        ];
        View::render('comment/edit', $data, 'user');
    }

    public function delete()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");

        if (Users::get("delete_news") == "no" && $type == "news" || Users::get("delete_torrents") == "no" && $type == "torrent") {
            Redirect::autolink(URLROOT, Lang::T("ERR_YOU_CANT_DO_THIS"));
        }

        if ($type == "torrent") {
            $row = DB::select('comments', 'torrent', ['id'=>$id]);
            if ($row["torrent"] > 0) {
                Torrents::updateComments($id, 'sub');
            }
        }

        DB::delete('comments', ['id'=>$id]);
        Logs::write(Users::coloredname(Users::get('username')) . " has deleted comment: ID: $id");
        Redirect::autolink(URLROOT, Lang::T("_SUCCESS_DEL_"));
    }

    public function take()
    {
        $id = (int) Input::get("id");
        $type = Input::get("type");
        $body = Input::get('body');

        if (!$body) {
            Redirect::autolink(URLROOT . "/comment?type=$type&id=$id", Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
        }
        if ($type == "torrent") {
            Torrents::updateComments($id, 'add');
        }
        
        $ins = DB::insert('comments', ['user'=>Users::get("id"), $type=>$id, 'added'=>TimeDate::get_date_time(), 'text'=>$body, 'type'=>$type]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/comment?type=$type&id=$id", Lang::T("_SUCCESS_ADD_"));
        } else {
            Redirect::autolink(URLROOT . "/comment?type=$type&id=$id", Lang::T("UNABLE_TO_ADD_COMMENT"));
        }
    }

    public function user()
    {
        // Get Id
        $id = (int) Input::get("id");

        // Run Some Checks
        if (!isset($id) || !$id) {
            Redirect::autolink(URLROOT, Lang::T("ERROR"));
        }
        
        // Count For Pager
        $count = DB::column('comments', 'count(*)', ['user' => $id]);
        list($pager, $limit) = Pagination::pager(20, $count, URLROOT . "/comment/user?id=$id&");
                
        // Grab All Data
        $row = Comments::join($id, $limit);
        //echo '<pre>'.print_r($row, true).'</pre>';  

        // Check If Data Returned
        if (!$row) {
            Redirect::autolink(URLROOT, "User has not posted any comments");
        }

        // Check If Data Returned
        $data = [
            'title' => Lang::T("Search Users Comments"),
            'id' => $id,
            'res' => $row,
            'pager' => $pager,
        ];
        View::render('comment/user', $data, 'user');
    }

}