<center>
<a href="<?php echo URLROOT ?>/search/browse"><?php echo Lang::T("BROWSE_TORRENTS") ?></a><br><br>

<form method="get" action="<?php echo URLROOT; ?>/search/test<?php echo $data['url']; ?>">
<?php
    echo Lang::T("SEARCH"); ?>&nbsp;<input type="text" name="keyword" size="40" value="<?php echo stripslashes(htmlspecialchars($data['keyword'])) ?>" />
    <?php print(Lang::T("IN")); ?>
    <select name="cat">
    <option value="0"><?php echo "(" . Lang::T("ALL") . " " . Lang::T("TYPES") . ")"; ?></option>
    <?php
    $cats = Catagories::genrelist();
    $catdropdown = "";
    foreach ($cats as $cat) {
        $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
        if ($cat["id"] == $_GET["cat"]) {
            $catdropdown .= " selected=\"selected\"";
        }
        $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
    }
    echo $catdropdown ?>
    </select><br><br>

    <select name="incldead">
    <option value="0"><?php echo Lang::T("ACTIVE_TRANSFERS"); ?></option>
    <option value="1"><?php if ($_GET["incldead"] == 1) { echo "selected='selected'"; }
    echo Lang::T("INC_DEAD"); ?></option>
    <option value="2"><?php if ($_GET["incldead"] == 2) { echo "selected='selected'"; }
    echo Lang::T("ONLY_DEAD"); ?></option>
    </select>

    <select name="freeleech">
    <option value="0"><?php echo Lang::T("ALL"); ?></option>
    <option value="1"><?php if ($_GET["freeleech"] == 1) { echo "selected='selected'"; }
    echo Lang::T("NOT_FREELEECH"); ?></option>
    <option value="2"><?php if ($_GET["freeleech"] == 2) { echo "selected='selected'"; }
    echo Lang::T("ONLY_FREELEECH"); ?></option>
    </select>

<?php
if (Config::get('ALLOWEXTERNAL')) { ?>
    <select name="inclexternal">
    <option value="0"><?php echo Lang::T("LOCAL_EXTERNAL"); ?></option>
    <option value="1"><?php if ($_GET["inclexternal"] == 1) { echo "selected='selected'"; }
    echo Lang::T("LOCAL_ONLY"); ?></option>
    <option value="2"><?php if ($_GET["inclexternal"] == 2) { echo "selected='selected'"; }
    echo Lang::T("EXTERNAL_ONLY"); ?></option>
    </select> <?php
} ?>

    <select name="lang">
    <option value="0"><?php echo "(" . Lang::T("ALL") . ")"; ?></option>
    <?php
    $lang = Torrentlang::langlist();
    $langdropdown = "";
    foreach ($lang as $lang) {
        $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
        if ($lang["id"] == $_GET["lang"]) {
            $langdropdown .= " selected=\"selected\"";
        }
        $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
    }
    echo $langdropdown ?>
    </select><br><br>

    <button type='submit' class='btn btn-sm ttbtn'><?php print Lang::T("SEARCH");?></button>
    </center><br>
    </form>

<?php 
echo "<form id='sort' action=''><div align='right'>" . Lang::T("SORT_BY") . ": <select name='sort' onchange='window.location=\"$data[url]sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">" . Lang::T("ADDED") . "</option>";
echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">" . Lang::T("NAME") . "</option>";
echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">" . Lang::T("COMMENTS") . "</option>";
echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">" . Lang::T("SIZE") . "</option>";
echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">" . Lang::T("COMPLETED") . "</option>";
echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">" . Lang::T("SEEDERS") . "</option>";
echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">" . Lang::T("LEECHERS") . "</option>";
echo "</select>&nbsp;";
echo "<select name='order' onchange='window.location=\"$data[url]order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">" . Lang::T("ASCEND") . "</option>";
echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">" . Lang::T("DESCEND") . "</option>";
echo "</select>";
echo "</div>";
echo "</form>";
?>

<?php
torrenttable($data['res']);
echo $data['pagerbuttons'];