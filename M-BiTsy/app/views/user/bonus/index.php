<?php usermenu(Users::get('id')); ?>
  <center>
  This page displays the options where you can redeem accumulated bonus points. <font color=yellow><b><?php echo Users::get('seedbonus'); ?></font></b>
  </center>
  <br />
  <div class='table-responsive'> <table class='table table-striped'><thead><tr>
      <th>Option</th>
      <th>What is it?</th>
      <th>Points</th>
      <th>Exchange</th>
  </tr></thead>
  <?php foreach ($data['bonus'] as $row): ?>
  <form method="post" action="<?php echo URLROOT; ?>/bonus/submit">
  <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
  <tbody><tr>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo htmlspecialchars($row['descr']); ?></td>
      <td><?php echo $row['cost']; ?></td>
      <td><input type="submit"  class='btn btn-sm ttbtn' value="Exchange"></td>
  </tr></tbody>
  </form>
  <?php endforeach;?>
  </table>
  <ul>
     <li>You receive <font color=red><?php echo Config::get('BONUSPERTIME'); ?></font> points per <?php echo $data['autoclean_interval']; ?> minutes the system registers you as a seeder by torrent.</li>
  </ul>
  <ul>
     <li><a href='<?php echo URLROOT; ?>/profile?id=<?php echo Users::get('id'); ?>'>Return to profile</a></li>
  </ul>
  </div>