<?php
// Function For Comment Table
function commenttable($res, $type = null)
{
    while ($row = $res->fetch(PDO::FETCH_LAZY)) {
        $postername = Users::coloredname($row["id"]);
        if ($postername == "") {
            $postername = Lang::T("DELUSER");
            $title = Lang::T("DELETED_ACCOUNT");
            $avatar = "";
            $usersignature = "";
            $userdownloaded = "";
            $useruploaded = "";
        } else {
            $privacylevel = $row["privacy"];
            $avatar = htmlspecialchars($row["avatar"]);
            $title = format_comment($row["title"]);
            $usersignature = stripslashes(format_comment($row["signature"]));
            $userdownloaded = mksize($row["downloaded"]);
            $useruploaded = mksize($row["uploaded"]);
        }

        $userratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"]) : "---";

        if (!$avatar) {
            $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
        }

        $commenttext = format_comment($row["text"]);

        $edit = null;
        if ($type == "torrent" && Users::get("edit_torrents") == "yes" || $type == "news"  || $type == "req" && Users::get("edit_news") == "yes" || Users::get('id') == $row['user'] && Users::get('id')) {
            $edit = '[<a href="' . URLROOT . '/comment/edit?id=' . $row["id"] . '&amp;type=' . $type . '">Edit</a>]&nbsp;';
        }

        $delete = null;
        if ($type == "torrent" && Users::get("delete_torrents") == "yes" || $type == "req" || $type == "news" && Users::get("delete_news") == "yes") {
            $delete = '[<a href="' . URLROOT . '/comment/delete?id=' . $row["id"] . '&amp;type=' . $type . '">Delete</a>]&nbsp;';
        }

        print('<div class="container"><table class="table table-striped" style="border: 1px solid black" >');
        print('<thead><tr">');
        print('<th align="center" width="200"></th>');
        print('<th align="right">' . $edit . $delete . '[<a href="' . URLROOT . '/report/comment?comment=' . $row["id"] . '&amp;type=' . $type . '">Report</a>] Posted: ' . date("d-m-Y \\a\\t H:i:s", TimeDate::utc_to_tz_time($row["added"])) . '<a id="comment' . $row["id"] . '"></a></th>');
        print('</tr></thead>');
        print('<tr valign="top">');
        if (Users::get('edit_users') == 'no' && $privacylevel == 'strong') {
            print('<td align="left" width="200"><center><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ---<br />Downloaded: ---<br />Ratio: '.get_ratio_color('---').'<br /><br /><a href="' . URLROOT . '/profile?id=' . $row["user"] . '"><i class="fa fa-user" title="Send PM"></i></a></center></td>');
        } else {
            print('<td align="left" width="200"><center><a href="' . URLROOT . '/profile?id=' . $row['id'] . '"><b>' . $postername . '</b></a><br /><i>' . $title . '</i><br /><img width="80" height="80" src="' . $avatar . '" alt="" /><br /><br />Uploaded: ' . $useruploaded . '<br />Downloaded: ' . $userdownloaded . '<br />Ratio: ' . get_ratio_color($userratio) . '<br /><br /><a href="' . URLROOT . '/profile?id=' . $row["user"] . '"><i class="fa fa-user" title="Profile"></i></a> <a href="/message/create?id=' . $row["user"] . '"><i class="fa fa-comment" title="Send PM"></i></a></center></td>');
        }

        print('<td>' . $commenttext . '<hr />' . $usersignature . '</td>');
        print('</tr>');
        print('</table></div>');
    }
}