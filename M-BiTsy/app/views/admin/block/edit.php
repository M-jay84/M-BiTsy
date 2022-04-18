<div class="row justify-content-center">
<div class="col-md-8">
<form name="blocks" method="post" action="<?php echo URLROOT ?>/adminblock/edit">
    <input type="hidden" name="edit" value="true">
    <center><font size="2"><a href="<?php echo URLROOT ?>/adminblock"><b><?php echo Lang::T("_BLC_MAN_") ?></b></a></font></center><br />
    <table class='table table-striped'><thead><tr>
    <th rowspan="2" class="table_head"><?php echo Lang::T("_NAMED_") ?><br />(<?php echo Lang::T("_FL_NM_IF_NO_SET_") ?>)</th>
    <th rowspan="2" class="table_head"><?php echo Lang::T("_FILE_NAME_") ?></th>
    <th rowspan="2" class="table_head"><?php echo Lang::T("DESCRIPTION") ?><br />(<?php echo Lang::T("_MAX_") ?> 255 <?php echo Lang::T("_CHARS_") ?>)</th>
    <th rowspan="2" colspan="3" class="table_head"><?php echo Lang::T("_POSITION_") ?></th>
    <th rowspan="2" colspan="2" class="table_head"><?php echo Lang::T("_SORT_ORDER_") ?></th>
    <th colspan="2" class="table_head"><?php echo Lang::T("ENABLED") ?></th>
    <th rowspan="2" class="table_head"><?php echo Lang::T("_DEL_") ?></th>
    </tr><tr>
    <th class="table_head"><?php echo Lang::T("YES") ?></th>
    <th class="table_head"><?php echo Lang::T("NO") ?></th>
    </tr></thead> <?php
    while ($blocks2 = $data['res']->fetch(PDO::FETCH_ASSOC)) {
        $down = $blocks["id"] ?? '';
        if (!$setclass) {
            $class = "table_col2";
            $setclass = true;
        } else {
            $class = "table_col1";
            $setclass = false;
        }
        switch ($blocks2["position"]) {
            case "left":
                $pos = Lang::T("_LEFT_");
                break;
            case "middle":
                $pos = Lang::T("_MIDDLE_");
                break;
            case "right":
                $pos = Lang::T("_RIGHT_");
                break;
        }

        print("<tr>" . # id=\"qq"\" - removed
            "<td rowspan=\"2\" class=\"$class\"><input type=\"text\" name=\"named_" . $blocks2["id"] . "\" value=\"" . ($blocks2["named"] ? $blocks2["named"] : $blocks2["name"]) . "\" /></td>" .
            "<td rowspan=\"2\" class=\"$class\">" . $blocks2["name"] . "</td>" .
            "<td rowspan=\"2\" class=\"$class\"><textarea name=\"description_" . $blocks2["id"] . "\" rows=\"2\" cols=\"20\">" . $blocks2["description"] . "</textarea></td>" .
            "<td colspan=\"3\" class=\"$class\" align=\"center\">" . $pos . "</td>" .
            "<td colspan=\"2\" class=\"$class\" align=\"center\">" . $blocks2["sort"] . "</td>" .
            "<td rowspan=\"2\" class=\"$class\" align=\"center\"><input type=\"radio\" name=\"enable_" . $blocks2["id"] . "\"" . ($blocks2["enabled"] ? " checked=\"checked\"" : "") . " value=\"1\" /></td>" .
            "<td rowspan=\"2\" class=\"$class\" align=\"center\"><input type=\"radio\" name=\"enable_" . $blocks2["id"] . "\"" . (!$blocks2["enabled"] ? " checked=\"checked\"" : "") . " value=\"0\" /></td>" .
            "<td rowspan=\"2\" class=\"$class\" align=\"center\"><input type=\"checkbox\" name=\"delete[]\" value=\"" . $blocks2["id"] . "\"/></td>" .
            "</tr>" .
            "<tr>" .
            "<td class=\"$class\" height=\"1%\">" . ((($blocks2["position"] != "left") && ($blocks2["enabled"] == 1)) ? "<a href=\"" . URLROOT . "/adminblock/edit?edit=true&amp;position=left&amp;left=" . $blocks2["id"] . "\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/leftenable.gif\" width=\"18\" height=\"15\"  /></a>" : "<img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/leftdisable.gif\" width=\"18\" height=\"15\" " . ($blocks2["enabled"] ? "alt=\"" . Lang::T("_AT_LEFT_") . "\"" : "") . " " . ($blocks2["enabled"] ? "onclick=\"javascript: alert('" . Lang::T("_AT_LEFT_") . "');\"" : "onclick=\"javascript: alert('" . Lang::T("_MUST_ENB_FIRST") . "');\"") . "  />") . "</td>" .
            "<td class=\"$class\" height=\"1%\">" . ((($blocks2["position"] != "middle") && ($blocks2["enabled"] == 1)) ? "<a href=\"" . URLROOT . "/adminblock/edit?edit=true&amp;position=middle&amp;middle=" . $blocks2["id"] . "\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/middleenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/middledisable.gif\" width=\"18\" height=\"15\" " . ($blocks2["enabled"] ? "alt=\"" . Lang::T("_AT_CENTER_") . "\"" : "") . " " . ($blocks2["enabled"] ? "onclick=\"javascript: alert('" . Lang::T("_AT_CENTER_") . "');\"" : "onclick=\"javascript: alert('" . Lang::T("_MUST_ENB_FIRST") . "');\"") . "  />") . "</td>" .
            "<td class=\"$class\" height=\"1%\">" . ((($blocks2["position"] != "right") && ($blocks2["enabled"] == 1)) ? "<a href=\"" . URLROOT . "/adminblock/edit?edit=true&amp;position=right&amp;right=" . $blocks2["id"] . "\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/rightenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/rightdisable.gif\" width=\"18\" height=\"15\" " . ($blocks2["enabled"] ? "alt=\"" . Lang::T("_AT_RIGHT_") . "\"" : "") . " " . ($blocks2["enabled"] ? "onclick=\"javascript: alert('" . Lang::T("_AT_RIGHT_") . "');\"" : "onclick=\"javascript: alert('" . Lang::T("_MUST_ENB_FIRST") . "');\"") . "  />") . "</td>" .
            "<td class=\"$class\" height=\"1%\">" . ((($blocks2["sort"] != 1) && ($blocks2["enabled"] != 0)) ? "<a href=\"" . URLROOT . "/adminblock/edit?edit=true&amp;position=" . $blocks2["position"] . "&amp;sort=" . $blocks2["sort"] . "&amp;up=" . $blocks2["id"] . "\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/updisable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border='0' src=\"" . URLROOT . "/assets/images/blocks/updisable.gif\" width=\"18\" height=\"15\" alt=\"" . ($blocks2["enabled"] ? "" . Lang::T("_AT_TOP_") . "" : "") . "\" " . ($blocks2["enabled"] ? "onclick=\"javascript: alert('" . Lang::T("_AT_TOP_") . "');\"" : "onclick=\"javascript: alert('" . Lang::T("_MUST_ENB_FIRST") . "');\"") . " />") . "</td>" .
            "<td class=\"$class\" height=\"1%\">" . (((($blocks2["sort"] != ($nextleft - 1)) && ($blocks2["position"] == "left") || ($blocks2["sort"] != ($nextright - 1)) && ($blocks2["position"] == "right") || ($blocks2["sort"] != ($nextmiddle - 1)) && ($blocks2["position"] == "middle")) && ($blocks2["enabled"] != 0)) ? "<a href=\"" . URLROOT . "/adminblock/edit?edit=true&amp;position=" . $blocks2["position"] . "&amp;sort=" . $blocks2["sort"] . "&amp;down=" . $blocks2["id"] . "\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/downenable.gif\" width=\"18\" height=\"15\" /></a>" : "<img border=\"0\" src=\"" . URLROOT . "/assets/images/blocks/downdisable.gif\" width=\"18\" height=\"15\" alt=\"" . ($blocks2["enabled"] ? "" . Lang::T("_AT_BOTTOM_") . "" : "") . "\" " . ($blocks2["enabled"] ? "onclick=\"javascript: alert('" . Lang::T("_AT_BOTTOM_") . "');\"" : "onclick=\"javascript: alert('" . Lang::T("_MUST_ENB_FIRST") . "');\"") . " />") . "</td>" .
            "</tr>");
    }
    print("<tr>" .
    "<td colspan=\"11\" align=\"center\" class=\"table_head\"><input type=\"submit\" value=\"" . Lang::T("_BTN_UPDT_") . "\" /></td>" .
    "</tr>" .
    "</table>" .
    "</form></div></div>");