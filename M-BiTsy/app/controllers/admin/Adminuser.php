<?php

class Adminuser
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // No Default Page
    public function index()
    {
        Redirect::to(URLROOT . '/admincp');
    }

    // Add User Default Page
    public function add()
    {
        // Init Data
        $data = [
            'title' => 'Add User',
        ];

        // Load View
        View::render('user/adduser', $data, 'admin');
    }

    // Add User Form Submit
    public function addeduserok()
    {
        if (Input::exist()) {
            // Check User Input
            $username = $_POST["username"];
            $password = $_POST["password"];
            $password2 = $_POST["password2"];
            $email = $_POST["email"];
            $secret = Security::mksecret();
            $passhash = password_hash($password, PASSWORD_BCRYPT);
            
            // Check Correct Input
            if ($username == "" || $password == "" || $email == "") {
                Redirect::autolink(URLROOT . "/adminuser/add", "Missing form data.");
            }

            if ($password != $password2) {
                Redirect::autolink(URLROOT . "/adminuser/add", "Passwords mismatch.");
            }
            
            // Insert
            $count = DB::column('users', 'count(*)', ['username'=>$username]);
            if ($count !=0) {
                Redirect::autolink(URLROOT . "/adminuser/add", "Unable to create the account. The user name is possibly already taken.");
            }
            DB::insert('users', ['added'=>TimeDate::get_date_time(), 'last_access'=>TimeDate::get_date_time(), 'secret'=>$secret, 'username'=>$username, 'password'=>$passhash, 'status'=>'confirmed', 'email'=>$email]);
            Redirect::autolink(URLROOT.'/admincp', Lang::T("COMPLETE"));
        } else {
            Redirect::autolink(URLROOT.'/adminuser/add', Lang::T("ERROR"));
        }
    }

    // Confirm User Default Page
    public function confirm()
    {
        $do = $_GET['do'];
        if ($do == "confirm") {
            if ($_POST["confirmall"]) {
                DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0'");
            } else {
                if (!@count($_POST["users"])) {
                    Redirect::autolink(URLROOT."/adminusers/confirm", Lang::T("NOTHING_SELECTED"));
                }
                $ids = array_map("intval", $_POST["users"]);
                $ids = implode(", ", $ids);
                DB::run("UPDATE `users` SET `status` = 'confirmed' WHERE `status` = 'pending' AND `invited_by` = '0' AND `id` IN ($ids)");
            }
            Redirect::autolink(URLROOT . "/Adminuser/confirm", "Entries Confirmed");
        }
        
        // Pagination
        $count = get_row_count("users", "WHERE status = 'pending' AND invited_by = '0'");
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, '' . URLROOT . '/Adminuser/confirm?');
        $res = DB::raw('users', 'id,username,email,added,ip', ['status'=>'status','invited_by'=>0], "ORDER BY `added` DESC $limit");

        // Init Data
        $data = [
            'title' => Lang::T("Manual Registration Confirm"),
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
            'res' => $res,
        ];

        // Load View
        View::render('user/confirmreg', $data, 'admin');
    }

}