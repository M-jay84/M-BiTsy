<?php

class Admininactive
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Inactive Users Default Page
    public function index()
    {
        // Inactivite Days
        $cday = empty($_POST['cday']) ? 30 : $_POST['cday'];
        $dt = sqlesc(TimeDate::get_date_time(TimeDate::gmtime() - ($cday * 86400)));
        
        // Get Users Data
        $res = DB::run("SELECT id,username,class,email,uploaded,downloaded,last_access,ip,added FROM users WHERE last_access<$dt AND status='confirmed' AND enabled='yes' ORDER BY last_access DESC ");
        $count = $res->rowCount();

        if ($count > 0) {
            // Init Data
            $data = [
                'title' => Lang::T("Search Users Comments"),
                'cday' => $cday,
                'res' => $res,
                'count' => $count,
            ];
        
            // Load View
            View::render('inactive/index', $data, 'admin');
        } else {
            Redirect::autolink(URLROOT . '/admincp', Lang::T("NOACOUNTINATIVE") . " " . $cday . " " . Lang::T("DAYS") . "");
        }
    }

    // Inactive Users Form Submit
    public function submit()
    {
        $sitename = Config::get('SITENAME');
        $siteurl = Config::get('SITEURL');
        
        if (Input::exist()) {
            // Check User Input
            $action = $_POST["action"];
            $cday = 0 + $_POST["cday"];
            $days = 30; // Number of days of inactivite

            // Check Correct Input
            if (!is_numeric($cday)) {
                Redirect::autolink(URLROOT . '/admininactive', "Int Expected !");
            }

            if (empty($_POST["userid"]) && (($action == "deluser") || ($action == "mail") || ($action == "disable"))) {
                Redirect::autolink(URLROOT . '/admininactive', "For this to work you must select at least a user !");
            }

            if ($action == "cday" && ($cday > $days)) {
                $days = $cday;
            }

            // Delete
            if ($action == "deluser" && (!empty($_POST["userid"]))) {
                $ids = implode(", ", $_POST['userid']);
                DB::deleteByIds('users', 'id', $ids);
                Redirect::autolink(URLROOT . '/admininactive', "You have successfully deleted the selected accounts! <a href=" . URLROOT . "/admininactive>Go back</a>");
            }

            // Disable
            if ($action == "disable" && (!empty($_POST["userid"]))) {
                $res = DB::run("SELECT id, modcomment FROM users WHERE id IN (" . implode(", ", $_POST['userid']) . ") ORDER BY id DESC ");
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = 0 + $arr["id"];
                    $cname = Users::get("username");
                    $modcomment = $arr["modcomment"];
                    $modcomment = gmdate("Y-m-d") . " - Disabled for inactivity by $cname.n" . $modcomment;
                    DB::update('users', ['modcomment' =>$modcomment, 'enabled' =>'no'], ['id' => $id]);
                }
                Redirect::autolink(URLROOT . '/admininactive', "You have successfully disabled the selected accounts! <a href=" . URLROOT . "/inactiveusers.php>Go back</a>");
            }

            // Email
            if ($action == "mail" && (!empty($_POST["userid"]))) {
                $res = DB::run("SELECT id, email , username, added, last_access FROM users WHERE id IN (" . implode(", ", $_POST['userid']) . ") ORDER BY last_access DESC ");
                $count = $res->rowCount();
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = $arr["id"];
                    $username = htmlspecialchars($arr["username"]);
                    $email = htmlspecialchars($arr["email"]);
                    $added = $arr["added"];
                    $last_access = $arr["last_access"];
                    $subject = "Warning for inactive account on $sitename";

                    // Send Email
                    $body = file_get_contents(APPROOT . "/views/user/email/changeemail.php");
                    $body = str_replace("%username%", $username, $body);
                    $body = str_replace("%sitename%", $sitename, $body);
                    $body = str_replace("%siteurl%", $siteurl, $body);

                    $body = str_replace("%added%", $$added, $body);
                    $body = str_replace("%last_access%", $last_access, $body);
                    $TTMail = new TTMail();
                    $mail = $TTMail->Send($email, $subject, $body, "From: " . Config::get('SITEEMAIL') . "", "-f" . Config::get('SITEEMAIL') . "");
                }

                if ($mail) {
                    Redirect::autolink(URLROOT . '/admininactive', "Messages sent.");
                } else {
                    Redirect::autolink(URLROOT . '/admininactive',  "Try again.");
                }

            }
        }

    }

}