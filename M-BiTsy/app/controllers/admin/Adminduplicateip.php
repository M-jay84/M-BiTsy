<?php

class Adminduplicateip
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Duplicate Ips Default Page
    public function index()
    {
        // Get Ip Data
        $res = DB::run("SELECT ip FROM users GROUP BY ip HAVING count(*) > 1");
        $num = $res->rowCount();
		if ($num == 0) {
            Redirect::autolink(URLROOT."/admincp", Lang::T("No Duplicate IPs."));
        }

        // Pagination
        list($pagerbuttons, $limit) = Pagination::pager(25, $num, 'Adminduplicateip?');
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access, COUNT(*) as count FROM users GROUP BY ip HAVING count(*) > 1 ORDER BY id ASC $limit");
        
        // Init Data
        $data = [
            'title' => Lang::T("DUPLICATEIP"),
            'num' => $num,
            'res' => $res,
        ];

        // Load View
        View::render('duplicuteip/index', $data, 'admin');
    }

}