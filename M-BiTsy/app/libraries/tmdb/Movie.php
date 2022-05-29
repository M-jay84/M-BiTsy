<?php

class Movie
{
	private $_tmdb;

	// 	Construct Class
	public function __construct($data) {
		$this->_data = $data;
	}

	// 	Get the Movie's id
	public function getID() {
		return $this->_data['id'];
	}

	// 	Get the Movie's title
	public function getTitle() {
		return $this->_data['title'];
	}

	// 	Get the Movie's tagline
	public function getTagline() {
		return $this->_data['tagline'];
	}

	// Calculate Time
	public static function duree($time){
		$tabTemps = array("jours" => 86400,"h." => 60,"min." => 1);
		$result = "";
		foreach($tabTemps as $uniteTemps => $nombreSecondesDansUnite){
		$$uniteTemps = floor($time/$nombreSecondesDansUnite);
		$time = $time%$nombreSecondesDansUnite;
		if($$uniteTemps > 0 || !empty($result))
		$result .= $$uniteTemps." $uniteTemps ";
		}
		return $result;
	}

	// 	Get duration
	public function duration() {
		$duration = $this->_data['runtime'];
		$duration = self::duree($duration);
		return $duration;
	} 
	
	// Get Plot
    public function getplot() {
	   return $this->_data['overview'];
    }
   
   
    // Genre
    public function genre() {
	    $nom ='';
	    $genres = $this->_data['genres'];
	    foreach($genres as $genre)
	    {
		   $nom .= $genre['name']. ', ';
	    }
	    return substr($nom, 0, -2); 
    }
	
	// Actors
    public function actors() {
	    return $this->_data['credits'];
    }

    //	Get the Movie's trailer
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

   // 	Get the Movie's Poster
	public function getPoster() {
		return $this->_data['poster_path'];
	}

	// 	Get the Movie's vote average
	public function getVoteAverage() {
		return $this->_data['vote_average'];
	}

	// 	Get the Movie's vote count
	public function getVoteCount() {
		return $this->_data['vote_count'];
	}

	// 	Get the Movie's trailers
	public function getTrailers() {
		if (empty($this->_data['trailers']) && isset($this->_tmdb)){
			$this->loadTrailer();
		}
		return $this->_data['trailers'];
	}

	// 	Get the Movie's trailer
	public function getTrailer() {
		return $this->getTrailers()['youtube'][0]['source'];
	}

	//  Get Generic.<br> - Get a item of the array, you should not get used to use this, better use specific get's.
	public function get($item = ''){
		return (empty($item)) ? $this->_data : $this->_data[$item];
	}

	// 	Load the images of the Movie -	Used in a Lazy load technique
	public function loadImages(){
		$this->_data['images'] = $this->_tmdb->getMovieInfo($this->getID(), 'images', false);
	}

	//	Load the trailer of the Movie -	Used in a Lazy load technique
	public function loadTrailer() {
		$this->_data['trailers'] = $this->_tmdb->getMovieInfo($this->getID(), 'trailers', false);
	}

	// 	Load the casting of the Movie -	Used in a Lazy load technique
	public function loadCasting(){
		$this->_data['casts'] = $this->_tmdb->getMovieInfo($this->getID(), 'casts', false);
	}

	// 	Load the translations of the Movie -	Used in a Lazy load technique
	public function loadTranslations(){
		$this->_data['translations'] = $this->_tmdb->getMovieInfo($this->getID(), 'translations', false);
	}

	//	Set an instance of the API
	public function setAPI($tmdb){
		$this->_tmdb = $tmdb;
	}

	// 	Get the JSON representation of the Movie
	public function getJSON() {
		return json_encode($this->_data, JSON_PRETTY_PRINT);
	}
}