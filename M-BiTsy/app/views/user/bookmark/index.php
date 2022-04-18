<?php
usermenu(Users::get('id'));
echo "<div style='margin-top:5px; margin-bottom:15px' align='center'>You have bookmarked <b>" . $data['count'] . "</b> torrent" . ($data['count'] != 1 ? "s" : "") . "</b></div>";
echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
echo "<th>" . Lang::T("TYPE") . "</th>";
echo "<th>" . Lang::T("NAME") . "</th>";
echo "<th>" . Lang::T("Size") . "</th>";
echo "<th>" . Lang::T("Added") . "</th>";
echo "<th>" . Lang::T("Download") . "</th>";
echo "<th>" . Lang::T("Comments") . "</th>";
echo "<th>Seeders</th>";
echo "<th>" . Lang::T("Leechers") . "</th>";
echo "<th>" . Lang::T("Completed") . "</th>";
echo "<th>L/E</th>";
echo "<th>" . Lang::T("Delete") . "</th></tr><tbody>";

foreach ($data['res'] as $row) {
    print("<tr>");
    $smallname = htmlspecialchars(CutName($row["name"], 40));
    $dispname = "<b>" . $smallname . "</b>";
    // cat img & name
    print("<td class='table_col1' width='1%' align='center' valign='middle'>");
    if (!empty($row["cat_name"])) {
        print("<a href=\"torrents.php?cat=" . $row["category"] . "\">");
        if (!empty($row["cat_pic"]) && $row["cat_pic"] != "") {
            print("<img border=\"0\" src=\"" . URLROOT . "/assets/images/categories/" . $row["cat_pic"] . "\" title=\"" . $row["cat_parent"] . ": " . $row["cat_name"] . "\" />");
        } else {
            print($row["cat_parent"] . ": " . $row["cat_name"]);
        }
        print("</a>");
    } else {
        print("---");
    }
    print("</td>\n");

    echo "
    <td nowrap='nowrap'><a title=\"" . $row["name"] . "\" href=\"" . URLROOT . "/torrent?id=$row[id]&amp;hit=1\">$dispname</a></td>
    <td>" . mksize($row["size"]) . "</td>
    <td>" . date("j.M.Y<\\B\\R>H:i", TimeDate::utc_to_tz_time($row["added"])) . "</td>
    <td><a href=" . URLROOT . "/download?id=" . $row["id"] . "&amp;name=" . rawurlencode($row["filename"]) . "\"><i class='fa fa-download' ></i></a></td>
    <td><a href='" . URLROOT . "/comment?type=torrent&id=$row[id]'>" . number_format($row["comments"]) . "</a></td>
    <td><font color='limegreen'>" . number_format($row["seeders"]) . "</font></td>
    <td><font color='red'>" . number_format($row["leechers"]) . "</font></td>
    <td><font color='darkorange'>" . number_format($row["times_completed"]) . "</font></td>\n";
    if ($row["external"] == 'yes') {
        print("<td>Ext</td>");
    } else {
        print("<td>Loc></td>");
    }
    echo "<td><a href=\"" . URLROOT . "/bookmark/delete?target=" . $row['id'] . "\"><i class='fa fa-remove' ></i></a></td>\n";
}

print("</tr></tbody>");
print("</table>$data[pager]</div><br />");