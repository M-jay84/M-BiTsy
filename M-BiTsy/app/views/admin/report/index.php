<table align="right">
<tr><td valign="top">
<form id='sort' action=''>
<b>Type:</b>
<select name="type" onchange="window.location='<?php echo $data['page']; ?>type='+this.options[this.selectedIndex].value+'&amp;completed='+document.forms['sort'].completed.options[document.forms['sort'].completed.selectedIndex].value">
    <option value="">All Types</option>
    <option value="user" <?php echo ($_GET['type'] == "user" ? " selected='selected'" : ""); ?>>Users</option>
    <option value="torrent" <?php echo ($_GET['type'] == "torrent" ? " selected='selected'" : ""); ?>>Torrents</option>
    <option value="comment" <?php echo ($_GET['type'] == "comment" ? " selected='selected'" : ""); ?>>Comments</option>
    <option value="forum" <?php echo ($_GET['type'] == "forum" ? " selected='selected'" : ""); ?>>Forum</option>
    </select>
    <b>Completed:</b>
    <select name="completed" onchange="window.location='<?php echo URLROOT; ?>/adminreport?completed='+this.options[this.selectedIndex].value+'&amp;type='+document.forms['sort'].type.options[document.forms['sort'].type.selectedIndex].value">
    <option value="0" <?php echo ($_GET['completed'] == 0 ? " selected='selected'" : ""); ?>>No</option>
    <option value="1" <?php echo ($_GET['completed'] == 1 ? " selected='selected'" : ""); ?>>Yes</option>
</select>
</form>
</td></tr>
</table><br />

<form id="reports" method="post" action="<?php echo URLROOT; ?>/adminreport/completed">
<table class='table table-striped table-bordered table-hover'><thead><tr>
<th>Reported By</th>
<th>Subject</th>
<th>Type</th>
<th>Reason</th>
<th>Dealt With</th>
<th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
</tr><thead>
<?php
if ($data['res']->rowCount() <= 0): ?>
    <tr><td class="table_col1" colspan="6" align="center">No reports found.</td></tr>
    <?php
endif;

while ($row = $data['res']->fetch(PDO::FETCH_LAZY)):
    $dealtwith = '<b>No</b>';
    if ($row["dealtby"] > 0) {
        $r = DB::raw('users', 'username', ['id'=>$row['dealtby']])->fetch();
        $dealtwith = 'By <a href="' . URLROOT . '/profile?id=' . $row['dealtby'] . '">' . $r['username'] . '</a>';
    }

    $r = Reports::getname($row['type'], $row['votedfor']);

    //var_dump($r);
    //$r = $q->fetch(PDO::FETCH_LAZY);
    if ($row["type"] == "user") {
        $link = "".URLROOT."/profile?id=$row[votedfor]";
    } else if ($row["type"] == "torrent") {
    $link = "".URLROOT."/torrent?id=$row[votedfor]";
    } else if ($row["type"] == "comment") {
        $link = "".URLROOT."/comment?type=" . ($r['news'] > 0 ? "news" : "torrent") . "&amp;id=" . ($r['news'] > 0 ? $r['news'] : $r['torrent']) . "#comment$row[votedfor]";
    } else if ($row["type"] == "forum") {
        $link = "".URLROOT."/topic?topicid=$row[votedfor]&amp;page=last#post$row[votedfor_xtra]";
    }
    ?>
    <tr>
    <td><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row['addedby']; ?>"><?php echo Users::coloredname($row['addedby']); ?></a></td>
    <td><a href="<?php echo $link; ?>"><?php echo CutName($r['name'], 40); ?></a></td>
    <td><?php echo $row['type']; ?></td>
    <td><?php echo htmlspecialchars($row['reason']); ?></td>
    <td><?php echo $dealtwith; ?></td>
    <td><input type="checkbox" name="reports[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
<?php
endwhile; ?>
</tbody></table>

<div class="text-center">
<?php
if ($_GET["completed"] != 1): ?>
    <input type="submit" class='btn btn-sm ttbtn' name="mark" value="Mark Completed" />
    <?php
endif;?>
<input type="submit" class='btn btn-sm ttbtn' name="del" value="Delete" />
</div>
</form>
<?php
print $pagerbuttons;