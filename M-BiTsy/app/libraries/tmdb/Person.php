<?php

class Person
{

    private $_data;

    // 	Construct Class
    public function __construct($data) {
        $this->_data = $data;
    }

    // Get the Person's name
    public function getName() {
        return $this->_data['name'];
    }

    //  Get the Person's id
    public function getID() {
        return $this->_data['id'];
    }

    //  Get the Person's profile image
    public function getProfile() {
        return $this->_data['profile_path'];
    }

    public function getJob() {
        return $this->_data['job'];
    }

    //  Get the Person's birthday
    public function getBirthday() {
        return $this->_data['birthday'];
    }

    //  Get the Person's place of birth
    public function getPlaceOfBirth() {
        return $this->_data['place_of_birth'];
    }

    // Get the Person's imdb id
    public function getImbdID() {
        return $this->_data['imdb_id'];
    }

    //  Get the Person's popularity
    public function getPopularity() {
        return $this->_data['popularity'];
    }

    //  Get the Person's MovieRoles
    public function getMovieRoles() {
        $movieRoles = array();

        foreach($this->_data['movie_credits']['cast'] as $data){
            $movieRoles[] = new MovieRole($data, $this->getID());
        }

        return $movieRoles;
    }

    //  Get the Person's TVShowRoles
    public function getTVShowRoles() {
        $tvShowRole = array();

        foreach($this->_data['tv_credits']['cast'] as $data){
            $tvShowRole[] = new TVShowRole($data, $this->getID());
        }

        return $tvShowRole;
    }

    //  Get a item of the array, you should not get used to use this, better use specific get's.
    public function get($item = ''){
        return (empty($item)) ? $this->_data : $this->_data[$item];
    }

    //  Get the JSON representation of the Episode
    public function getJSON() {
        return json_encode($this->_data, JSON_PRETTY_PRINT);
    }
}