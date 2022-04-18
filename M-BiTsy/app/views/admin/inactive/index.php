<?php
print("<form action='" . URLROOT . "/admininactive' method='post'>");
print("<br><table class=table_table align=center border=1 cellspacing=0 cellpadding=5><tr>\n");
print("<td class=table_head>" . Lang::T("NUMBER0FDAYS") . "</td><td class=table_head><input type='text' name='cday' size='10' value='" . ($data['cday'] > 30 ? $data['cday'] : 30) . "' maxlength='3' /></td>");
print("<td class='table_head'><input type='submit' value='Change' /><input type='hidden' name='action' value='cday' />");
print("</td></tr></table></form><br/>");

print("<h2 align=center>" . $data['count'] . " accounts inactive for longer than " . $data['cday'] . " days.</h2>");
print("<form action='" . URLROOT . "/admininactive/submit' method='post'>");
print("<table class=table_table align=center width=800 border=1 cellspacing=0 cellpadding=5><tr>\n");
print("<td class=table_head>" . Lang::T("USERNAME") . "</td>");
print("<td class=table_head>" . Lang::T("CLASS") . "</td>");
print("<td class=table_head>" . Lang::T("IP") . "</td>");
print("<td class=table_head>" . Lang::T("RATIO") . "</td>");
print("<td class=table_head>" . Lang::T("JOINDATE") . "</td>");
print("<td class=table_head>" . Lang::T("LAST_VISIT") . "</td>");
print("<td class=table_head align='center'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></td>");

while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
    $downloaded = mksize($arr["downloaded"]);
    $uploaded = mksize($arr["uploaded"]);
    $last_seen = (($arr["last_access"] == null) ? "never" : "" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr["last_access"])) . "&nbsp;ago");
    $class = Groups::get_user_class_name($arr["class"]);
    $joindate = substr($arr['added'], 0, strpos($arr['added'], " "));
    print("<tr>");
    print("<td><a href='" . URLROOT . "/profile?id=" . $arr["id"] . "'>" . Users::coloredname($arr["id"]) . "</a></td>"); //=== Use this line if you did not have the function class_user ===//
    print("<td>" . $class . "</td>");
    print("<td>" . ($arr["ip"] == "" ? "----" : $arr["ip"]) . "</td>");
    print("<td><b>" . get_ratio_color($userratio) . "</b><font class='small'> | Dl:<font color=red><b>" . $downloaded . "</b></font> | Up:<font color=lime><b>" . $uploaded . "</b></font></font></td>");
    print("<td>" . $joindate . "</td>");
    print("<td>" . $last_seen . "</td>");
    print("<td><input type='checkbox' name='userid[]' value='$arr[id]' /></td>");
    print("</tr>");
}
print("<tr><td colspan=7 class='table_head' align='center'>
	                   <select name='action'>
	                   <option value=mail>" . Lang::T("SENDMAIL") . "</option>
	                   <option value='deluser'>" . Lang::T("DELETEUSERS") . "</option>
                       <option value='disable'>" . Lang::T("DISABLED_ACCOUNTS") . "</option>
	                   </select>&nbsp;&nbsp;<input type='submit' name='submit' value='Apply Changes'/>&nbsp;&nbsp;<input type='button' value='Check all' onClick='this.value=check(form)' /></td></tr>");
$record_mail = true; // Set this true or false . If you set this true every time whene you send a mail the time , userid , and the number of mail sent will be recorded

print("</table></form>");