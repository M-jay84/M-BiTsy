<?php

class Admintask
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // No Default Page
    public function index()
    {
        Redirect::to(URLROOT . '/admincp');
    }

    // Cleanup Form Submit
    public function cleanup()
    {
        DB::update('tasks', ['last_time' =>TimeDate::gmtime()], ['task' => 'cleanup']);
        Cleanup::run();
        Redirect::autolink(URLROOT . '/admincp', Lang::T("FORCE_CLEAN_COMPLETED"));
    }

    // Cache Form Submit
    public function cache()
    {
        # Prune Block Cache.
        $TTCache = new Cache();
        $TTCache->Delete("block/blocks_left");
        $TTCache->Delete("block/blocks_middle");
        $TTCache->Delete("block/blocks_right");
        $TTCache->Delete("block/latestuploadsblock");
        $TTCache->Delete("block/mostactivetorrents_block");
        $TTCache->Delete("block/newestmember_block");
        $TTCache->Delete("block/seedwanted_block");
        $TTCache->Delete("block/usersonline_block");
        $TTCache->Delete("block/request_block");
        Redirect::autolink(URLROOT . "/admincp", 'Purge Cache Successful');
    }

}