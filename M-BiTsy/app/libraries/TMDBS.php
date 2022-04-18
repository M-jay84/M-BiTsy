<?php
require_once APPROOT . "/libraries/TMDB.php";

class TMDBS
{
    // Get TMDB Id From Url
    public static function getId($url)
    {
        $parts = explode('/', $url);
        $id_tmdb = strtok($parts[4], '-');
        return $id_tmdb;
    }
    
    // Save Film Details In Database
    public static function createFilm($id_tmdb, $id, $url = null)
    {
        // Get TMDB Api
        $tmdb = new TMDB(); // , true
        $film = $tmdb->getMovie($id_tmdb);
        // Get & Save Image
        $image_url = $tmdb->getImageURL('w300') . "" . $film->getPoster();
        $image = $tmdb->saveImage($image_url, UPLOADDIR . "/tmdb/film/", $id_tmdb);
        // Get Data
        $trailer = $film->getTrailer();
        $title = $film->getTitle();
        $duration = $film->duration();
        $genre = $film->genre();
        $plot = $film->getPlot();
        $actors = $film->actor();
        $casting = $actors;
        $casting_role = $casting[0];
        $casting_role = explode('*', $casting_role);
        $casting_nom = $casting[1];
        $casting_nom = explode('+', $casting_nom);
        $casting_img = $casting[2];
        $casting_img = explode('&', $casting_img);
        for ($i = 0; $i <= 3; $i++) {
            $bdd .= "$casting_nom[$i]*$casting_role[$i]*$casting_img[$i]&";
        }
        // Insert Data
        DB::run("INSERT INTO tmdb 
                 (id_tmdb, title, duration, producer, genre, plot, actor, trailer, date, image, url, type)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                 [$id_tmdb, $title, $duration, 'not working', $genre, $plot, $bdd, $trailer, null, $image, $url, 'movie']);
        // Return To Torrent
        Redirect::to(URLROOT . "/torrent?id=$id");

    }

    // Fetch Movie Data From Database or Cache
    public static function getFilm($id_tmdb)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("tmdb/film/$id_tmdb", $expires)) === false) {
            // Get Data From DB
            $film = DB::select('tmdb', '*', ['id_tmdb'=>$id_tmdb,'type'=>'movie']);
            // Data
            $_data["title"] = $film["title"];
            $_data["duration"] = $film["duration"];
            $_data["producer"] = $film["producer"];
            $_data["genre"] = $film["genre"];
            $_data["plot"] = $film["plot"];
            $_data["actors"] = $film["actor"];
            $_data["trailer"] = $film["trailer"];
            $_data["date"] = $film["date"];
            $_data["poster"] = $film["image"];
            $_data["type"] = "film";
            // Set Data In Cache
            $TTCache->Set("tmdb/film/$id_tmdb", $_data, $expires);
        }
        
        return $_data;
    }

    // Save Show Details In Database
    public static function createSerie($id_tmdb, $id, $url = null)
    {
        // Get TMDB Api
        $tmdb = new TMDB(); // , true
        $serie = $tmdb->getTVShow($id_tmdb);
        // Get & Save Image
        $image_url = $tmdb->getImageURL('w300') . "" . $serie->getPoster();
        $image = $tmdb->saveImage($image_url, UPLOADDIR . "/tmdb/serie/", $id_tmdb);
        // Get Data
        $titre = $serie->getName();
        $season = $serie->getNumSeasons();
        $episode = $serie->getNumEpisodes();
        $status = $serie->getInProduction();
        $date = $serie->date();
        $creator = $serie->creator();
        $genre = $serie->genre();
        $plot = $serie->getOverview();
        $actors = $serie->actor();
        $casting = $actors;
        $casting_role = $casting[0];
        $casting_role = explode('*', $casting_role);
        $casting_nom = $casting[1];
        $casting_nom = explode('+', $casting_nom);
        $casting_img = $casting[2];
        $casting_img = explode('&', $casting_img);
        for ($i = 0; $i <= 3; $i++) {
            $bdd .= "$casting_nom[$i]*$casting_role[$i]*$casting_img[$i]&";
        }
        // Insert Data
        DB::run("INSERT INTO tmdb (id_tmdb, title, image, season, episodes, status, date, creator, genre, plot, actor, url, type)
	             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
                [$id_tmdb, $titre, $image, $season, $episode, $status, $date, $creator, $genre, $plot, str_replace("http:", "https:", $bdd,), $url, 'show']);
        // Return To Torrent
        Redirect::to(URLROOT . "/torrent?id=$id");
    }

    // Fetch Show Data From Database or Cache
    public static function getSerie($id_tmdb)
    {
        $TTCache = new Cache();
        $expires = 1728000; // Cache time in seconds
        if (($_data = $TTCache->Get("tmdb/serie/$id_tmdb", $expires)) === false) {
            // Get Data From DB
            $serie = DB::select('tmdb', '*', ['id_tmdb'=>$id_tmdb,'type'=>'show']);
            // Data
            $_data["poster"] = $serie["image"];
            $_data["title"] = $serie["title"];
            $_data["season"] = $serie["season"];
            $_data["episode"] = $serie["episodes"];
            $_data["status"] = $serie["status"];
            $_data["date"] = $serie["date"];
            $_data["creator"] = $serie["creator"];
            $_data["genre"] = $serie["genre"];
            $_data["plot"] = $serie["plot"];
            $_data["actor"] = $serie["actor"];
            $_data["type"] = "serie";
            // Set Data In Cache
            $TTCache->Set("tmdb/serie/$id_tmdb", $_data, $expires);
        }
        
        return $_data;
    }

}