<?php

class Scrape
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }
    
    // Scrape Torrent Submit
    public function index()
    {
        //disable error reporting
        error_reporting(0);

        // check if client can handle gzip
        if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
            if (ini_get('output_handler') != 'ob_gzhandler') {
                ob_start("ob_gzhandler");
            } else {
                ob_start();
            }
        } else {
            ob_start();
        }

        $infohash = array();
        foreach (explode("&", $_SERVER["QUERY_STRING"]) as $item) {
            if (preg_match("#^info_hash=(.+)\$#", $item, $m)) {
                $hash = urldecode($m[1]);
                $info_hash = stripslashes($hash);
                if (strlen($info_hash) == 20) {
                    $info_hash = bin2hex($info_hash);
                } elseif (strlen($info_hash) != 40) {
                    continue;
                }
                $infohash[] = strtolower($info_hash);
            }
        }

        if (!count($infohash)) {
            die("Invalid infohash.");
        }

        $query = DB::run("SELECT info_hash, seeders, leechers, times_completed, filename FROM torrents WHERE info_hash IN (" . join(",", $infohash) . ")");
        $result = "d5:filesd";

        while ($row = $query->fetch()) {
            $hash = pack("H*", $row[0]);
            $result .= "20:" . $hash . "d";
            $result .= "8:completei" . $row[1] . "e";
            $result .= "10:downloadedi" . $row[3] . "e";
            $result .= "10:incompletei" . $row[2] . "e";
            $result .= "4:name" . strlen($row[4]) . ":" . $row[4] . "e";
            $result .= "e";
        }

        $result .= "ee";
        echo $result;
        ob_end_flush();
    }

    // Scrape External Torrent Submit
    public function external()
    {
        $id = Input::get('id');

        $resu = DB::raw('torrents', 'id, info_hash, announce, announcelist', ['external' => 'yes','id'=>$id]);
        
        while ($rowu = $resu->fetch(PDO::FETCH_ASSOC)) {
            // No need parsing but lets update any old torrents first
            if ($rowu['announcelist'] == '') {
                $torInfo = new Parse();
                $tor = $torInfo->torr(UPLOADDIR."/torrents/$rowu[id].torrent");
                $infohash = $tor[1];
                $annlist = array_flatten($tor[6]);
                $newannouncelist = serialize($annlist);
            } else {
                $infohash = $rowu['info_hash'];
                $annlist = unserialize($rowu['announcelist']);
                $newannouncelist = $rowu['announcelist'];
            }
            
            $scraper = new Scraper();
            $scraped = $scraper->scrape($infohash, $annlist, 20, 4, true );
            $myarray = array_shift($scraped);

            $seeders = $leechers = $completed = 0;
            if ($myarray['seeders'] > 0) {
                $seeders = $myarray['seeders'];
            }
            if ($myarray['leechers'] > 0) {
                $leechers = $myarray['leechers'];
            }
            if ($myarray['completed'] > 0) {
                $completed = $myarray['completed'];
            }

            if ($seeders !== null) {
                // Update the Torrent
                DB::run("
                UPDATE torrents
                SET leechers = ?, seeders = ?, times_completed = ?, last_action = ?, visible = ?, announcelist = ?
                WHERE id = ?",
                [$leechers, $seeders, $completed, TimeDate::get_date_time(), 'yes', $newannouncelist, $rowu['id']]);

            } else {
                // Its Dead :(
                DB::update('torrents', ['last_action' =>TimeDate::get_date_time()], ['id' => $rowu['id']]);
            }

            // Redirect with message
            if ($seeders !== null) {
                Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("The Tracker is Updated"));
            } else {
                Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("The Torrent seems to be dead"));
            }
        }
    }

}