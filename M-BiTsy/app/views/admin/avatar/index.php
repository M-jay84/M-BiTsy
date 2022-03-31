<?php echo ($data['pagerbuttons']); ?>
<div class="container justify-content-md-center-2">
<div class="row">
<?php
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    echo ("<div class='col-1'><b><a href=\"" . URLROOT . "/profile?id=" . $arr['id'] . "\">" . Users::coloredname($arr['id']) . "</a></b><br>");
    if (!$arr['avatar']) {
        echo "<img width=\"80\" src=" . URLROOT . "/images/misc/default_avatar.png' alt='' /></div>";
    } else {
        echo "<img width=\"80\" src=\"" . htmlspecialchars($arr["avatar"]) . "\" alt='' /></div>";
    }
} ?>
</div>
</div>
<?php echo ($data['pagerbuttons']);