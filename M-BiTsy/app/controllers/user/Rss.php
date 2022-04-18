<?php

class Rss
{

    public function __construct()
    {
        // Verify User/Guest
        Auth::user(0, 1);
    }

    // RSS Default Page
    public function index()
    {
        $cat = $_GET["cat"];
        $dllink = (int) $_GET["dllink"];
        $passkey = $_GET["passkey"];
        $incldead = $_GET["incldead"];

        if (!get_row_count("users", "WHERE passkey=" . sqlesc($passkey))) {
            $passkey = "";
        }
        $where = "";
        $wherea = array();
        if (!$incldead) {
            $wherea[] = "visible='yes'";
        }
        if ($cat) {
            $cats = implode(", ", array_unique(array_map("intval", explode(",", (string) $cat))));
            $wherea[] = "category in ($cats)";
        }
        if (Validate::Id($_GET["user"])) {
            $wherea[] = "owner=$_GET[user]";
        }
        if ($wherea) {
            $where = "WHERE " . implode(" AND ", $wherea);
        }
        $limit = "LIMIT 50";

        // start the RSS feed output
        header( "Content-type: text/xml; charset=".CHARSET."");
        echo "<?xml version='1.0' encoding='".CHARSET."'?>
        <rss version='2.0'>
        <channel>
        <title>" . htmlspecialchars(Config::get('SITENAME')) . " | RSS</title>
        <link>https://github.com/M-jay84/M-BiTsy</link>
        <description>" . htmlspecialchars(Config::get('SITENAME')) . " RSS</description>
        <language>en-us</language>";


        $res = DB::run("SELECT torrents.id, torrents.name, torrents.size, torrents.category, torrents.added, torrents.leechers, torrents.seeders, categories.parent_cat as cat_parent, categories.name AS cat_name FROM torrents LEFT JOIN categories ON category = categories.id $where ORDER BY added DESC $limit");
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            list($id, $name, $size, $category, $added, $leechers, $seeders, $catname) = $row;
            $guid = URLROOT . "/torrent?id=$id&amp;hit=1";
            if ($dllink) {
                if ($passkey) {
                    $link = "".URLROOT."/download?id=$id&amp;passkey=$passkey";
                    $link = "".URLROOT."/download?id=$id&amp;passkey=$passkey";
                } else {
                    $link = "".URLROOT."/download?id=$id";
                }
            } else {
                $link = URLROOT . "/torrent?id=$id&amp;hit=1";
            }
            $pubdate = date("r", TimeDate::sql_timestamp_to_unix_timestamp($added));
            echo "<item>
                  <title>" . htmlspecialchars($name) . "</title>
                  <guid>" . $link . "</guid>
                  <link>" . $link . "</link>
                  <pubDate>" . $pubdate . "</pubDate>
                  <category> " . $row["cat_parent"] . ": " . $row["cat_name"] . "</category>
                  <description>Size: " . mksize($size) . " Seeders: " . $seeders . " Leechers: " . $leechers . "</description>
                  </item>";
        }
        echo ("</channel></rss>");
    }

    // RSS Custom Default Page
    public function custom()
    {
        // Get Cat Data
        $resqn = DB::raw('categories', 'id, name, parent_cat', '', 'ORDER BY parent_cat ASC, sort_index ASC');
        
        // Init Data
        $data = [
            'title' => Lang::T("CUSTOM_RSS_XML_FEED"),
            'resqn' => $resqn
        ];

        // Load View
        View::render('rss/custom', $data, 'user');
    }

    // RSS Form Submit
    public function submit()
    {
        if ($_POST) {
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
            if ($_POST["dllink"]) {
                $params[] = "dllink=1";
            }
            if (!$_POST["cookies"] && $_SESSION['loggedin'] == true) {
                $params[] = "passkey=".Users::get('passkey')."";
            }
            if ($params) {
                $param = "?" . implode("&amp;", $params);
            } else {
                $param = "";
            }
            $mss = "Your RSS link is: <a href=\"".URLROOT."/rss$param\">".URLROOT."/rss$param</a><br/><br/>";
            Redirect::autolink(URLROOT, $mss);
        }
    }

}