<?php

class Adminwarning
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Warning Default Page
    public function index()
    {
        // Pagination
        $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' AND warned = 'yes'");
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT.'/adminwarning?');
        $res = DB::raw('users', 'id,username,class,added,last_access', ['status'=>'confirmed','enabled'=>'yes','warned'=>'yes'], "ORDER BY `added` DESC $limit");
        
        // Init Data
        $data = [
            'title' => "Warned Users",
            'pagerbuttons' => $pagerbuttons,
            'count' => $count,
            'res' => $res,
        ];

        // Load View
        View::render('warning/index', $data, 'admin');
    }

    // Warning Form Submit
    public function submit()
    {
        if ($_POST["removeall"]) {

            $res = DB::raw('users', 'id', ['enabled' => 'yes', 'status' => 'confirmed', 'warned' => 'yes']);
            while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                DB::delete('warnings', ['active' =>'yes', 'userid'=>$row['id']]);
                DB::update('users', ['warned' =>'no'], ['id' => $row['id']]);
            }

        } else {

            if (!@count($_POST['warned'])) {
                Redirect::autolink(URLROOT . "/adminwarning", Lang::T("NOTHING_SELECTED"));
            }
            $ids = array_map("intval", $_POST["warned"]);
            $ids = implode(", ", $ids);
            DB::deleteByIds('warnings', ['active'=>'yes'], $ids, 'userid');
            DB::run("UPDATE `users` SET `warned` = 'no' WHERE `id` IN ($ids)");
            
        }
        Redirect::autolink(URLROOT . "/adminwarning", "Entries Confirmed");
    }
    
}