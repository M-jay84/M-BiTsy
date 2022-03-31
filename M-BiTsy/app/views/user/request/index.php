<a href='<?php echo URLROOT ?>/request/makereq'><button  class='btn btn-sm ttbtn'>Add New Request</button></a>&nbsp;
<a href='<?php echo URLROOT ?>/request?requestorid=<?php echo Users::get('id') ?>'><button  class='btn btn-sm ttbtn'>View my requests</button></a>&nbsp;
<a href='<?php echo URLROOT ?>/request'><button  class='btn btn-sm ttbtn'>All requests</button></a>
<br><br>
 
<form method=post action='<?php echo URLROOT ?>/request/delete'>
<div class='table-responsive'> <table class='table table-striped'><thead><tr>
<th><?php echo Lang::T('REQUESTS') ?></th>
<th><?php echo Lang::T('TYPE') ?></th>
<th><?php echo Lang::T('DATE_ADDED') ?></th>
<th><?php echo Lang::T('ADDED_BY') ?></th>
<th><?php echo Lang::T('FILLED') ?></th>
<th><?php echo Lang::T('FILLED_BY') ?></th>
<th><?php echo Lang::T('VOTES') ?></th>
<th>Comm</th>
<th><?php echo Lang::T('DEL') ?></th>
</tr></thead><?php

for ($i = 0; $i < $data['num']; ++$i) {
    $arr = $data['res']->fetch(PDO::FETCH_ASSOC);
    $privacylevel = $arr["privacy"];
    $userratio = $arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 1) : "---";
    $res2 = DB::raw('users', 'username', ['id' =>$arr['filledby']]);
    $arr2 = $res2->fetch(PDO::FETCH_ASSOC);
    if ($arr2['username']) {
        $filledby = Users::coloredname($arr['filledby']);
    } else {
        $filledby = " ";
    }
    
    if ($privacylevel == "strong") {
        if (Users::get("class") >= 5) {
            $addedby = "<td class=table_col2 align=center><a href=".URLROOT."/profile?id=$arr[userid]><b>".Users::coloredname($arr['userid'])." (".get_ratio_color($userratio).")</b></a></td>";
        } else {
            $addedby = "<td class=table_col2 align=center><a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username] (----)</b></a></td>";
        }
    } else {
        $addedby = "<td class=table_col2 align=center><a href=".URLROOT."/profile?id=$arr[userid]><b>$arr[username] (".get_ratio_color($userratio).")</b></a></td>";
    }
    $filled = $arr['filled'];
    if ($filled) {
        $filled = "<a href=$filled><font color=green><b>Yes</b></font></a>";
        $filledbydata = "<a href=".URLROOT."/profile?id=$arr[filledby]><b>$arr2[username]</b></a>";
    } else {
        $filled = "<a href=".URLROOT."/request/reqdetails?id=$arr[id]><font color=red><b>No</b></font></a>";
        $filledbydata = "<i>nobody</i>";
    }
    print("<tr><td class=table_col1 align=left><a href=".URLROOT."/request/reqdetails?id=$arr[id]><b>$arr[request]</b></a></td>" .
    "<td class=table_col2 align=center>$arr[parent_cat]: $arr[cat]</td><td align=center
    class=table_col1>$arr[added]</td>$addedby<td
    class=table_col2>$filled</td>
    <td class=table_col1>$filledbydata</td>
    <td class=table_col2><a href=".URLROOT."/request/votesview?requestid=$arr[id]><b>$arr[hits]</b></a></td>
    <td class=table_col1 align=center><a href=".URLROOT."/request/reqdetails?id=$arr[id]><b>" . $arr["comments"] . "");
    if (Users::get('id') == $arr['userid'] || Users::get("class") > 5) {
        print("<td class=table_col1><input type=\"checkbox\" name=\"delreq[]\" value=\"" . $arr['id'] . "\" />&nbsp;<a href='".URLROOT."/request/edit?id=$arr[id]'><i class='fa fa-pencil' title=" . Lang::T("EDIT") . "></i></a></td>");
    } else {
        print("<td class=table_col1>&nbsp;</td>");
    }
    print("</tr>\n");
}
print("</table></div>");
print("<p align=right><input type=submit class='btn btn-sm ttbtn' value=" . Lang::T('DO_DELETE') . "></p>");
print("</form>");
echo $data['pagerbuttons'];