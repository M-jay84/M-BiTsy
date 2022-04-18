<?php

class Likes
{

    public static function switch($id, $type)
    {
        switch ($type) {
            
            case 'liketorrent':
                DB::insert('likes', ['user'=>Users::get('id'), 'liked'=>$id, 'added'=>TimeDate::get_date_time(), 'type'=>'torrent', 'reaction'=>'like']);
                Redirect::autolink(URLROOT."/torrent?id=$id", "Thanks you for you appreciation.");
                break;

            case 'unliketorrent':
                DB::delete('likes', ['user' =>Users::get('id'), 'liked' => $id, 'type'=>'torrent']);
                Redirect::autolink(URLROOT."/torrent?id=$id", "Unliked.");
                break;

            default:
                Redirect::autolink(URLROOT, "Thanks you for you appreciation.");
                break;
        }
    }

}