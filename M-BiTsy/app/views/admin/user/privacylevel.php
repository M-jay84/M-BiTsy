<center>
This page displays all users which are enabled, confirmed grouped by their privacy level.
</center>

<div class="text-center">
    <form id='sort' action=''>
    <b>Privacy Level:</b>
    <select name="type" onchange="window.location='<?php echo URLROOT; ?>/adminuser/privacy?type='+this.options[this.selectedIndex].value">
    <option value="">Any</option>
    <option value="low" <?php echo ($_GET['type'] == "low" ? " selected='selected'" : ""); ?>>Low</option>
    <option value="normal" <?php echo ($_GET['type'] == "normal" ? " selected='selected'" : ""); ?>>Normal</option>
    <option value="strong" <?php echo ($_GET['type'] == "strong" ? " selected='selected'" : ""); ?>>Strong</option>
    </select>
    </form>
</div>

<?php if ($data['count'] > 0): ?>
<br />
<div class='table-responsive'><table class='table table-striped'>
<thead><tr>
    <th>Username</th>
    <th><?php echo Lang::T("CLASS"); ?></th>
    <th>E-mail</th>
    <th>IP</th>
    <th>Added</th>
    <th>Last Visited</th>
</tr></thead>
<?php while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
<tbody><tr>
    <td><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["id"]); ?></a></td>
    <td><?php echo Groups::get_user_class_name($row["class"]); ?></td>
    <td><?php echo $row["email"]; ?></td>
    <td><?php echo $row["ip"]; ?></td>
    <td><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
    <td><?php echo TimeDate::utc_to_tz($row["last_access"]); ?></td>
</tr>
<?php endwhile;?>
<tbody></table></div>
<?php else: ?>
<center><b>Nothing Found...</b></center>
<?php
endif;

if ($data['count'] > 25) {
echo $data['pagerbuttons'];
}