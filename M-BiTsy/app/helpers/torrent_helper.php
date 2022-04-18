<?php
// Function That Returns The Health Level Of A Torrent
function health($leechers, $seeders)
{
    if (($leechers == 0 && $seeders == 0) || ($leechers > 0 && $seeders == 0)) {
        return 0;
    } elseif ($seeders > $leechers) {
        return 10;
    }
    $ratio = $seeders / $leechers * 100;
    if ($ratio > 0 && $ratio < 15) {
        return 1;
    } elseif ($ratio >= 15 && $ratio < 25) {
        return 2;
    } elseif ($ratio >= 25 && $ratio < 35) {
        return 3;
    } elseif ($ratio >= 35 && $ratio < 45) {
        return 4;
    } elseif ($ratio >= 45 && $ratio < 55) {
        return 5;
    } elseif ($ratio >= 55 && $ratio < 65) {
        return 6;
    } elseif ($ratio >= 65 && $ratio < 75) {
        return 7;
    } elseif ($ratio >= 75 && $ratio < 85) {
        return 8;
    } elseif ($ratio >= 85 && $ratio < 95) {
        return 9;
    } else {
        return 10;
    }
}

// Create Table Of Peers
function peerstable($res)
{
    $ret = "<table align='center' cellpadding=\"3\" cellspacing=\"0\" class=\"table_table\" width=\"100%\" border=\"1\"><tr><th class='table_head'>" . Lang::T("NAME") . "</th><th class='table_head'>" . Lang::T("SIZE") . "</th><th class='table_head'>" . Lang::T("UPLOADED") . "</th>\n<th class='table_head'>" . Lang::T("DOWNLOADED") . "</th><th class='table_head'>" . Lang::T("RATIO") . "</th></tr>\n";

    while ($arr = $res->fetch(PDO::FETCH_LAZY)) {
        $res2 = DB::run("SELECT name,size FROM torrents WHERE id=? ORDER BY name", [$arr['torrent']]);
        $arr2 = $res2->fetch(PDO::FETCH_LAZY);
        $userratio = $arr2["downloaded"] > 0 ? number_format($arr2["uploaded"] / $arr2["downloaded"], 1) : "---";
        $ret .= "<tr><td class='table_col1'><a href=" . URLROOT . "torrent?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr2["name"]) . "</b></a></td><td align='center' class='table_col2'>" . mksize($arr2["size"]) . "</td><td align='center' class='table_col1'>" . mksize($arr["uploaded"]) . "</td><td align='center' class='table_col2'>" . mksize($arr["downloaded"]) . "</td><td align='center' class='table_col1'>".get_ratio_color($userratio)."</td></tr>\n";
    }
    $ret .= "</table>\n";
    return $ret;
}

// Function To Display Tables Of Torrents
function torrenttable($res)
{
    // Set Waiting Time
    if (Config::get('_WAIT') && Config::get('MEMBERSONLY') && in_array(Users::get("class"), explode(",", Config::get('CLASS_WAIT')))) {
        $gigs = Users::get("uploaded") / (1024 * 1024 * 1024);
        $userratio = Users::get("downloaded") > 0 ? number_format(Users::get("uploaded") / Users::get("downloaded"), 1) : "---";
        if ($userratio < 0 || $gigs < 0) {
            $wait = Config::get('A_WAIT');
        } elseif ($userratio < Config::get('RATIOA') || $gigs < Config::get('GIGSA')) {
            $wait = Config::get('A_WAIT');
        } elseif ($userratio < Config::get('RATIOB') || $gigs < Config::get('GIGSB')) {
            $wait = Config::get('B_WAIT');
        } elseif ($userratio < Config::get('RATIOC') || $gigs < Config::get('GIGSC')) {
            $wait = Config::get('C_WAIT');
        } elseif ($userratio < Config::get('RATIOD') || $gigs < Config::get('GIGSD')) {
            $wait = Config::get('D_WAIT');
        } else {
            $wait = 0;
        }
    }
    $wait = '';
    // Columns
    $cols = explode(",", TORRENTTABLE_COLUMNS);
    $cols = array_map("strtolower", $cols);
    $cols = array_map("trim", $cols);
    $colspan = count($cols);
    // Start The Table
    echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';

    foreach ($cols as $col) {
        switch ($col) {
            case 'category':
                echo "<th>" . Lang::T("TYPE") . "</th>";
                break;
            case 'name':
                echo "<th>" . Lang::T("NAME") . "</th>";
                break;
            case 'dl':
                echo "<th>" . Lang::T("DL") . "</th>";
                break;
            case 'magnet':
                echo "<th>" . Lang::T("MAGNET2") . "</th>";
                break;
            case 'uploader':
                echo "<th>" . Lang::T("UPLOADER") . "</th>";
                break;
            case 'tube':
                echo "<th>" . Lang::T("YOUTUBE") . "</th>";
                break;
            case 'tmdb':
                echo "<th>TMDB</th>";
                break;
            case 'comments':
                echo "<th>" . Lang::T("COMM") . "</th>";
                break;
            case 'nfo':
                echo "<th>" . Lang::T("NFO") . "</th>";
                break;
            case 'size':
                echo "<th>" . Lang::T("SIZE") . "</th>";
                break;
            case 'completed':
                echo "<th>" . Lang::T("C") . "</th>";
                break;
            case 'seeders':
                echo "<th>" . Lang::T("S") . "</th>";
                break;
            case 'leechers':
                echo "<th>" . Lang::T("L") . "</th>";
                break;
            case 'health':
                echo "<th>" . Lang::T("HEALTH") . "</th>";
                break;
            case 'external':
                if (Config::get('ALLOWEXTERNAL')) {
                    echo "<th>" . Lang::T("L/E") . "</th>";
                }
                break;
            case 'added':
                echo "<th>" . Lang::T("ADDED") . "</th>";
                break;
            case 'speed':
                echo "<th>" . Lang::T("SPEED") . "</th>";
                break;
            case 'wait':
                if ($wait) {
                    echo "<th>" . Lang::T("WAIT") . "</th>";
                }
                break;
            case 'rating':
                echo "<th>" . Lang::T("RATINGS") . "</th>";
                break;
            case 'admin':
                if (Users::get('class') === 7) {
                    echo "<th>" . Lang::T("Admin") . "</th>";
                }
                break;
        }
    }
    // Do They Have To Wiait
    if ($wait && !in_array("wait", $cols)) {
        echo "<th>" . Lang::T("WAIT") . "</th>";
    }

    echo "</tr></thead>";

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $id = $row["id"];
        print("<tr>\n");
        $x = 1;

        foreach ($cols as $col) {
            switch ($col) {
                case 'category':
                    print("<td class='ttable_col$x' align='center' valign='middle'>");
                    if (!empty($row["cat_name"])) {
                        print("<a href=\"" . URLROOT . "/search/browse?cat=" . $row["category"] . "\">");
                        if (!empty($row["cat_pic"]) && $row["cat_pic"] != "") {
                            print("<img border=\"0\"src=\"" . URLROOT . "/assets/images/categories/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" />");
                        } else {
                            print($row["cat_parent"] . ": " . $row["cat_name"]);
                        }
                        print("</a>");
                    } else {
                        print("-");
                    }
                    print("</td>\n");
                    break;
                case 'name':
                    $char1 = 50; //cut name length
                    $smallname = htmlspecialchars(CutName($row["name"], $char1));
                    $dispname = "<b>" . $smallname . "</b>";
                    $added = date("M d, Y", TimeDate::utc_to_tz_time($row['added']));
                    if (strtotime($added) >= strtotime("yesterday")){ // Only show for torrents 1 day old
                        $dispname .= "<b><font color='#ff0000'> (" . Lang::T("NEW") . "!) </font></b>";
                    }
                    if ($row["freeleech"] == 1) {
                        $dispname .= " <img src='" . URLROOT . "/assets/images/misc/free.gif' border='0' alt='' />";
                    }
                    if ($row["vip"] == "yes") {
                        $dispname .= " <img src='" . URLROOT . "/assets/images/misc/vip.gif' border='0' alt='' />";
                    }
                    if ($row["sticky"] == "yes") {
                        $dispname .= " <img src='" . URLROOT . "/assets/images/misc/sticky.gif' bored='0' alt='sticky' title='sticky'>";
                    }
                    //print("<td class='ttable_col$x' nowrap='nowrap'><a href=\"" . URLROOT . "/torrent?id=$id&amp;hit=1\">$dispname</a></td>");
                    // BALLOON TOOLTIP MOD
                    print("<td class='ttable_col$x' nowrap='nowrap'><a href=\"" . URLROOT . "/torrent?id=$id&hit=1\" onMouseover=\"return overlib('<table class=ballooncolor border=1 width=300px align=center><tr><td class=balloonheadercolor colspan=2 align=center>$smallname</td></tr><tr valign=top><td class=ballooncolor align=center><img border=0 height=200 width=120 src=".getimage($row)."></td><td width=80%  class=ballooncolor><div align=left><b>Uploaded on: </b>" . date("m-d-Y", TimeDate::utc_to_tz_time($row["added"])) . "<br /><b>Size: </b>". mksize($row["size"]) . "<br /><b>Completed: </b>" . $row["times_completed"] . "<br /></div><div align=left><b>Views: </b>" . $row["views"] . "<br /><b>Hits: </b>" . $row["hits"] . "<br /><b>Seeders: </b><font color=green>" . $row["seeders"] . "</font><br /><b>Leechers: </b><font color=red>" . $row["leechers"] . "</font><br /><b> Uploaded by: </b>" . $row['username'] . "</div></td></tr><tr><td class=balloonheadercolor colspan=2 align=center>".Lang::T("DESCRIPTION")."</td></tr><tr><td  class=ballooncolor colspan=2>" . $row['descr'] . "</td></tr></table>', CENTER, HEIGHT, 200, WIDTH, 300)\"; onMouseout=\"return nd()\">".$dispname."</a></td>");
                    break;
                case 'dl':
                    print("<td class='ttable_col$x' align='center'><a href=\"" . URLROOT . "/download?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><i class='fa fa-download' style='color:green' title='Download'></i></a></td>");
                    break;
                case 'magnet':
                    $magnet = DB::raw('torrents', 'info_hash', ['id' =>$id])->fetch();
                    // Like Mod
                    if (!Config::get('FORCETHANKS')) {
                        print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . Users::get('passkey') . "\"><i class='fa fa-magnet' aria-hidden='true' style='color:green' title='Download via Magnet'></i></a></td>");
                    } elseif (Config::get('FORCETHANKS')) {
                        $like = DB::select('thanks', 'user', ['thanked' =>$id, 'type' =>'torrent', 'user' =>Users::get('id')]);
                        if ($like) {
                            if (Users::get("can_download") != "no") {
                                print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . Users::get('passkey') . "\"><i class='fa fa-magnet' aria-hidden='true' style='color:green' title='Download via Magnet'></i></a></td>");
                            } else {
                                print("<td class='ttable_col$x' align='center'></td>");
                            }
                        } elseif (Users::get("id") == $row["owner"]) {
                            print("<td class='ttable_col$x' align='center'><a href=\"magnet:?xt=urn:btih:" . $magnet["info_hash"] . "&dn=" . rawurlencode($row['name']) . "&tr=" . $row['announce'] . "?passkey=" . Users::get('passkey') . "\"><i class='fa fa-magnet' aria-hidden='true' style='color:green' title='Download via Magnet'></i></a></td>");
                        } else {
                            print("<td class='ttable_col$x' align='center'><a href='" . URLROOT . "/like/thanks?id=$id&type=torrent><button  class='btn btn-sm ttbtn'>Thanks</button></td>");
                        }
                    }
                    break;
                case 'uploader':
                    echo "<td class='ttable_col$x' align='center'>";
                    if (($row["anon"] == "yes" || $row["privacy"] == "strong") && Users::get("id") != $row["owner"] && Users::get("edit_torrents") != "yes") {
                        echo "Anonymous";
                    } elseif ($row["username"]) {
                        echo "<a href='" . URLROOT . "/profile?id=$row[owner]'>" . Users::coloredname($row['owner']) . "</a>";
                    } else {
                        echo "Unknown";
                    }

                    echo "</td>";
                    break;
                case 'tube':
                    if ($row["tube"]) {
                        print("<td class='ttable_col$x' align='center'><a rel=\"prettyPhoto\"  href=" . $row['tube'] . " ><" . htmlspecialchars($row['tube']) . "><img src='" . URLROOT . "/assets/images/misc/youtube.png'  border='0' width='20' height='20' alt=\"\" /></a></td>");
                    } else {
                        print("<td class='ttable_colx' align='center'>-</td>");
                    }
                    break;
                case 'tmdb':
                    if ($row["tmdb"]) {
                        print("<td class='ttable_col$x' align='center'><a href=" . $row['tmdb'] . " target='_blank'><" . htmlspecialchars($row['tmdb']) . "><img src='" . URLROOT . "/assets/images/misc/tmdb.png'  border='0' width='20' height='20' alt=\"\" /></a></td>");
                    } else {
                        print("<td class='ttable_colx' align='center'>-</td>");
                    }
                    break;
                case 'comments':
                    print("<td class='ttable_col$x' align='center'><font size='1' face='verdana'><a href=" . URLROOT . "/comment?type=torrent&amp;id=$id'>" . number_format($row["comments"]) . "</a></font></td>\n");
                    break;
                case 'nfo':
                    if ($row["nfo"] == "yes") {
                        print("<td class='ttable_col$x' align='center'><a href=" . URLROOT . "nfo?id=$row[id]'><i class='fa fa-file-text-o tticon' title='View NFO'></i></a></td>");
                    } else {
                        print("<td class='ttable_col$x' align='center'>-</td>");
                    }
                    break;
                case 'size':
                    print("<td class='ttable_col$x' align='center'>" . mksize($row["size"]) . "</td>\n");
                    break;
                case 'completed':
                    print("<td class='ttable_col$x' align='center'><font color='orange'><b>" . number_format($row["times_completed"]) . "</b></font></td>");
                    break;
                case 'seeders':
                    print("<td class='ttable_col$x' align='center'><font color='green'><b>" . number_format($row["seeders"]) . "</b></font></td>\n");
                    break;
                case 'leechers':
                    print("<td class='ttable_col$x' align='center'><font color='#ff0000'><b>" . number_format($row["leechers"]) . "</b></font></td>\n");
                    break;
                case 'health':
                    print("<td class='ttable_col$x' align='center'><img src='" . URLROOT . "/assets/images/health/health_" . health($row["leechers"], $row["seeders"]) . ".gif' alt='' /></td>\n");
                    break;
                case 'external':
                    if (Config::get('ALLOWEXTERNAL')) {
                        if ($row["external"] == 'yes') {
                            print("<td class='ttable_col$x' align='center'>" . Lang::T("E") . "</td>\n");
                        } else {
                            print("<td class='ttable_col$x' align='center'>" . Lang::T("L") . "</td>\n");
                        }
                    }
                    break;
                case 'added':
                    print("<td class='ttable_col$x' align='center'>" . TimeDate::get_time_elapsed($row['added']) . "</td>");
                    break;
                case 'speed':
                    if ($row["external"] != "yes" && $row["leechers"] >= 1) {
                        $speedQ = DB::run("SELECT (SUM(downloaded)) / (UNIX_TIMESTAMP('" . TimeDate::get_date_time() . "') - UNIX_TIMESTAMP(started)) AS totalspeed FROM peers WHERE seeder = 'no' AND torrent = '$id' ORDER BY started ASC");
                        $a = $speedQ->fetch(PDO::FETCH_LAZY);
                        $totalspeed = mksize($a["totalspeed"]) . "/s";
                    } else {
                        $totalspeed = "--";
                    }
                    print("<td class='ttable_col$x' align='center'>$totalspeed</td>");
                    break;
                case 'wait':
                    if ($wait) {
                        $elapsed = floor((TimeDate::gmtime() - strtotime($row["added"])) / 3600);
                        if ($elapsed < $wait && $row["external"] != "yes") {
                            $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                            print("<td class='ttable_col$x' align='center'><a href=\"/faq#section46\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
                        } else {
                            print("<td class='ttable_col$x' align='center'>--</td>\n");
                        }

                    }
                    break;
                case 'rating':
                    if (!$row["rating"]) {
                        $rating = "--";
                    } else {
                        $rating = "<a title='$row[rating]/5'>" . ratingpic($row["rating"]) . "</a>";
                    }
                    //$rating = $row["rating"] . "/5)";
                    print("<td class='ttable_col$x' align='center'>$rating</td>");
                    break;
                case 'admin':
                    if (Users::get('class') === 7) {
                        print("<td class='ttable_col$x' align='center'><a href='".URLROOT."/torrent/edit?id=".$row['id']."'><i class='fa fa-pencil' title='Edit' aria-hidden='true'></i> </a> <a href='".URLROOT."/torrent/delete?id=".$row['id']."'><i class='fa fa-trash' title='Delete' aria-hidden='true'></i></a> </td>");
                    }
                    break;
            }
            if ($x == 2) {
                $x--;
            } else {
                $x++;
            }
        }

        // Wait Time Check
        if ($wait && !in_array("wait", $cols)) {
            $elapsed = floor((TimeDate::gmtime() - strtotime($row["added"])) / 3600);
            if ($elapsed < $wait && $row["external"] != "yes") {
                $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                print("<td class='ttable_col$x' align='center'><a href=\"/faq\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></td>\n");
            } else {
                print("<td class='ttable_col$x' align='center'>--</td>\n");
            }
            $colspan++;
            if ($x == 2) {
                $x--;
            } else {
                $x++;
            }
        }

        print("</tr>\n");
    }

    print("</table></div><br />\n");
}

// Function that assigns a color based on the value of the ratio
function get_ratio_color($ratio)
{
   if ($ratio == '---') {
       return "<font color='FFFFFF'>$ratio</font>";
   }
   if ($ratio < 0.5) {
       return "<font color='#fc0313'>$ratio</font>";
   }
   if ($ratio < 1) {
       return "<font color='#fc6203'>$ratio</font>";
   }
   if ($ratio < 1.5) {
       return "<font color='#ebc334'>$ratio</font>";
   }
   if ($ratio < 2) {
       return "<font color='#1a7d09'>$ratio</font>";
   }
   return "<font color='#0d6efd'>$ratio</font>";
}

// Function That Returns The Image Corresponding To The Votes
function ratingpic($num)
{
    $r = round($num * 2) / 2;
    if ($r != $num) {
        $n = $num - $r;
        if ($n < .25) {
            $n = 0;
        } elseif ($n >= .25 && $n < .75) {
            $n = .5;
        }

        $r += $n;
    }
    if ($r < 1 || $r > 5) {
        return;
    }

    return "<img src=\"" . URLROOT . "/assets/images/rating/$r.png\" border=\"0\" alt=\"rating: $num/5\" title=\"rating: $num/5\" />";
}

// Sort Torrent Table By Column
function sortMod()
{
    $sort = $_GET['sort'] ?? '';
    $order = $_GET['order'] ?? '';
    switch ($sort) {
        case 'id':$column = "id";
            break;
        case 'name':$column = "name";
            break;
        case 'comments':$column = "comments";
            break;
        case 'size':$column = "size";
            break;
        case 'times_completed':$column = "times_completed";
            break;
        case 'seeders':$column = "seeders";
            break;
        case 'leechers':$column = "leechers";
            break;
        case 'category':$column = "category";
            break;
        default:$column = "id";
            break;
    }

    switch ($order) {
        case 'asc':$ascdesc = "ASC";
            break;
        case 'desc':$ascdesc = "DESC";
            break;
        default:$ascdesc = "DESC";
            break;
    }

    $orderby = 'ORDER BY torrents.' . $column . ' ' . $ascdesc;
    $pagerlink = "sort=" . $column . "&amp;order=" . strtolower($ascdesc) . "&amp;";

    return [
        'orderby' => $orderby, 'pagerlink' => $pagerlink,
        'column' => $column, 'by' => $ascdesc,
    ];
}

// Torrent Search
function search()
{
    $keyword = $_GET['keyword'] ?? '';
    $cats = (int) $_GET['cat'] ?? 0;
    $incldead = (int) $_GET['incldead'] ?? 0;
    $freeleech = (int) $_GET['freeleech'] ?? 0;
    $inclexternal = (int) $_GET['inclexternal'] ?? 0;
    $lang = (int) $_GET['lang'] ?? 0;
    
    $url = "?"; // assign url
    $wherea = []; // assign conditions
    $params = []; // assign vars
    // browse
    $cats = (int) Input::get('cat') ?? 0;
    $parent_cat = Input::get('parent_cat') ?? 0;

    if (!$keyword == '') {
        $keys = explode(" ", $keyword);
        foreach ($keys as $k) {
            $ssa[] = " torrents.name LIKE '%$k%' ";
        }
        $wherea[] = '(' . implode(' OR ', $ssa) . ')';
        $url .= "keyword=" . urlencode($keyword) . "&";
    }

    if (!$cats == 0) {
        $wherea[] = "category = $cats";
        $url .= "cat=" . urlencode($cats) . "&";
    }

    if (!$parent_cat == 0) {
        $wherea[] = "categories.parent_cat = $cats";
        $url .= "parent_cat=" . urlencode($cats) . "&";
    }

    if ($incldead == 1) {
        $url .= "incldead=1&";
    } elseif ($incldead == 2) {
        $params[] = 'no';
        $wherea[] = "visible = ?";
        $url .= "incldead=2&";
    } else {
        $params[] = 'yes';
        $wherea[] = "visible = ?";
    }

    if ($freeleech == 1) {
        $params[] = 0;
        $wherea[] = "freeleech = ?";
        $url .= "freeleech=1&";
    } elseif ($freeleech == 2) {
        $params[] = 1;
        $wherea[] = "freeleech = ?";
        $url .= "freeleech=2&";
    }

    if ($inclexternal == 1) {
        $params[] = 'no';
        $wherea[] = "external = ?";
        $url .= "inclexternal=1&";
    } elseif ($inclexternal == 2) {
        $params[] = 'yes';
        $wherea[] = "external = ?";
        $url .= "inclexternal=2&";
    }

    if ($lang) {
        $params[] = $lang;
        $wherea[] = "torrentlang = ?";
        $url .= "lang=" . urlencode($lang) . "&";
    }

    $where = implode(' AND ', $wherea);

    // browse
    $wherecatina = array();
    $wherecatin = "";
    $res = DB::raw('categories', 'id', '');
    while ($row = $res->fetch(PDO::FETCH_LAZY)) {
        if (Input::get("c$row[id]")) {
            $wherecatina[] = $row["id"];
            $url .= "c$row[id]=1&";
        }
        $wherecatin = implode(", ", $wherecatina);
    }

    if ($wherecatin) {
        $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
    }

    if ($where != '') {
        $where = 'WHERE ' . $where;
    }

    $sortmod = sortMod();
    $orderby = 'ORDER BY torrents.' . $sortmod['column'] . ' ' . $sortmod['by'];
    $pagerlink = $sortmod['pagerlink'];

    $array = [
        'where'=>$where,
        'keyword'=>$keyword,
        'orderby'=>$orderby,
        'params'=>$params,
        'url'=>$url,
        'pagerlink'=>$pagerlink,
        'parent_cat' => $parent_cat,
        'wherecatina' => $wherecatina,
    ];
    return $array;
}

// Torrent Search
function torrentsearch($searchwhat)
{
    $search = $_GET['search'] ?? '';
    $letter = $_GET["letter"] ?? '';
    $url = "?";

    if ($search != '') {
        $keys = explode(" ", $search);
        foreach ($keys as $k) {
            $ssa[] = " $searchwhat LIKE '%$k%' ";
        }
        $query = '(' . implode(' OR ', $ssa) . ')';
        $url .= "search=" . urlencode($search);
    } else {
        if (strlen($letter) > 1) {
            die;
        }
        if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
            $letter = "t";
        }
        $query = "$searchwhat LIKE '$letter%'";
        $url = "?letter=$letter";
    }

    $array = [
        'url' => $url,
        'query' => $query
    ];

    return $array;
}