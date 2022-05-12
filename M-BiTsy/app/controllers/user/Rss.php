<?php

class Rss
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 0);
    }

    // RSS Default Page
    public function index()
    {
        // Check User Input
        $passkey = $_GET["passkey"] ?? Users::get('passkey');
        $incldead = $_GET["incldead"];
        $cat = $_GET["cat"];
        $user = $_GET["user"];

        // Check User
        if (!get_row_count("users", "WHERE passkey=" . sqlesc($passkey))) {
            $passkey = "";
        }
        if ($passkey == "" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_PERMISSION"));
        }

        // Sort Input
        $where = "";
        $wherea = array();
        if (!$incldead) {
            $wherea[] = "visible='yes'";
        }
        if ($cat) {
            $cats = implode(", ", array_unique(array_map("intval", explode(",", (string) $cat))));
            $wherea[] = "category in ($cats)";
        }
        if (Validate::Id($user)) {
            $wherea[] = "owner=$user";
        }
        if ($wherea) {
            $where = "WHERE " . implode(" AND ", $wherea);
        }
        $limit = "LIMIT 50";

        // Get Data
        $res = DB::run("SELECT torrents.id,
                               torrents.name,
                               torrents.size,
                               torrents.category,
                               torrents.added, torrents.leechers,
                               torrents.seeders, categories.parent_cat as cat_parent,
                               categories.name AS cat_name 
                        FROM torrents 
                        LEFT JOIN categories ON category = categories.id 
                        $where 
                        ORDER BY added 
                        DESC $limit")->fetchAll();

        // start the RSS feed output
        header( "Content-type: text/xml; charset=".CHARSET."");
        echo "<?xml version='1.0' encoding='".CHARSET."'?>
        <rss version='2.0'>
        <channel>
        <title>" . htmlspecialchars(Config::get('SITENAME')) . " | RSS</title>
        <link>https://github.com/M-jay84/M-BiTsy</link>
        <description>" . htmlspecialchars(Config::get('SITENAME')) . " RSS</description>
        <language>en-us</language>";

        foreach ($res as $row) {
            $pubdate = date("r", TimeDate::sql_timestamp_to_unix_timestamp($row['added']));
            echo "<item>
                  <title>" . htmlspecialchars($row['name']) . "</title>
                  <guid>" . URLROOT . "/torrent?id=$row[id]&amp;hit=1</guid>
                  <link>" . URLROOT . "/download?id=$row[id]&amp;passkey=$passkey</link>
                  <pubDate>" . $pubdate . "</pubDate>
                  <category> " . $row["cat_parent"] . ": " . $row["cat_name"] . "</category>
                  <description>Size: " . mksize($row['size']) . " Seeders: " . $row['seeders'] . " Leechers: " . $row['leechers'] . "</description>
                  </item>";
        }
        echo ("</channel></rss>");
    }

    // RSS Custom Default Page
    public function custom()
    {
        // Check User
        if (Users::get("view_torrents") != "yes" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        // Get Cat Data
        $stmt = DB::raw('categories', 'id, name, parent_cat', '', 'ORDER BY parent_cat ASC, sort_index ASC');
        
        // Init Data
        $data = [
            'title' => Lang::T("CUSTOM_RSS_XML_FEED"),
            'stmt' => $stmt
        ];

        // Load View
        View::render('rss/custom', $data, 'user');
    }

    // RSS Form Submit
    public function submit()
    {
        // Check User
        if (Users::get("view_torrents") != "yes" && Config::get('MEMBERSONLY')) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }

        if (Input::exist()) {
            // Sort Data
            $params = array();
            if ($cats = $_POST["cats"]) {
                $catlist = array();
                foreach ($cats as $cat) {
                    if (is_numeric($cat)) {
                        $catlist[] = $cat;
                    }
                }
                if ($catlist) {
                    $params[] = "cat=" . implode(",", $catlist);
                }
            }
            if ($_POST["incldead"]) {
                $params[] = "incldead=1";
            }
            if ($_SESSION["loggedin"]) {
                $params[] = "passkey=".Users::get('passkey')."";
            }
            if (Validate::Id($_POST['user'])) {
                $params[] = "user=$_POST[user]";
            }
            if ($params) {
                $param = "?" . implode("&amp;", $params);
            } else {
                $param = "";
            }
            
            Redirect::autolink(URLROOT, "Your RSS link is: <a href='".URLROOT."/rss$param'>".URLROOT."/rss$param</a>");

        } else {
            Redirect::autolink(URLROOT, 'error with custom input');
        }
    }

}