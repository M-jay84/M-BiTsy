<?php

class Thanks
{

    public static function switch($id, $type)
    {
        switch ($type) {

            case 'torrent':
                DB::insert('thanks', ['user'=>Users::get('id'), 'thanked'=>$id, 'added'=>TimeDate::get_date_time(), 'type'=>'torrent']);
                Redirect::autolink(URLROOT."/torrent?id=$id", "Thanks you for you appreciation.");
                break;

            case 'thanksforum':
                DB::insert('thanks', ['user'=>Users::get('id'), 'thanked'=>$id, 'added'=>TimeDate::get_date_time(), 'type'=>'forum']);
                Redirect::autolink(URLROOT."/topic?topicid=$id", "Thanks you for you appreciation.");
                break;

            default:
                Redirect::autolink(URLROOT, "Thanks you for you appreciation.");
                break;
        }
    }

}