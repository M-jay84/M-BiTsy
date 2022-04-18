<?php

class Adminprivacy
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Privacy Default Page
    public function index()
    {
        $where = array();
        switch ($_GET['type']) {
            case 'low':
                $where[] = "privacy = 'low'";
                break;
            case 'normal':
                $where[] = "privacy = 'normal'";
                break;
            case 'strong':
                $where[] = "privacy = 'strong'";
                break;
            default:
                break;
        }
        $where[] = "enabled = 'yes'";
        $where[] = "status = 'confirmed'";
        $where = implode(' AND ', $where);
        
        // Pagination
        $count = get_row_count("users", "WHERE $where");
        list($pagerbuttons, $limit) = Pagination::pager(25, $count, htmlspecialchars($_SERVER['REQUEST_URI'] . '&'));
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE $where ORDER BY username DESC $limit");

        // Init Data
        $data = [
            'title' => Lang::T("PRIVACY_LEVEL"),
            'count' => $count,
            'res' => $res,
            'pagerbuttons' => $pagerbuttons,
        ];

        // Load View
        View::render('privacy/index', $data, 'admin');
    }

}