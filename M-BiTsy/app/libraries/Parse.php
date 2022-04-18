<?php

class Parse
{

    // Get Data From Torrent File
    public function torr($filename = "")
    {
        // First Check File Is Torrent
        $torrent = explode(".", $filename);
        $fileend = strtolower(end($torrent));
        if ($fileend != "torrent") {
            Redirect::autolink(URLROOT . '/upload', 'this is not a torrent');
        }

        // Set Arrays
        $torrentInfo = array();
        $array = array();

        // Read The File
        $parse = file_get_contents("$filename");
        if (!isset($parse)) {
            Redirect::autolink(URLROOT . '/upload', "Error Getting Torrent file");
        }

        // Decode File Array
        $array = Bencode::decode($parse);

        // If Array Lets Read It
        if ($array === false) {
            Redirect::autolink(URLROOT . '/upload', "Unable to decode file.");
        }

        // Check Info Is There
        if (array_key_exists("info", $array) === false) {
            Redirect::autolink(URLROOT . '/upload', "Error opening torrent.<br>");
        }

        // Read info
        $info = $array["info"];
        if (isset($info["file tree"]) && isset($info["files"])) {
            Redirect::autolink(URLROOT . '/upload', 'We do not allow hybrid, we except v1 while others catch up but use v2 when possible');
        }

        // Get Announce URL
        $torrentInfo['announce'] = $array["announce"];

        // Get Announce List Array
        if (isset($array["announce-list"])) {
            $torrentInfo["announce-list"] = $array["announce-list"];
        }

        // Calculates date from UNIX Epoch
        $torrentInfo["creation date"] = date('r', $array["creation date"]);

        // Get torrents comment
        if (isset($array['comment'])) {
            $torrentInfo['comment'] = $array['comment'];
        }

        // The name of the torrents is different to the file name
        $torrentInfo['name'] = $info['name'];

        // Calculate Size/Count
        $filecount = 0;
        $torrentsize = 0;

        // Get The Hash
        if (isset($info["file tree"])) {
            // Calculates SHA256 Hash
            $hash = hash('sha256', Bencode::encode($info));
            $torrentInfo['hash'] = substr($hash, 0, 40);
            // Return Marker To identify as v2
            $torrentInfo['type'] = '2';
        } else {
            // Calculates SHA1 Hash
            $torrentInfo['hash'] = hash('sha1', Bencode::encode($info));
            // Return Marker To identify as v1
            $torrentInfo['type'] = '1';
        }

        // V2 Get file list
        if (isset($info["file tree"]) && is_array($info["file tree"])) {
            // For Now Dont Allow V2
            unlink("$filename");
            Redirect::autolink(URLROOT . '/upload', "Ask Admin To Turn V2 Torrents On.");

            $Iterator = new RecursiveIteratorIterator(
                new RecursiveArrayIterator($info["file tree"]),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($Iterator as $key => $val) {
                if ($key != 'pieces root' && $key != 'length' && !empty($key)) {
                    $subfolder = substr($key, -4, 1);
                    if($subfolder === '.') {
                        $keys[0] = $key;
                        $filecount = ++$filecount;
                    } else {
                        $keys[1] = $key;
                    }
                }
                if ($val > 1 && is_int($val)) {
                    $vals = $val;
                    $torrentsize += $val;
                }
                if ($val > 1 && is_int($val)) {
                    $paths[] = [
                        'length' => $vals,
                        'path' => $keys,
                    ];
                }
            }
    
            $torrentInfo['files'] = $paths;
            $torrentInfo['length'] = $torrentsize;
            $torrentInfo['ttfilecount'] = $filecount;

        } elseif (isset($info["files"]) && is_array($info["files"])) {

            // V1 Get file list
            foreach ($info["files"] as $file) {
                $filecount = ++$filecount;
                $torrentsize += $file['length'];

                $path[0] = $file['path'][1] ?? $file['path'][0];
                if ($file['path'][1] ===  $path[0]) {
                    $path[1] = $file['path'][0];
                }
                
                $length = $file['length'];
                $paths[] = [
                    'length' => $length,
                    'path' => $path
                ];
            }

            $torrentInfo['files'] = $paths;
            $torrentInfo['length'] = $torrentsize;
            $torrentInfo['ttfilecount'] = $filecount;
        } else {
            // Single File Torrent
            $torrentInfo['name'] = $info['name'];
            $torrentInfo['length'] = $info['length'];
            $torrentInfo['ttfilecount'] = 1;
        }

        return $torrentInfo;
        //var_dump($torrentInfo);
    }
}