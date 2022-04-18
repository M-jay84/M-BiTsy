<?php

class Attachment
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // Download Attachment Page
    public function index()
    {
        // Check User Input
        $id = (int) Input::get("id");
        $filename = Input::get("hash");

        // Check The File
        $fn = UPLOADDIR . "/attachment/$filename.data";
        
        // Check Attachment
        $sql = DB::select('attachments', '*', ['id'=>$id]);
        $extension = substr($sql['filename'], -3);
        
        // Download
        if (!file_exists($fn)) {
            Redirect::autolink($_SERVER['HTTP_REFERER'], "The file $filename does not exists");
        } else {
            header('Content-Disposition: attachment; filename="' . $sql['filename'] . '"');
            header('Content-Length: ' . filesize($fn));
            header("Content-Type: application/$extension");
            readfile($fn);
        }
    }

    // View Image Attachment Page
    public function images()
    {
        // Check User Input
        $file_hash = Input::get("hash");

        // Open Image
        $switchimage = UPLOADDIR . "/attachment/$file_hash.data";
        if (file_exists($switchimage)) {
            //list($width, $height) = getimagesize($plik); 
            ?> 
            <img alt="test image" src="<?php echo data_uri($switchimage, $file_hash); ?>">
            <?php
        }
    }

}