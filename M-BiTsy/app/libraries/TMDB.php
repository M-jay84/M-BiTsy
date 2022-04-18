<?php
include APPROOT . "/libraries/tmdb/Movie.php";
include APPROOT . "/libraries/tmdb/TVShow.php";
include APPROOT . "/libraries/tmdb/Season.php";
include APPROOT . "/libraries/tmdb/Episode.php";
include APPROOT . "/libraries/tmdb/Person.php";
include APPROOT . "/libraries/tmdb/Role.php";
include APPROOT . "/libraries/tmdb/roles/MovieRole.php";
include APPROOT . "/libraries/tmdb/roles/TVShowRole.php";
include APPROOT . "/libraries/tmdb/Collection.php";

class TMDB
{
    const _API_URL_ = "http://api.themoviedb.org/3/";

    // Not sure why but we need the config in constructor - maybe to work with json arrays http://api.themoviedb.org/3/configuration?api_key=apikey&language=en&
    public function __construct()
    {
        global $cnf;
        $cnf = $this->_call('configuration', '');
        // returns a config array - normally use tmdb url _call('configuration', '')
        if (!$cnf) {
            echo "Unable to read configuration, verify that the API key is valid";
            exit;
        }
    }

    // Call A TMDB Url
    private function _call($action, $appendToResponse)
    {
        $url = self::_API_URL_ . $action . '?api_key=' . _TMDBAPIKEY . '&language=' . _TMDBLANG . '&' . $appendToResponse; // todo lang var

        if (_TMDBDEBUG) {
            echo '<pre><a href="' . $url . '">check request</a></pre>';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);

        $results = curl_exec($ch);

        curl_close($ch);

        return (array) json_decode(($results), true);
    }

    // Get Image From Url
    public static function url_get_contents($Url)
    {
        if (!function_exists('curl_init')) {
            header("HTTP/1.0 403 Forbidden");
            echo '<html><head><title>Forbidden</title> </head><body> <h1>Forbidden</h1>CURL is not installed!.<br> </body></html>';
            die();
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    // Save Image To Folder
    public function saveImage(&$image = null, $path = null, $id = 0)
    {
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $iid = sprintf('%d.%s', $id, $ext);

        if (($data = self::url_get_contents($image)) && (file_put_contents($path . $iid, $data))) {
            return $iid;
        }
        return (bool) false;
    }

    // Get the URL images
    public function getImageURL($size = 'original')
    {
        global $cnf;
        return $cnf['images']['base_url'] . $size;
    }

    // Find External id Like IMDB $tmdb->find('tt1877830');
	public function find($id, $external_source = 'imdb_id'){
		$found = array();
		$result = $this->_call('find/'.$id, '&external_source='. urlencode($external_source));
        // eg https://api.themoviedb.org/3/find/tt3107288?api_key=fc104461a1313e89bced780eb81a53a4&language=en-US&external_source=imdb_id
		$found = [
            'title' => $result["movie_results"][0]["original_title"],
            'poster_path' => $result["movie_results"][0]["poster_path"],
            'overview' => $result["movie_results"][0]["overview"],
            'release_date' => $result["movie_results"][0]["release_date"],
            'video' => $result["movie_results"][0]["video"],
        ];
		return $found;
	}

    // Gets part of the info of the Movie, mostly used for the lazy load
    public function getMovieInfo($idMovie, $option = '', $append_request = '')
    {
        $option = (empty($option)) ? '' : '/' . $option;
        $params = 'movie/' . $idMovie . $option;
        $result = $this->_call($params, $append_request);
        return $result;
    }

    // Get latest Movie
    public function getLatestMovie()
    {
        return new Movie($this->_call('movie/latest', ''));
    }

    // Now Playing Movies
    public function nowPlayingMovies($page = 1)
    {
        $movies = array();
        $result = $this->_call('movie/now-playing', 'page=' . $page);
        foreach ($result['results'] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }

    // Get Lists of Persons
    public function getLatestPerson()
    {
        return new Person($this->_call('person/latest', ''));
    }

    // Get Popular Persons
    public function getPopularPersons($page = 1)
    {
        $persons = array();
        $result = $this->_call('person/popular', 'page=' . $page);
        foreach ($result['results'] as $data) {
            $persons[] = new Person($data);
        }
        return $persons;
    }

    // Get a Movie
    public function getMovie($idMovie, $appendToResponse = 'append_to_response=trailers,images,credits,translations')
    {
        return new Movie($this->_call('movie/' . $idMovie, $appendToResponse));
    }

    // Get a TVShow
    public function getTVShow($idTVShow, $appendToResponse = 'append_to_response=trailers,images,credits,translations,keywords')
    {
        return new TVShow($this->_call('tv/' . $idTVShow, $appendToResponse));
    }

    // Get a Season
    public function getSeason($idTVShow, $numSeason, $appendToResponse = 'append_to_response=trailers,images,casts,translations')
    {
        return new Season($this->_call('tv/' . $idTVShow . '/season/' . $numSeason, $appendToResponse), $idTVShow);
    }

    // Get a Episode
    public function getEpisode($idTVShow, $numSeason, $numEpisode, $appendToResponse = 'append_to_response=trailers,images,casts,translations')
    {
        return new Episode($this->_call('tv/' . $idTVShow . '/season/' . $numSeason . '/episode/' . $numEpisode, $appendToResponse), $idTVShow);
    }

    // Get a Person
    public function getPerson($idPerson, $appendToResponse = 'append_to_response=tv_credits,movie_credits')
    {
        return new Person($this->_call('person/' . $idPerson, $appendToResponse));
    }

    // Get a Collection
    public function getCollection($idCollection, $appendToResponse = 'append_to_response=images')
    {
        return new Collection($this->_call('collection/' . $idCollection, $appendToResponse));
    }

    // Search Movie
    public function searchMovie($movieTitle)
    {
        $movies = array();
        $result = $this->_call('search/movie', 'query=' . urlencode($movieTitle), _TMDBLANG);
        foreach ($result['results'] as $data) {
            $movies[] = new Movie($data);
        }
        return $movies;
    }

    // Search TVShow
    public function searchTVShow($tvShowTitle)
    {
        $tvShows = array();
        $result = $this->_call('search/tv', 'query=' . urlencode($tvShowTitle), _TMDBLANG);
        foreach ($result['results'] as $data) {
            $tvShows[] = new TVShow($data);
        }
        return $tvShows;
    }

    // Search Person
    public function searchPerson($personName)
    {
        $persons = array();
        $result = $this->_call('search/person', 'query=' . urlencode($personName), _TMDBLANG);
        foreach ($result['results'] as $data) {
            $persons[] = new Person($data);
        }
        return $persons;
    }

    // Search Collection
    public function searchCollection($collectionName)
    {
        $collections = array();
        $result = $this->_call('search/collection', 'query=' . urlencode($collectionName), _TMDBLANG);
        foreach ($result['results'] as $data) {
            $collections[] = new Collection($data);
        }
        return $collections;
    }

}