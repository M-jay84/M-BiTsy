<p class="text-center"><a href='<?php echo URLROOT ?>/admincategorie/add'><b>Add New Category</b></a></p><br />
<p class="text-center"><i>Please note that if no image is specified, the category name will be displayed</i></p><br />
<div class="table-responsive">
<table class='table table-striped table-bordered table-hover'><thead><tr>
<th width='10'>Sort</th>
<th>Parent Cat</th>
<th>Sub Cat</th>
<th class='table_head'>Image</th>
<th width='30'></th>
</tr></thead>
<?php
foreach ($data['sql'] as $row) {
    print("<tbody><tr><td>$row[sort_index]</td><td>$row[parent_cat]</td><td>$row[name]</td><td>");
    if (isset($row["image"]) && $row["image"] != "") {
        print("<img border=\"0\" src=\"" . URLROOT . "/assets/images/categories/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
    } else {
        print("-");
    }
    print("</td><td><a href=" . URLROOT . "/admincategorie/edit?id=$row[id]>[EDIT]</a> <a href='" . URLROOT . "/admincategorie/delete?id=$row[id]'>[DELETE]</a></td></tr></tbody>");
}
echo ("</table></div></center>");