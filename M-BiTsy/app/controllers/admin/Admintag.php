<?php

class Admintag
{

  public function __construct()
  {
    Auth::user(_SUPERMODERATOR, 2);
  }

  // Tag Default Page
  public function index()
  {
    // Get Tag Data
    $supertag = Tags::getAll();

    // Init Data
    $data = [
      'title' => 'Tags',
      'supertag' => $supertag
    ];

    // Load View
    View::render('tag/index', $data, 'admin');
  }

  // Tag Form Submit
  public function action()
  {
    // Check User Input
    $name = Input::get('name');

    if (!empty($_POST['addtag'])) {
      if (!$name) {
        Redirect::autolink(URLROOT . "/admintag", Lang::T("YOU_DID_NOT_ENTER_ANYTHING"));
      }
      DB::insert('tags', ['name' => $name]);
      Redirect::autolink(URLROOT . '/admintag', 'Tag Added');

    } elseif (!empty($_POST['delete'])) {

      if ($_POST["del"]) {
        if (!@count($_POST["del"])) {
          Redirect::autolink(URLROOT . "/admintag", "Nothing selected to fix.");
        }
        $ids = array_map("intval", $_POST["del"]);
        $ids = implode(",", $ids);
        DB::deleteByIds('tags', 'id', $ids);
        Redirect::autolink(URLROOT . '/admintag', 'Tags Deleted');
      }

    } else {
      Redirect::autolink(URLROOT . '/admintag', 'Strange, try again');
    }
  }

}