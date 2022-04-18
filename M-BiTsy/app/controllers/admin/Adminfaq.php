<?php

class Adminfaq
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // FAQ Default Page
    public function index()
    {
        // Get FAQ Data
        $faq_categ = Faqs::select();

        // Init Data
        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
            'faq_categ' => $faq_categ,
        ];

        // Load View
        View::render('faq/index', $data, 'admin');
    }
    
    // Order FAQ Submit
    public function reorder()
    {
        foreach ($_POST['order'] as $id => $position) {
            DB::run("UPDATE `faq` SET `order`='$position' WHERE id='$id'");
        }
        Redirect::to(URLROOT . "/adminfaq");
    }

    // Delete FAQ Submit
    public function delete()
    {
        if ($_GET['confirm'] == "yes") {
            DB::delete('faq', ['id'=>$_GET['id']], 1);
            Redirect::to(URLROOT . "/adminfaq");
        } else {
            Redirect::autolink(URLROOT . '/adminfaq',  "Please click <a href=\"" . URLROOT . "/adminfaq/delete?id=$_GET[id]&amp;confirm=yes\">here</a> to confirm.", URLROOT . "/adminfaq");
        }
    }

    // FAQ Add Section Default Page
    public function newsection()
    {
        // Form Submit
        if ($_POST['action'] == "addnewsect" && $_POST['title'] != null && Validate::Int($_POST['flag'])) {
            $title = $_POST['title'];
            $res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='categ'");
            while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                $order = $arr[0] + 1;
            }
            DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES (?,?,?,?,?,?)", ['categ', $title, '', $_POST['flag'], 0, $order]);
            Redirect::to(URLROOT . "/adminfaq");
        }

        // Init Data
        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
        ];

        // Load View
        View::render('faq/newsection', $data, 'admin');
    }

    // FAQ Add Item Default Page
    public function additem()
    {
        // Form Submit
        if ($_POST['question'] != null && $_POST['answer'] != null && Validate::Int($_POST['flag']) && Validate::Int($_POST['categ'])) {
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            $res = DB::run("SELECT MAX(`order`) FROM `faq` WHERE `type`='item' AND `categ`='$_POST[categ]'");
            while ($arr = $res->fetch(PDO::FETCH_BOTH)) {
                $order = $arr[0] + 1;
            }
            DB::run("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES (?,?,?,?,?,?)", ['item', $question, $answer, $_POST['flag'], $_POST['categ'], $order]);
            Redirect::to(URLROOT . "/adminfaq");
        }

        // Get FAQ Data
        $res = DB::run("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");

        // Init Data
        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
            'res' => $res
        ];

        // Load View
        View::render('faq/newitem', $data, 'admin');
    }

    // FAQ Edit Section/Item Default Page
    public function edit()
    {
        // Edit Item Submit
        if ($_GET['action'] == "edititem" && Validate::Id($_POST['id']) && $_POST['question'] != null && $_POST['answer'] != null && Validate::Int($_POST['flag']) && Validate::Id($_POST['categ'])) {
            $question = $_POST['question'];
            $answer = $_POST['answer'];
            DB::update('faq', ['question'=>$question, 'answer'=>$answer, 'flag'=>$_POST['flag'], 'categ'=>$_POST['categ']], ['id' => $_POST['id']]);
            Redirect::to(URLROOT . "/adminfaq");
        }
        
        // Edit Section Submit
        if ($_GET['action'] == "editsect" && Validate::Id($_POST['id']) && $_POST['title'] != null && Validate::Int($_POST['flag'])) {
            $title = $_POST['title'];
            DB::update('faq', ['question'=>$title, 'answer'=>$_POST['flag'], 'flag'=>'', 'categ'=>0], ['id' => $_POST['id']]);
            Redirect::to(URLROOT . "/adminfaq");
        }
        
        // Get FAQ Data
        $res = DB::raw('faq', '*', ['id' => $_GET['id']]);
        $res2 = DB::raw('faq', 'id,question', ['type' => 'categ'], 'LIMIT 1');
       
        // Init Data
        $data = [
            'title' => Lang::T("FAQ_MANAGEMENT"),
            'res' => $res,
            'res2' => $res2,
        ];

        // Load View
        View::render('faq/edit', $data, 'admin');
    }

}