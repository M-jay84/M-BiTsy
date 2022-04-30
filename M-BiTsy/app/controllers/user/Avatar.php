<?php

class Avatar
{
    
    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 2);
    }

    // User Avatar Upload Submit
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");

        // Check User
        if ($id != Users::get('id')) {
            Redirect::autolink(URLROOT . "/index", Lang::T("NO_PERMISSION"));
        }

        // Upload Image
        if (isset($_FILES["upfile"])) {
            $upload = new Uploader($_FILES["upfile"]);
            $upload->must_be_image();
            $upload->max_size(100); // in MB
            $upload->max_image_dimensions(130, 130);
            $upload->encrypt_name();
            $upload->path("uploads/avatars");
            if (!$upload->upload()) {
                Redirect::autolink(URLROOT . "/profile/edit?id=$id", "Upload error: " . $upload->get_error() . " image should be 90px x 90px or lower");
            } else {
                $avatar = URLROOT . "/uploads/avatars/" . $upload->get_name();
                DB::update('users', ['avatar'=>$avatar], ['id'=>$id]);
                Redirect::autolink(URLROOT . "/profile/edit?id=$id", Lang::T("UP_AVATAR")." OK");
            }
        }
        
        // Init Data
        $data = [
            'title' => Lang::T("AVATAR_UPLOAD"),
            'id' => $id,
        ];

        // Load View
        View::render('avatar/index', $data, 'user');
    }

}