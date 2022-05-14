<?php

class Download
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }

    // Download Torrent 
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");
        $passkey = Input::get("passkey") == "" ?  Users::get('passkey') : Input::get("passkey");
        
        // Get Data
        $torrent = DB::select('torrents', 'filename, banned, external, announce, owner, vip', ['id'=>$id]);
        $user = DB::run("SELECT users.id, users.class, users.downloadbanned, groups.can_download, groups.view_torrents FROM `users` 
                        LEFT OUTER JOIN `groups` 
                        ON users.class=groups.group_id 
                        WHERE passkey = ?", [$passkey])->fetch(PDO::FETCH_ASSOC) ;

        // Check The Data
        if (!$user && Config::get('MEMBERSONLY')) { //  && Config::get('MEMBERSONLY')
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("no user"));
        }
        if ($user["can_download"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION_TO_DOWNLOAD"));
        }
        if ($user["downloadbanned"] == "yes") {
            Redirect::autolink(URLROOT, Lang::T("DOWNLOADBAN"));
        }
        if (!$torrent) {
            Redirect::autolink(URLROOT . '/home', Lang::T("ID_NOT_FOUND"));
        }
        if ($torrent["banned"] == "yes") {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("BANNED_TORRENT"));
        }
        if ($user['class'] < _VIP && $torrent['vip'] == "yes") {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("VIPTODOWNLOAD"));
        }
        if ($user["view_torrents"] == "no" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
		
        // Thanks
        if (Config::get('FORCETHANKS') && $user) {
            if ($user["id"] != $torrent["owner"]) {
                $like = DB::select('thanks', 'user', ['thanked' => $id, 'type' => 'torrent', 'user' => $user['id']]);
                if (!$like) {
                    Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("PLEASE_THANK"));
                }
            }
        }

        // Check The File
        $fn = UPLOADDIR . "/torrents/$id.torrent";
        if (!is_file($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("FILE_NOT_FILE"));
        }
        if (!is_readable($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], Lang::T("FILE_UNREADABLE"));
        }

        // Name Download File
        $name = $torrent['filename'];
        $friendlyurl = str_replace("http://", "", URLROOT);
        $friendlyname = str_replace(".torrent", "", $name);
        $friendlyext = ".torrent";
        $name = $friendlyname . "[" . $friendlyurl . "]" . $friendlyext;
        
        // Update Hit When Downloaded
        $pvkey_var = "d_".$id;
        $views = $_SESSION[$pvkey_var] == 1 ? $torrent["hits"] + 0 : $torrent["hits"] + 1;
        $_SESSION[$pvkey_var] = 1;
        DB::update('torrents', ['hits'=>$views], ['id'=>$id]);

        // Local Torrent To Add Passkey
        if ($torrent["external"] != 'yes') {
            // Bencode
            $dict = Bencode::decode(file_get_contents($fn));
            $dict['announce'] = sprintf(PASSKEYURL, $passkey);
            unset($dict['announce-list']);
            $data = Bencode::encode($dict);
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header("Content-Type: application/x-bittorrent");
            print $data;
        } else {
            // Download External
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . filesize($fn));
            header("Content-Type: application/x-bittorrent");
            readfile($fn);
        }
    }

}