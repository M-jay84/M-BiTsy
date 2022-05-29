<?php

class TVShow
{
    private $_data;

    // 	Construct Class
    public function __construct($data) {
        $this->_data = $data;
    }

    // 	Get the TVShow's id
    public function getID() {
        return $this->_data['id'];
    }

    //	Get the TVShow's name
    public function getName() {
        return $this->_data['name'];
    }

    // 	Get the TVShow's original name
    public function getOriginalName() {
        return $this->_data['original_name'];
    }

    // 	Get the TVShow's number of seasons
    public function getNumSeasons() {
        return $this->_data['number_of_seasons'];
    }

    //  Get the TVShow's number of episodes
    public function getNumEpisodes() {
        return $this->_data['number_of_episodes'];
    }

    // Get a TVShow's season
    public function getSeason($numSeason) {
        foreach($this->_data['seasons'] as $season){
            if ($season['season_number'] == $numSeason){
                $data = $season;
                break;
            }
        }
        return new Season($data);
    }

    //  Get the TvShow's seasons
    public function getSeasons() {
        $seasons = array();

        foreach($this->_data['seasons'] as $data){
            $seasons[] = new Season($data, $this->getID());
        }

        return $seasons;
    }

    // 	Get the TVShow's Poster
    public function getPoster() {
        return $this->_data['poster_path'];
    }

    // 	Get the TVShow's Backdrop
    public function getBackdrop() {
        return $this->_data['backdrop_path'];
    }

    // 	Get the TVShow's Overview
    public function getOverview() {
        return $this->_data['overview'];
    }

    // 	Get the TVShow's vote average
    public function getVoteAverage() {
        return $this->_data['vote_average'];
    }

    // 	Get the TVShow's vote count
    public function getVoteCount() {
        return $this->_data['vote_count'];
    }

    // 	Get if the TVShow is in production
    public function getInProduction() {
        return $this->_data['in_production'];
    }
    
    // Creator
    public function creator() {
        $nom ='';
        $creators = $this->_data['created_by'];
        foreach($creators as $creator)
        {
            $nom .= $creator['name']. ', ';
        }
        return substr($nom, 0, -2);
    }

    public function actors() {
        return $this->_data['credits'];
    }
    
    // 	Get the Movie's trailer
    public function actor() {
        $role ='';
        $nom='';
        $img='';
        $actor = $this->actors();
        for($i=0;$i<=3;$i++){
            $role .= $actor['cast'][$i]['character'].' * ';
            $nom .= $actor['cast'][$i]['name'].' + ';
            $img .= 'http://image.tmdb.org/t/p/w92'.$actor['cast'][$i]['profile_path'].' & ';
		    $id .= $actor['cast'][$i]['id'].' & ';
        }
        return array(substr($role, 0, -2),substr($nom, 0, -2),substr($img, 0, -2), $id); 
    }
        
    // Date Created
    public function date() {
        $date = $this->_data['first_air_date'];
        $time = date('Y-m-d H:i:s',strtotime($date));
        return $time; 
    }
    
    //genre
    public function genre() {
        $nom ='';
        $genres = $this->_data['genres'];
        foreach($genres as $genre)
        {
            $nom .= $genre['name']. ', ';
        }
        return substr($nom, 0, -2); 
    }

    //  Get Generic.<br>
    public function get($item = ''){
        return (empty($item)) ? $this->_data : $this->_data[$item];
    }

    // 	Get the JSON representation of the TVShow
    public function getJSON() {
        return json_encode($this->_data, JSON_PRETTY_PRINT);
    }
}