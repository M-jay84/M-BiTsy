<div class="ttform">
<form method='post' action='<?php echo URLROOT ?>/adminban/email?add=1'>
<div class="text-center"> <?php
    echo Lang::T("EMAIL_BANS_INFO") ?><br><br>

    <label for="mail_domain"><b><?php echo Lang::T("ADD_EMAIL_BANS") ?></b></label>
	<input id="mail_domain" type="text" class="form-control" name="mail_domain" ><br>
    <label for="comment"><?php echo Lang::T("ADDCOMMENT") ?></label>
	<input id="comment" type="text" class="form-control" name="comment"><br>
    <input type='submit'  class='btn btn-sm ttbtn' value='<?php echo Lang::T("ADD_BAN") ?>' />
</div>
</form>
</div>

<br />
<center><b><?php echo Lang::T("EMAIL_BANS") ?> (<?php echo $data['count'] ?>)</b></center>
<?php
if ($data['count'] == 0) {
    print("<p align='center'><b>" . Lang::T("NOTHING_FOUND") . "</b></p><br />\n");
} else {
    echo $data['pagerbuttons']; ?>
    <div class='table-responsive'> <table class='table table-striped'><thead><tr>
    <th>Added</th>
    <th>Mail Address Or Domain</th>
    <th>Banned By</th>
    <th>Comment</th>
    <th>Remove</th>
    </tr></thead>
    <?php
    while ($arr = $data['res']->fetch(PDO::FETCH_LAZY)) {
        print("<tbody><tr>
        <td>" . TimeDate::utc_to_tz($arr['added']) . "</td>
        <td>$arr[mail_domain]</td>
        <td><a href='" . URLROOT . "/profile?id=$arr[addedby]'>".Users::coloredname($arr['addedby'])."" . "</a></td>
        <td>$arr[comment]</td>
        <td><a href='".URLROOT."/adminban/email?remove=$arr[id]'>Remove</a></td>
        </tr></tbody>");
    }
    print("</table></div>");
    echo $data['pagerbuttons'];
}