<center><?php echo Lang::T("DUPLICATEIPINFO"); ?></center>
<br />
<?php
if ($data['num'] > 0): ?>
    <br />
    <table class='table table-striped table-bordered table-hover'><thead>
    <tr>
        <th><?php echo Lang::T("USERNAME"); ?></th>
        <th><?php echo Lang::T("USERCLASS"); ?></th>
        <th><?php echo Lang::T("EMAIL"); ?></th>
        <th><?php echo Lang::T("IP"); ?></th>
        <th><?php echo Lang::T("ADDED"); ?></th>
        <th><?php echo Lang::T("COUNT"); ?></th>
    </tr></thead>
    <?php
    while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
        <td><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["id"]); ?></a></td>
        <td><?php echo Groups::get_user_class_name($row["class"]); ?></td>
        <td><?php echo $row["email"]; ?></td>
        <td><?php echo $row["ip"]; ?></td>
        <td><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td><a href="<?php echo URLROOT; ?>/adminsearch?ip=<?php echo $row['ip']; ?>"><?php echo number_format($row['count']); ?></a></td>
        </tr>
        <?php
    endwhile;?>
    </table>
    <?php
else: ?>
    <center><b><?php echo Lang::T("NOTHING_FOUND"); ?></b></center>
    <?php
endif;