<center>
    This page displays all users which are enabled and confirmed. You can search for users and results will be returned
    matched against there username, e-mail and ip. You can also choose to delete them. If no results are shown please try
    redefining your search.
    <br />
    <form method="get" action="<?php echo URLROOT; ?>/adminsearch/simplesearch">
    <input type="hidden" name="action" value="users" />
    Search: <input type="text" name="search" size="30" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
    <input type="submit" class="btn ttbtn  btn-sm"value="Search" />
    </form>
    </center>
    <?php if ($data['count'] > 0): ?>
    <form id="usersearch" method="post" action="<?php echo URLROOT; ?>/adminsearch/simplesearch">
    <input type="hidden" name="do" value="del" />
    <table class='table table-striped table-bordered table-hover'>
    <thead>
    <tr>
        <th class="table_head">Username</th>
        <th class="table_head"><?php echo Lang::T("CLASS"); ?></th>
        <th class="table_head">E-mail</th>
        <th class="table_head">IP</th>
        <th class="table_head">Added</th>
        <th class="table_head">Last Visited</th>
        <th class="table_head"><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr></thead><tbody>
    <?php while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td class="table_col1" align="center"><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["id"]); ?></a></td>
        <td class="table_col2" align="center"><?php echo Groups::get_user_class_name($row["class"]); ?></td>
        <td class="table_col1" align="center"><?php echo $row["email"]; ?></td>
        <td class="table_col2" align="center"><?php echo $row["ip"]; ?></td>
        <td class="table_col1" align="center"><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td class="table_col2" align="center"><?php echo TimeDate::utc_to_tz($row["last_access"]); ?></td>
        <td class="table_col1" align="center"><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile;?>
    </tbody></table>
    <div class='text-center'>
        <input type="submit" class="btn ttbtn  btn-sm" name="inc" value="Delete (inc. torrents)" />
        <input type="submit" class="btn ttbtn  btn-sm" value="Delete" />
    </div>
    </form>
    <?php
    endif;
    if ($data['count'] > 25) {
        echo $data['pagerbuttons'];
    }