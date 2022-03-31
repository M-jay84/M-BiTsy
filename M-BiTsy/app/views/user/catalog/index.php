<?php
// Form For Search
print("<br /><center><form method=get action=?>\n");
print("<input align='center' type=text size=30 name=search placeholder='" . Lang::T("SEARCH_CAT") . "' id=searchinput>\n");
print("<input type=submit class='btn btn-sm ttbtn' value=" . Lang::T("SEARCH") . ">\n");
print("</form></center>\n");


Lang::letters();

if ($data['count']) { ?>
<div class="row"><?php
    echo $data['pagerbuttons'];
    foreach ($data['res'] as $arr) { ?>
        <div class="col-sm-4">
        <?php 
        
        $shortname = CutName(htmlspecialchars($arr["name"]), 40);
        if ($arr["image1"] == ''){
            $image1 = "<div><img src=".URLROOT."/assets/images/misc/default_avatar.png width=100% height=245></div>";
        } elseif ($arr["image1"] != ""){
            $image1 = "<img src=".data_uri(UPLOADDIR."/images/".$arr["image1"], $arr['image1'])." width=100% height=245>";
        }
        $donated = $arr["donated"];
        $warned = $arr["warned"];
        $ca = "<a href='".URLROOT."/profile?id=$arr[owner]'>" . Users::coloredname($arr['owner']) . "</a>";
        if (($arr["anon"] == "yes" || $cat["privacy"] == "strong") && Users::get("id") != $arr["owner"] && Users::get("edit_torrents") != "yes") {
            $owner2 = "<b>" . Lang::T("ADDED_BY") . ": </b><i>" . Lang::T("ANONYMOUS") . "</i>";
        } else {
            $owner2 = "<b>" . Lang::T("ADDED_BY") . ": </b>" . $ca . "";
        }
        if (empty($arr["username"])) {
            $owner1 = "<i>" . Lang::T("UNKNOWN_USER") . "</i>";
        }
        $flagname = $arr["lang_name"];
        if (empty($arr["lang_name"])) {
            $arr["lang_name"] = "Unknown/NA";
        }
        $lang = "<b>" . Lang::T("LANG") . " : </b>" . $arr["lang_name"] . "";
        if (empty($arr["lang_image"])) {
            $arr["lang_image"] = "unknown.gif";
        }
        $flag = "<img src=\"assets/images/languages/" . $arr["lang_image"] . "\" alt=\"" . $flagname . "\" />";

        ?>
<div class="row">
  
    <div class="col-12 ttborder">
      <a href="<?php echo URLROOT ?>/torrent?id=<?php echo $arr['id'] ?>&hit=1"><b><?php echo $shortname ?></b></a>
    </div>
  
    <div class="col-6 ttborder">
        <?php echo $image1; ?>
    </div>
  
    <div class="col-6 ttborder">

    <b><?php echo $owner2 ?></b><br>
    <b><?php echo Lang::T("CATEGORY") ?> : </b><?php echo $arr["cat_parent"] ?> > <?php echo $arr["cat_name"] ?><br>
    <?php echo $lang . "&nbsp;&nbsp;" .$flag . "<br>";
    echo "<b>" . Lang::T("TOTAL_SIZE") . " : </b>" . mksize($arr["size"]) . "<br>";
    
    ?>
    <?php echo Lang::T("LEECHERS") ?><br>
    <font color=red><b><?php echo $arr["leechers"] ?></b></font><br>
    <?php echo Lang::T("SEEDERS") ?><br>
    <font color=green><b><?php echo  $arr["seeders"] ?></b></font><br>
    <b><?php echo  Lang::T("NUMBER_OF_DOWNLOADS") ?><br>
    <font color=green><?php echo $arr['times_completed'] ?></b></font><br>
    <a href="<?php echo URLROOT ?>/download?id=<?php echo $arr['id'] ?>">Download</a>

    </div>
  


    <div class="col-12 ttborder">
    <?php
$shortdescription = mb_strimwidth($arr['descr'], 0, 10, "...");
?>
        <center>
        <?php echo Lang::T("DESCRIPTION") ?><br>
        <i><b><u><b><font size=2><?php echo format_comment($shortdescription) ?></font></b></u></b></i></center>
    </div>

</div>



        </div> <?php

//print("$s"); remember top peer/snatched

    }
?></div><?php

} else {
    $message = $_GET['letter'] || $_GET['search'] ? Lang::T("NO_MATCHES") : 'Search Our Torrents';
    echo "<center><b>$message</b></center>";
}