<?php
forumheader('search'); 
?>
<div class="ttform">
    <form method='get' action='<?php echo URLROOT; ?>/forum/result'>
        <p class="text-center"><?php echo Lang::T("SEARCH") ?></p>
        <div>
            <input id="keywords" type="text" class="form-control" name="keywords" minlength="3" maxlength="25" required autofocus>
        </div><br>
        <div class="text-center">
            <button type='submit' class='btn btn-sm ttbtn' value='Search'>Search Topics</button>&nbsp;&nbsp;
            <button  type='Submit' class='btn btn-sm ttbtn' name='type' value='deep'>Search Posts</button>
        </div>
    </form>
</div>
<p class="text-center">Search Phrase: <b><?php echo htmlspecialchars($data['keywords']) ?></b></p>
<div class='table'>
    <table class='table table-striped'><thead><tr>
        <th>Topic Subject</th>
        <th>Forum</th>
        <th>Added</th>
        <th>Posted By</th>
        </tr></thead> <?php
        foreach ($data['res'] as $row) {
            // todo move sql
            $res2 = DB::raw('forum_forums', 'name,minclassread, guest_read', ['id'=>$row['forumid']]);
            $forum = $res2->fetch(PDO::FETCH_ASSOC);
            if ($forum["name"] == "" || ($forum["minclassread"] > Users::get("class") && $forum["guest_read"] == "no")) {
                continue;
            } ?>
            <tr>
            <td><a href='<?php echo URLROOT ?>/topic?topicid=<?php echo $row['topicid'] ?>'><?php echo $row['subject'] ?></a></td>
            <td><a href='<?php echo URLROOT ?>/forum/view&forumid=<?php echo $row['forumid'] ?>'><?php echo $forum['name'] ?></a></td>
            <td><?php echo $row['added'] ?></td>
            <td><a href='<?php URLROOT ?>/profile?id=<?php echo $row['userid'] ?>'><?php echo Users::coloredname($row['userid']) ?></a></td>
            </tr> <?php
        } ?>
    </table>
</div>
<?php print("$data[pagerbuttons]\n"); ?>
<p class="text-center"><a href='<?php echo URLROOT ?>/forum/search' class='btn ttbtn'>Search Again</a></p>