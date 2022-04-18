<?php

class Bans
{
    public static function whereIn($ids)
    {
        $res = DB::run("SELECT * FROM bans WHERE id IN ($ids)");
        
        return $res;
    }

    public static function delete($where)
    {
        DB::delete('bans', $where, false);
    }
    
    public static function insert($added, $addedby, $first, $last, $comment)
    {
        $bins = DB::run("INSERT INTO bans (added, addedby, first, last, comment) VALUES(?,?,?,?,?)", [$added, $addedby, $first, $last, $comment]);
        $err = $bins->errorCode();
        switch ($err) {
            case 1062:
                Redirect::autolink(URLROOT . '/adminban/ip', "Duplicate ban.");
                break;
            case 0:
                Redirect::autolink(URLROOT . '/adminban/ip', "Ban added.");
                break;
            default:
                Redirect::autolink(URLROOT . '/adminban/ip', Lang::T("THEME_DATEBASE_ERROR"));
        }
    }

    public static function checkemail($email)
    {
        $maildomain = (substr($email, strpos($email, "@") + 1));
        $a = DB::column('email_bans', 'COUNT(*)', ['mail_domain'=>$email]);
        if ($a != 0) {
            $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED_S"), $email);
        }
        $a = DB::run("SELECT count(*) FROM email_bans where mail_domain LIKE '%$maildomain%'")->fetchColumn();
        if ($a != 0) {
            $message = sprintf(Lang::T("EMAIL_ADDRESS_BANNED_S"), $email);
        }
        // check if email addy is already in use
        $a = DB::column('users', 'COUNT(*)', ['email'=>$email]);
        if ($a != 0) {
            $message = sprintf(Lang::T("EMAIL_ADDRESS_INUSE_S"), $email);
        }
        return $message;
    }

}