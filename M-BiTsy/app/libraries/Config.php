<?php

class Config
{

	// Get Config Array
    public static function get($name) {
		global $config;
        if (isset($config[$name])) {
            return $config[$name];
        } else {
            return false;
        }
	}

}