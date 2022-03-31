<center>Please <a href="<?php echo URLROOT ?>/group/staff">contact</a> 
a member of staff if you would like a new team creating</center><br>
 <?php
 while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
      <div class="row frame-header">
           <div class="col-md-1">
           </div>
           <div class="col-md-11">
                Owner: <?php echo Users::coloredname($row["owner"]) ? '<a href="'.URLROOT.'/profile?id=' . $row["owner"] . '">' . Users::coloredname($row["owner"]) . '</a>' : "Unknown User"; ?> - Added: <?php echo TimeDate::utc_to_tz($row["added"]); ?>
           </div>
      </div>
      <div class="row">
           <div class="col-md-1">
                <img src="<?php echo htmlspecialchars($row["image"]); ?>" border="0" alt="<?php echo htmlspecialchars($row["name"]); ?>" title="<?php echo htmlspecialchars($row["name"]); ?>" />
           </div>
           <div class="col-md-11">
                <b><?php echo Lang::T('NAME'); ?>:</b><?php echo htmlspecialchars($row["name"]); ?><br /><b>Info:</b> <?php echo format_comment($row["info"]); ?>
           </div>
      <div class="row">
           <div class="col-md-1">
               <b>Members:</b>
           </div>
           <div class="col-md-11"> <?php
               foreach (explode(',', $row['members']) as $member): $member = explode(" ", $member);?>
	            <a href="<?php echo URLROOT ?>/profile?id=<?php echo $member[0]; ?>"><?php echo Users::coloredname($member[0]); ?></a>,
	            <?php
               endforeach; ?>
           </div>
      </div>
      </div>
	<br />
	<?php 
endwhile;