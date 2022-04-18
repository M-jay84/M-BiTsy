<?php
class Messages
{

    public static function countmsg()
    {
        $res = DB::run("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = ".Users::get('id')." AND `location` IN ('in','both')");
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . Users::get("id") . " AND `location` IN ('in','both')");
        $inbox = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `receiver` = " . Users::get("id") . " AND `location` IN ('in','both') AND `unread` = 'yes'");
        $unread = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . Users::get("id") . " AND `location` IN ('out','both')");
        $outbox = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . Users::get("id") . " AND `location` = 'draft'");
        $draft = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) AS count FROM messages WHERE `sender` = " . Users::get("id") . " AND `location` = 'template'");
        $template = $res->fetchColumn();

        $arr = [
            'inbox' => $inbox,
            'unread' => $unread,
            'outbox' => $outbox,
            'draft' => $draft,
            'template' => $template,
        ];
        return $arr;
    }

    // Templates Drop Down
    public static function echotemplates()
    {
        $templates = "<option value=\"0\">---- " . Lang::T("NONE_SELECTED") . " ----</option>\n";
        $stmt = DB::raw('messages', '*', ['sender' =>Users::get('id'), 'location'=>'template'], 'ORDER BY `subject`');
        foreach ($stmt as $arr) {
            $templates .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
        }
        echo $templates;
    }
    
    public static function insert($data)
    {
        DB::insert('messages', $data);
        // email notif
        $user = DB::run("SELECT id, username, acceptpms, notifs, email FROM users WHERE id=?", [$data['receiver']])->fetch(PDO::FETCH_ASSOC);
        if (strpos($user['notifs'], '[pm]') !== false) {
            $url = URLROOT;
            $body = "You have received a PM\n\nYou can use the URL below to view the message (you may have to login).\n\n$url/mailbox\n\n".Config::get('SITENAME')."";
            $email = Config::get('SITEEMAIL');
            $TTMail = new TTMail();
            $TTMail->Send($user["email"], "You have received a PM", $body, "From: " . Config::get('SITEEMAIL') . "", "-f" . Config::get('SITEEMAIL') . "");
        }
    }

    public static function getallmsg($id)
    {
        $res = DB::run('SELECT * FROM messages WHERE id = ?', [$id])->fetch(PDO::FETCH_ASSOC);
        return $res;
    }

    public static function msgPagination($type)
    {
        switch ($type) {
            case 'inbox':
                $where = "`receiver` = ".Users::get('id')." AND `location` IN ('in','both') ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT."/mailbox?type=inbox&");
                break;
            case 'outbox':
                $where = "`sender` = ".Users::get('id')." AND `location` IN ('out','both') ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT."/mailbox?type=outbox&");
                break;
            case 'templates':
                $where = "`sender` = ".Users::get('id')." AND `location` = 'template' ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT."/mailbox?type=templates&");
                break;
            case 'draft':
                $where = "`sender` = ".Users::get('id')." AND `location` = 'draft' ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = Pagination::pager(25, $count, URLROOT."/mailbox?type=draft&");
                break;
            }
        $arr = ['pagerbuttons' => $pagerbuttons, 'limit' => $limit, 'where' => $where];
        return $arr;
    }

    public static function insertbytype($type, $receiver, $subject, $body)
    {
        switch ($type) {
            case 'create':
                if (isset($_POST['save'])) {
                    Messages::insert(['sender'=>Users::get('id'), 'receiver'=>$receiver, 'added'=>TimeDate::get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'yes', 'location'=>'both']);
                } else {
                    Messages::insert(['sender'=>Users::get('id'), 'receiver'=>$receiver, 'added'=>TimeDate::get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'yes', 'location'=>'in']);
                }
                Redirect::autolink(URLROOT . "/mailbox?type=outbox", Lang::T('MESSAGES_SENT'));
                break;
            case 'draft':
                Messages::insert(['sender'=>Users::get('id'), 'receiver'=>$receiver, 'added'=>TimeDate::get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'no', 'location'=>'draft']);
                Redirect::autolink(URLROOT . "/mailbox?type=draft", Lang::T('SAVED_DRAFT'));
                break;
            case 'template':
                Messages::insert(['sender'=>Users::get('id'), 'receiver'=>$receiver, 'added'=>TimeDate::get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'no', 'location'=>'template']);
                Redirect::autolink(URLROOT . "/mailbox?type=templates", Lang::T('SAVED_TEMPLATE'));
                break;
        }
    }

    public static function delete($type, $ids)
    {
        if ($type == 'inbox') {
            DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = ".Users::get('id')." AND `id` IN ($ids)");
            DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = ".Users::get('id')." AND `id` IN ($ids)");
        } elseif ($type == 'outbox') {
            DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = ".Users::get('id')." AND `id` IN ($ids)");
            DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = ".Users::get('id')." AND `id` IN ($ids)");
        } elseif ($type == 'templates') {
            DB::deleteByIds('messages', ['sender'=>Users::get('id'),'location'=>'template'], $ids, 'id');
        } elseif ($type == 'draft') {
            DB::deleteByIds('messages', ['sender'=>Users::get('id'),'location'=>'draft'], $ids, 'id');
        }
    }
}