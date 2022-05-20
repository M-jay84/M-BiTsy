<form method="get" action="<?php echo URLROOT; ?>/search/browse">
<p class="text-center">
<?php
$i = 0;
while ($cat = $data['cats']->fetch(PDO::FETCH_ASSOC)) {
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "<br><br>" : "");
    print("<a href=" . URLROOT . "/search/browse?cat=$cat[id]>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $data['wherecatina']) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' />&nbsp;\n");
    $i++;
}
echo "<br><br><input type='submit' value='".Lang::T("GO")."' />";
echo "</p></form>";
//if we are browsing, display all subcats that are in same cat
if ($data['parent_cat']) {
    $data['url'] .= "parent_cat=" . urlencode($data['parent_cat']) . "&amp;";
    echo "<br /><br /><b>" . Lang::T("YOU_ARE_IN") . ":</b> <a href='" . URLROOT . "/search/browse?parent_cat=" . urlencode($data['parent_cat']) . "'>" . htmlspecialchars($data['parent_cat']) . "</a><br /><b>" . Lang::T("SUB_CATS") . ":</b> ";
    $subcatsquery = DB::raw('categories', 'id, name, parent_cat', ['parent_cat' =>$data['parent_cat']], 'ORDER BY name');
    while ($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)) {
        $name = $subcatsrow['name'];
        echo " - <a href=" . URLROOT . "/search/browse?cat=$subcatsrow[id]>$name</a>";
    }
}

if (Validate::Id(Input::get("page"))) {
    $data['url'] .= "page=$_GET[page]&amp;";
}

echo "</center><br /><br />"; //some spacing
// New code (TorrentialStorm)
echo "<div align='right'><form id='sort' action=''>" . Lang::T("SORT_BY") . ": <select name='sort' onchange='window.location=\"{$data['url']}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">" . Lang::T("ADDED") . "</option>";
echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">" . Lang::T("NAME") . "</option>";
echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">" . Lang::T("COMMENTS") . "</option>";
echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">" . Lang::T("SIZE") . "</option>";
echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">" . Lang::T("COMPLETED") . "</option>";
echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">" . Lang::T("SEEDERS") . "</option>";
echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">" . Lang::T("LEECHERS") . "</option>";
echo "</select>&nbsp;";
echo "<select name='order' onchange='window.location=\"{$data['url']}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">" . Lang::T("ASCEND") . "</option>";
echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">" . Lang::T("DESCEND") . "</option>";
echo "</select>";
echo "</form></div>";
// End

if ($data['count']) {
    torrenttable($data['res']);
    print($data['pagerbuttons']);
} else {
    print(Lang::T("NOTHING_FOUND") . "&nbsp;&nbsp;");
    print Lang::T("NO_UPLOADS");
}
if ($_SESSION) {
    DB::update('users', ['last_access' =>TimeDate::get_date_time()], ['id' => Users::get("id")]);
}