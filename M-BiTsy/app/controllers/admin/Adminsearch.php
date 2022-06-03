<?php

class Adminsearch
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        // Check User
        if (Users::get('delete_users') == 'no' || Users::get('delete_torrents') == 'no') {
            Redirect::autolink(URLROOT . "/admincp", "You do not have permission to be here.");
        }
        
        if ($_POST['do'] == "del") {
            if (!@count($_POST["users"])) {
                Redirect::autolink(URLROOT."/adminsearch", "Nothing Selected.");
            }
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            $res = DB::run("SELECT `id`, `username` FROM `users` WHERE `id` IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                Logs::write("Account '$row[1]' (ID: $row[0]) was deleted by ".Users::get('username')."");
                Users::deleteuser($row[0]);
            }
            if ($_POST['inc']) {
                $res = DB::run("SELECT `id`, `name` FROM `torrents` WHERE `owner` IN ($ids)");
                while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                    Logs::write("Torrent '$row[1]' (ID: $row[0]) was deleted by ".Users::get('username')."");
                    Torrents::deletetorrent($row["id"]);
                }
            }
            Redirect::autolink(URLROOT . "/adminsearch", "Entries Deleted");
        }

        // Search User
        $where = null;
        if (!empty($_GET['search'])) {
            $search = sqlesc('%' . $_GET['search'] . '%');
            $where = "AND username LIKE " . $search . " OR email LIKE " . $search . "
                 OR ip LIKE " . $search;
        }

        // Pagination
        $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' $where");
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, '/adminsearch/simpleusersearch?;');
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE enabled = 'yes' AND status = 'confirmed' $where ORDER BY username DESC $limit");

        // Init Data
        $data = [
            'title' => Lang::T("USERS_SEARCH_SIMPLE"),
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'res' => $res,
        ];

        // Load View
        View::render('search/index', $data, 'admin');
    }

    public function advanced()
    {
        $do = $_GET['do']; // todo
        if ($do == "warndisable") {
            if (empty($_POST["warndisable"])) {
                Redirect::autolink(URLROOT."/adminsearch/advanced", "You must select a user to edit.", 1);
            }
            if (!empty($_POST["warndisable"])) {
                $enable = $_POST["enable"];
                $disable = $_POST["disable"];
                $unwarn = $_POST["unwarn"];
                $warn = $_POST["warn"];
                $warnlength = (int) $_POST["warnlength"];
                $warnpm = $_POST["warnpm"];
                $_POST['warndisable'] = array_map("intval", $_POST['warndisable']);
                $userid = implode(", ", $_POST['warndisable']);
                if ($disable != '') {
                    DB::run("UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                }
                if ($enable != '') {
                    DB::run("UPDATE users SET enabled='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                }
                if ($unwarn != '') {
                    $msg = "Your Warning Has Been Removed";
                    foreach ($_POST["warndisable"] as $userid) {
                        $qry = DB::insert('messages', ['poster'=>0, 'sender'=>0, 'receiver'=>$userid, 'added'=>TimeDate::get_date_time(), 'msg'=>$msg]);
                        if (!$qry) {
                            die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $qry->errorCode() . ") " . $qry->errorInfo());
                        }
                    }
                    $r = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $r->errorCode() . ") " . $r->errorInfo());
                    $user = $r->fetch(PDO::FETCH_LAZY);
                    $exmodcomment = $user["modcomment"];
                    $modcomment = gmdate("Y-m-d") . " - Warning Removed By " . Users::get('username') . ".\n" . $modcomment . $exmodcomment;
                    $query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
                    $q = DB::run($query);
                    if (!$q) {
                        die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $q->errorCode() . ") " . $q->errorInfo());
                    }

                    DB::run("UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                }
                if ($warn != '') {
                    if (empty($_POST["warnpm"])) {
                        Redirect::autolink(URLROOT."/adminsearch/advanced", "You must type a reason/mod comment.", 1);
                    }

                    $msg = "You have received a warning, Reason: $warnpm";
                    $user = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")->fetch();
                    $exmodcomment = $user["modcomment"];
                    $modcomment = gmdate("Y-m-d") . " - Warned by " . Users::get('username') . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
                    $query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
                    $upd = DB::run($query);
                    if (!$upd) {
                        die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $upd->errorCode() . ") " . $upd->errorInfo());
                    }

                    DB::run("UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                    foreach ($_POST["warndisable"] as $userid) {
                        $ins = DB::insert('messages', ['poster'=>0, 'sender'=>0, 'receiver'=>$userid, 'added'=>TimeDate::get_date_time(), 'msg'=>$msg]);
                        if (!$ins) {
                            die("<b>A fatal MySQL error occured</b>.\n <br />\n" . Lang::T("ERROR") . ": (" . $ins->errorCode() . ") " . $ins->errorInfo());
                        }

                    }
                }
            }
            Redirect::autolink("$_POST[referer]", "Redirecting back");
            die;
        }
        
        $data = [
            "name" => $_POST['name'],
            "ratio" => $_POST['ratio'],
            "member" => $_POST['member'],
            "email" => $_POST['email'],
            "ip" => $_POST['ip'],
            "account" => $_POST['account'],
            "uploaded" => $_POST['uploaded'],
            "class" => $_POST['class'],
            "downloaded" => $_POST['downloaded'],
            "warned" => $_POST['warned']
        ];
        
        $url = "?"; // assign url
        $wherea = []; // assign conditions
        $params = []; // assign vars
        
        if (!empty($data['name'])) {
            $wherea[] = "users.username LIKE '%$data[name]%'";
        }
        
        if (!empty($data['ratio'])) {
            $ratio = $data['ratio'];
            $wherea[] = "users.uploaded/users.downloaded > $data[ratio]";
        }
        
        if (!empty($data['member'])) {
            $wherea[] = "users.status = '$data[member]'";
        }
        
        if (!empty($data['email'])) {
            $wherea[] = "users.email LIKE '%$data[email]%'";
        }

        if (!empty($data['ip'])) {
            $wherea[] = "users.ip LIKE '%$data[ip]%'";
        }
        
        if (!empty($data['account'])) {
            $wherea[] = "users.enabled = '$data[account]'";
        }

        if (!empty($data['uploaded'])) {
            $uploaded = strtobytes($data['uploaded']);
            $wherea[] = "users.uploaded > $uploaded";
        }
        
        if (!empty($data['class'])) {
            $wherea[] = "users.class = '$data[class]'";
        }

        if (!empty($data['downloaded'])) {
            $downloaded = strtobytes($data['downloaded']);
            $wherea[] = "users.downloaded > $downloaded";
        }
        
        if (!empty($data['warned'])) {
            $wherea[] = "users.warned = '$data[warned]'";
        }
        
        $where = implode(' AND ', $wherea);
        if ($where != '') {
            $where = 'WHERE ' . $where;
        }

        // Pagination
        $count = DB::run("SELECT COUNT(`id`) FROM `users` $where")->fetchcolumn();
        list($pager, $limit) = Pagination::pager(25, $count, URLROOT . "/adminsearch/advanced&");
        $results = DB::run("SELECT * FROM `users` $where $limit")->fetchAll();
        
        echo "<pre>".var_dump($_POST)."</pre>";

        $data = [
            'title' => Lang::T("ADVANCED_USER_SEARCH"),
            'results' => $results,
            'pager' => $pager,
            "name" => $_POST['name'],
            "ratio" => $_POST['ratio'],
            "member" => $_POST['member'],
            "email" => $_POST['email'],
            "ip" => $_POST['ip'],
            "account" => $_POST['account'],
            "uploaded" => $_POST['uploaded'],
            "class" => $_POST['class'],
            "downloaded" => $_POST['downloaded'],
            "warned" => $_POST['warned']
        ];
        
        // Load View
        View::render('search/advanced', $data, 'admin');

    }

}