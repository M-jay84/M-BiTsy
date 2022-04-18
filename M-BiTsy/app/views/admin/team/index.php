<b>Add new team:</b><br>
<div class='ttform'>
<form name='create' method='post' action='<?php echo URLROOT ?>/adminteam/add'>
    <input type='hidden' name='add' value='true' />

    <div class="form-group row">
        <label for="team_name" class="col-form-label col-3"><?php echo Lang::T("TEAM"); ?>:</label>
        <div class="col-9">
            <input id="team_name" type="text" class="form-control" name="team_name">
        </div>
    </div><br>

    <div class="form-group row">
        <label for="team_owner" class="col-form-label col-3"><?php echo Lang::T("TEAM_OWNER_NAME"); ?>:</label>
        <div class="col-9">
            <input id="team_owner" type="text" class="form-control" name="team_owner">
        </div>
    </div><br>

    <div class="form-group row">
        <label for="team_description" class="col-form-label col-3"><?php echo Lang::T("DESCRIPTION"); ?>:</label>
        <div class="col-9">
            <input id="team_description" type="text" class="form-control" name="team_description">
        </div>
    </div><br>

    <div class="form-group row">
        <label for="team_image" class="col-form-label col-3"><?php echo Lang::T("TEAM_LOGO_URL"); ?>:</label>
        <div class="col-9">
            <input id="team_image" type="text" class="form-control" name="team_image">
        </div>
    </div><br>

    <div class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='<?php echo Lang::T("TEAM_CREATE"); ?>' />
    </div>

</form>
</div>

<b>Current <?php echo  Lang::T("TEAMS") ?>:</b>
<br />
<center>
<div class='table-responsive'><table class='table table-striped'>
<thead><tr>
<th>ID</th><th><?php echo  Lang::T("TEAM_LOGO") ?></th>
<th><?php echo  Lang::T("TEAM_NAME") ?></th>
<th><?php echo  Lang::T("TEAM_OWNER_NAME") ?></th>
<th><?php echo  Lang::T("DESCRIPTION") ?></th>
<th><?php echo  Lang::T("OTHER") ?></th>
</tr></thead>
<?php
while ($row = $data['sql']->fetch(PDO::FETCH_LAZY)) {
    $id = (int) $row['id'];
    $name = htmlspecialchars($row['name']);
    $image = htmlspecialchars($row['image']);
    $owner = (int) $row['owner'];
    $info = format_comment($row['info']);
    ?>
    <tbody><tr>
    <td><b><?php echo $id ?></b> </td> 
    <td><img src='<?php echo $image ?>' alt='' /></td> 
    <td><b><?php echo $name ?></b></td>
    <td><a href='<?php echo URLROOT ?>/profile?id=<?php echo $owner ?>'><?php echo Users::coloredname($owner) ?></a></td>
    <td><?php echo $info ?></td>
    <td><a href='<?php echo URLROOT ?>/adminteam/members?teamid=<?php echo $id ?>'>[Members]</a>&nbsp;
        <a href='<?php echo URLROOT ?>/adminteam/edit?editid=<?php echo $id ?>&amp;name=<?php echo $name ?>&amp;image=<?php echo $image ?>&amp;info=<?php echo $info ?>&amp;owner=<?php echo $OWNERNAME ?>'>[<?php echo Lang::T("EDIT") ?>]</a>&nbsp;
        <a href='<?php echo URLROOT ?>/adminteam/delete?del=<?php echo $id ?>&amp;team=<?php echo $name ?>'>[Delete]</a></td></tr></tbody>
    <?php
}
?>
</table></center>