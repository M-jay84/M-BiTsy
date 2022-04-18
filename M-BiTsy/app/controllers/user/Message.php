<?php

class Message
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // Message Default Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get('id');
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        // Set Button
        if ($type == 'templates' || $type == 'draft') {
            $button = "<a href='" . URLROOT . "/message/update?id=$id&type=$type=$id'><button  class='btn btn-sm ttbtn'>Edit</button></a>";
        } elseif ($type == 'inbox' || $type == 'outbox') {
            $button = " <a href='" . URLROOT . "/message/reply?id=$id&type=$type'><button  class='btn btn-sm ttbtn'>Reply</button></a>
                        <a href='" . URLROOT . "/message/update?id=$id&$type'><button  class='btn btn-sm ttbtn'>Edit</button></a>";
        }
        
        // Get Message
        $arr = DB::select('messages', '*', ['id'=>$id]);

        // Check User
        if ($arr["sender"] != Users::get('id') && $arr["receiver"] != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T('NO_PERMISSION'));
        }

        // Mark Read
        if ($arr["unread"] == "yes" && $arr["receiver"] == Users::get('id')) {
            DB::update('messages', ['unread'=>'no'], ['id'=>$arr['id'], 'receiver'=>Users::get('id')]);
        }
        
        // Init Dta
        $data = [
            'title' => Lang::T('MESSAGE'),
            'id' => $id,
            'button' => $button,
            'sender' => $arr['sender'],
            'subject' => $arr['subject'],
            'added' => $arr['added'],
            'msg' => $arr['msg'],
        ];

        // Load Data
        View::render('message/index', $data, 'user');
    }

    // Ajax User search
	function findUser()
    {
        if(!empty($_POST["keyword"])) {
            $result = DB::run("SELECT id,username FROM users WHERE username like '" . $_POST["keyword"] . "%' ORDER BY username LIMIT 0,6");
            if(!empty($result)) {
                ?>
                <ul id="country-list">
                <?php foreach($result as $user) { ?>
                    <li onClick="userCountry('<?php echo $user["username"]; ?>');"><?php echo $user["username"]; ?></li>
                <?php } ?>
                </ul>
                <?php
            }
        }
    }

    // Create Message Default Page
    public function create()
    {
        // Check User Input
        $id = (int) Input::get('id');

        // Init Dta
        $data = [
            'title' => Lang::T('ACCOUNT_SEND_MSG'),
            'id' => $id
        ];

        // Load Data
        View::render('message/create', $data, 'user');
    }

    // Create/Reply Message Form Submit
    public function submit()
    {
        // Check User Input
        $receiver = Input::get('receiver');
        $subject = Input::get('subject');
        $body = Input::get('body');

        // Check Correct Input
        if (strlen($body) < 5) {
            Redirect::autolink(URLROOT . "/mailbox/overview", Lang::T('Body Too Short'));
        }
        if ($receiver == "") {
            Redirect::autolink(URLROOT . "mailbox/overview", Lang::T('EMPTY_RECEIVER'));
        }
        if (strlen($subject) < 3) {
            Redirect::autolink(URLROOT . "/mailbox/overview", Lang::T('EMPTY_SUBJECT'));
        }
        
        // Get User Id
        if (!is_numeric($receiver)) {
            $receiver = DB::column('users', 'id', ['username'=>$receiver]);
        }

        // Send Message
        Messages::insertbytype($_REQUEST['Update'], $receiver, $subject, $body);
    }

    // Message Reply Default Page
    public function reply()
    {
        // Check User Input
        $url_id = isset($_GET['id']) ? $_GET['id'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        // Get Message
        $row = Messages::getallmsg($url_id);
        if ($row["sender"] != Users::get('id') && $row["receiver"] != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T('NO_PERMISSION'));
        }

        // Check Type
        if ($type == 'inbox') {
            $arr2 =  DB::raw('users', 'username,id', ['id'=>$row['sender']])->fetch(PDO::FETCH_LAZY);
        } else {
            $arr2 = DB::raw('users', 'username,id', ['id'=>$row['receiver']])->fetch(PDO::FETCH_LAZY);
        }

        // Init Dta
        $data = [
            'title' => Lang::T('Reply To Message'),
            'username' => $arr2["username"],
            'userid' => $arr2['id'],
            'msg' => $row['msg'],
            'subject' => $row['subject'],
            'id' => $row['id'],
        ];

        // Load Data
        View::render('message/reply', $data, 'user');
    }

    // Message Edit Default Page
    public function update()
    {
        // Check User Input
        $url_id = $_GET['id'];
        
        // Get Message
        $row = Messages::getallmsg($url_id);
        if ($row["sender"] != Users::get('id') && $row["receiver"] != Users::get('id')) {
            Redirect::autolink(URLROOT, Lang::T('NO_PERMISSION'));
        }
        if (!$row) {
            Redirect::autolink(URLROOT . '/mailbox?type=inbox', Lang::T("INVALID_ID"));
        }
        
        // Submit Form
        if (isset($_GET['id'])) {
            if (!empty($_POST)) {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
                // Update Message
                DB::update('messages', ['msg'=>$msg], ['id'=>$id]);
                Redirect::autolink(URLROOT . '/mailbox?type=inbox', "Edited Successfully !");
            }
        }

        // Init Dta
        $data = [
            'title' => 'Edit Message',
            'msg' => $row['msg'],
            'subject' => $row['subject'],
            'id' => $row['id'],
        ];

        // Load Data
        View::render('message/update', $data, 'user');
    }

    // Delete Message Submit
    public function delete()
    {
        // Check User Input
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        if ($_POST["read"]) {
            
            if (!isset($_POST["del"])) {
                Redirect::autolink(URLROOT . "/mailbox?type=$type", Lang::T("NOTHING_SELECTED"));
            }

            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);

            // Update Read
            DB::run("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($ids)");
            Redirect::autolink(URLROOT . "/mailbox?type=$type", Lang::T("COMPLETED"));

        } else {
            
            if (!isset($_POST["del"])) {
                Redirect::autolink(URLROOT . "/mailbox?type=$type", Lang::T("NOTHING_SELECTED"));
            }

            $ids = array_map("intval", $_POST["del"]);
            $ids = implode(", ", $ids);

            // Delete Message
            Messages::delete($type, $ids);
            Redirect::autolink(URLROOT . "/mailbox?type=$type", Lang::T("COMPLETED"));
            
        }
    }
}