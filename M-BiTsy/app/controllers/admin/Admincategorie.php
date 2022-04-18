<?php

class Admincategorie
{

    public function __construct()
    {
        // Verify User/Staff
        Auth::user(_MODERATOR, 2);
    }

    // Categorie Default Page
    public function index()
    {
        // Get Categorie
        $sql = DB::all('categories', '*', '', 'ORDER BY parent_cat ASC, sort_index ASC');
        
        // Init Data
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
            'sql' => $sql,
        ];

        // Load View
        View::render('categorie/index', $data, 'admin');
    }

    // Categorie Edit Default Page
    public function edit()
    {
        // Check User Input
        $id = (int) $_GET["id"];

        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "/admincategorie", Lang::T("INVALID_ID"));
        }

        // Get Categorie
        $res = DB::all('categories', '*', ['id'=>$id]);
        if (count($res) != 1) {
            Redirect::autolink(URLROOT . "/admincategorie", "No category with ID $id.");
        }

        
        // Form Submit OR View
        if ($_GET["save"] == '1') {
            // Check User Input
            $name = $_POST['name'];
            $sort_index = $_POST['sort_index'];
            $image = $_POST['image'];
            $parent_cat = $_POST['parent_cat'];

            // Check Correct Input
            if ($name == "") {
                Redirect::autolink(URLROOT . "/admincategorie/edit", "Sub cat cannot be empty!");
            }
            if ($parent_cat == "") {
                Redirect::autolink(URLROOT . "/admincategorie/edit", "Parent Cat cannot be empty!");
            }
            
            // Update
            DB::update('categories', ['parent_cat' => $parent_cat, 'name' => $name, 'sort_index' => $sort_index, 'image' => $image], ['id' => $id]);
            Redirect::autolink(URLROOT . "/admincategorie", Lang::T("SUCCESS"), "category was edited successfully!");
        
        } else {
            // Init Data
            $data = [
                'title' => Lang::T("TORRENT_CATEGORIES"),
                'res' => $res,
                'id' => $id,
            ];

            // Load View
            View::render('categorie/edit', $data, 'admin');
        }
    }

    // Categorie Delete Default Page
    public function delete()
    {
        // Check User Input
        $id = (int) $_GET["id"];
        
        // Form Submit OR View
        if ($_GET["sure"] == '1') {
            if (!Validate::Id($id)) {
                Redirect::autolink(URLROOT . "/admincategorie", Lang::T("CP_NEWS_INVAILD_ITEM_ID = $id"));
            }

            
            // Delete Categorie
            $newcatid = (int) $_POST["newcat"];
            DB::run("UPDATE torrents SET category=$newcatid WHERE category=$id"); //move torrents to a new cat
            DB::delete('categories', ['id'=>$id]);
            Redirect::autolink(URLROOT . "/admincategorie", Lang::T("Category Deleted OK."));
        
        } else {
            // Init Data
            $data = [
                'title' => Lang::T("TORRENT_CATEGORIES"),
                'id' => $id,
            ];

            // Load View
            View::render('categorie/delete', $data, 'admin');
        }
    }

    // Add Categorie Default Page
    public function add()
    {
        // Init Data
        $data = [
            'title' => Lang::T("TORRENT_CATEGORIES"),
        ];

        // Load View
        View::render('categorie/add', $data, 'admin');
    }

    // Add Categorie Form Submit
    public function takeadd()
    {
        // Check User Input
        $name = $_POST['name'];
        $parent_cat = $_POST['parent_cat'];
        $sort_index = $_POST['sort_index'];
        $image = $_POST['image'];

        // Check Correct Input
        if ($parent_cat == "") {
            Redirect::autolink(URLROOT . "/admincategorie/add", "Parent Cat cannot be empty!");
        }
        
        if ($name == "") {
            Redirect::autolink(URLROOT . "/admincategorie/add", "Sub Cat cannot be empty!");
        }

        // Insert Categorie
        $ins = DB::insert('categories', ['name'=>$name, 'parent_cat'=>$parent_cat, 'sort_index'=>$sort_index, 'image'=>$image]);
        if ($ins) {
            Redirect::autolink(URLROOT . "/admincategorie", Lang::T("Category was added successfully."));
        } else {
            Redirect::autolink(URLROOT . "/admincategorie/add", "Unable to add category");
        }
    }

}