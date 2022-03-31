<?php
Style::begin(Lang::T("Visited Users"));
while ($row = $data['stmt']->fetch(PDO::FETCH_ASSOC)) { 
    $avatar = htmlspecialchars($row["avatar"]);
    if (!$avatar) {
        $avatar = URLROOT . "/assets/images/misc/default_avatar.png";
    } ?>
    <img src='<?php echo $avatar; ?>' width='15px' height='15px'>
    <a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"] ?>">
    <small><b><?php echo Users::coloredname($row["id"], $row) ?></b></small></a>&nbsp;
    <?php
}
Style::end();