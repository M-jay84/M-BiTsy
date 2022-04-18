<?php
print("<p class='text-center'>Edit Section or Item</p>");
while ($arr = $data['res']->fetch(PDO::FETCH_BOTH)) {
    $arr['question'] = stripslashes(htmlspecialchars($arr['question']));
    $arr['answer'] = stripslashes(htmlspecialchars($arr['answer']));
    if ($arr['type'] == "item") {
       print("<form method=\"post\" action=\"" . URLROOT . "/adminfaq/edit?action=edititem\">");
        print("<table border=\"0\" class=\"table_table\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
        print("<tr><td class='table_col1'>ID:</td><td class='table_col1'>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
        print("<tr><td class='table_col2'>Question:</td><td class='table_col2'><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"$arr[question]\" /></td></tr>\n");
        print("<tr><td class='table_col1' style=\"vertical-align: top;\">Answer:</td><td class='table_col1'><textarea rows='3' cols='35' name=\"answer\">$arr[answer]</textarea></td></tr>\n");
        if ($arr['flag'] == "0") {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
        } elseif ($arr['flag'] == "2") {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\" selected=\"selected\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
        } elseif ($arr['flag'] == "3") {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
        } else {
            print("<tr><td class='table_col2'>Status:</td><td class='table_col2'><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #ff0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
        }
        print("<tr><td class='table_col1'>Category:</td><td class='table_col1'><select style=\"width: 300px;\" name=\"categ\">");
        while ($arr2 = $data['res2']->fetch(PDO::FETCH_BOTH)) {
            $selected = ($arr2['id'] == $arr['categ']) ? " selected=\"selected\"" : "";
            print("<option value=\"$arr2[id]\"" . $selected . ">$arr2[question]</option>");
        }
        print("</select></td></tr>\n");
        print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\" /></td></tr>\n");
        print("</table></form>");

    } elseif ($arr['type'] == "categ") { ?>
    
    <div class='ttform'>
    <form action="<?php echo URLROOT; ?>/adminfaq/edit?action=editsect" method="post">
    <input type="hidden" name="id" value="<?php echo $arr['id'] ?>" />

        <div class="form-group row">
            <label  class="col-form-label col-3">ID:</label>
            <div class="col-9">
            <?php echo $arr['id'] ?>
            </div>
        </div><br>  
    
        <div class="form-group row">
            <label for="title" class="col-form-label col-3">Title:</label>
            <div class="col-9">
            <input type="text" name="title" value="<?php echo $arr['question'] ?>">
            </div>
        </div><br><?php
    
        if ($arr['flag'] == "0") { ?>
            <div class="form-group row">
            <label for="public" class="col-form-label col-3">Status:</label>
            <div class="col-9">
            <select name="flag" >
            <option value="0" selected="selected">Hidden</option>
            <option value="1" >Normal</option>
            </select>
            </div>
            </div><br> <?php

        } else { ?>
            <div class="form-group row">
            <label for="flag" class="col-form-label col-3">Status:</label>
            <div class="col-9">
            <select name="flag">
            <option value="0">Hidden</option>
            <option value="1" selected="selected">Normal</option>
            </select>
            </div>
            </div><br> <?php
        } ?>
    
        <div class="text-center">
            <input type="submit" class='btn btn-sm ttbtn' name="edit" value="Edit">
        </div>
    
    </form>
    </div> <?php

    }
}