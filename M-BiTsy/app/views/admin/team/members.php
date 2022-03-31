<div class='table-responsive'><table class='table table-striped'><thead><tr>
<th>Username</th>
<th><?php echo Lang::T("UPLOADED"); ?>: </th>
<th>Downloaded</th>
</tr></thead></tbody>
<?php
while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
    $username = htmlspecialchars($row['username']);
    $uploaded = mksize($row['uploaded']);
    $downloaded = mksize($row['downloaded']);
    ?>
    <tr><td><a href='<?php echo URLROOT; ?>/profile?id=<?php echo $row['id']; ?>'><?php echo Users::coloredname($row['id']); ?></a></td>
    <td><?php echo $uploaded; ?></td>
    <td><?php echo $downloaded; ?></td></tr>
    <?php
} ?>
</tbody>
</table></div>