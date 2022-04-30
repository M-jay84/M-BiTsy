<?php

class Catalog
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Catalog Default Page
    public function index()
    {
        // Check User
        if (Users::get("view_torrents") != "yes" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        // Search Torrents
        $array = torrentsearch('torrents.name');

        // Pagination
        $count = DB::run("SELECT count(id) FROM torrents WHERE $array[query] AND visible != ?", ['no'])->fetchColumn();
        list($pagerbuttons, $limit) = Pagination::pager(28, $count, URLROOT . "/catalog$array[url]&");
        $res = Torrents::catalog($array['query'], $limit);

        // Init Data
        $data = [
            'title' => Lang::T("CATALOGUE"),
            'res' => $res,
            'count' => $count,
            'pagerbuttons' => $pagerbuttons,
        ];

        // Load View
        View::render('catalog/index', $data, 'user');
        
    }
    
}