<?php

class Tscraper
{

    // Scrape Torrent
    public static function ScrapeId($id, $annlist, $infohash)
    {
        if (!is_array($annlist)) {
            $torInfo = new Parse();
            $tor = $torInfo->torr(UPLOADDIR."/torrents/$id.torrent");
            $infohash = $tor[1];
            $annlist = array_flatten($tor[6]);
            $var2 = serialize($annlist); 
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

        if ($seeders !== 0) {
            // Update the Torrent
            DB::update('torrents', ['leechers' =>$leechers, 'seeders' =>$seeders, 'times_completed' =>$completed, 'last_action' =>TimeDate::get_date_time(), 'visible' =>'yes', 'announcelist'=>$var2,], ['id'=>$id]);
        } else {
            // Its Dead :(
            DB::update('torrents', ['last_action' =>TimeDate::get_date_time()], ['id' => $id]);
        }
    }

    // Scrape Torrent Batch/All todo
    public static function scrapeall()
    {
        // Set A Limit ? how fast is server / how many torrents to limit ?
        //set_time_limit(15);

        // Rescrape 10 Torrents Every X Days. (Default: 2 days)
        $stmt = DB::run("SELECT `id`, `info_hash`, `last_action`, `announcelist`
                         FROM `torrents` 
                         WHERE `external` = 'yes' 
                         AND `last_action` <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 DAY)
                         LIMIT 10");

        foreach ($stmt as $tor) {
            self::ScrapeId($tor['id'], $tor['announcelist'], $tor['info_hash']);
        }
    }

}