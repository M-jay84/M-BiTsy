
<form method="post" action="<?php echo URLROOT; ?>/adminsearch/advanced">
  <div class="form-group row">
    <label for="name" class="col-sm-1 col-form-label">name</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $data['name'] ?>">
    </div>

    <label for="ratio" class="col-sm-1 col-form-label">ratio</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" id="ratio" name="ratio" value="<?php echo $data['ratio'] ?>">
    </div>

    <label for="member" class="col-sm-1 col-form-label">Status</label>
    <div class="col-sm-3">
        <select id="member" name="member" >
        <option value="">--</option>
        <option value="confirmed">confirmed</option>
        <option value="pending">pending</option>
        </select>
    </div>

    <label for="email" class="col-sm-1 col-form-label">Email</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" id="email" name="email" value="<?php echo $data['email'] ?>">
    </div>

    <label for="uploaded" class="col-sm-1 col-form-label">uploaded</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" id="uploaded" name="uploaded" value="<?php echo $data['uploaded'] ?>">
    </div>

    <label for="account" class="col-sm-1 col-form-label">Enabled</label>
    <div class="col-sm-3">
        <select id="account" name="account" >
        <option value="">--</option>
        <option value="yes">yes</option>
        <option value="no">no</option>
        </select>
    </div>

    <label for="ip" class="col-sm-1 col-form-label">ip</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" id="ip" name="ip" value="<?php echo $data['ip'] ?>">
    </div>

    <label for="downloaded" class="col-sm-1 col-form-label">downloaded</label>
    <div class="col-sm-3">
      <input type="text" class="form-control" id="downloaded" name="downloaded" value="<?php echo $data['downloaded'] ?>">
    </div>

    <label for="class" class="col-sm-1 col-form-label">class</label>
    <div class="col-sm-3">
        <select id="class" name="class">
         <option value="">any</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
	   </select>
    </div>

    <label for="warned" class="col-sm-1 col-form-label">warned</label>
    <div class="col-sm-3">
        <select  id="warned" name="warned">
        <option value="">--</option>
        <option value="yes">yes</option>
        <option value="no">no</option>
        </select>
    </div>
  </div><br>
  <p class='text-center'><button type="submit" class="btn ttbtn "><?php echo Lang::T("Submit"); ?></button></p>
</form>

<form action='<?php echo URLROOT ?>/adminsearch/advanced?do=warndisable' method='post'>
<div class='table-responsive'>
<table class='table'><thead><tr>
    <th><?php echo Lang::T("NAME") ?></th>
	<th>IP</th>
	<th><?php echo Lang::T("EMAIL") ?></th>
    <th>Joined:</th>
    <th>Last Seen:</th>
    <th>Status</th>
    <th>Enabled</th>
    <th>Ratio</th>
    <th>Uploaded</th>
    <th>Downloaded</th>
    <th>Class</th>
    <th>Warned</th>
    </tr></thead> <?php
foreach ($data['results'] as $user) {
    $userratio = $user["downloaded"] > 0 ? number_format($user["uploaded"] / $user["downloaded"], 1) : "---"; ?>
    <tr>
    <td><b><a href='<?php echo  URLROOT ?>/profile?id=<?php echo $user['id'] ?>'><?php echo  Users::coloredname($user['id']) ?></a></b></td>
    <td><?php echo  $user['ip'] ?></td>
    <td><?php echo  $user['email'] ?></td>
    <td><?php echo TimeDate::utc_to_tz($user['added']) ?></td>
    <td><?php echo $user['last_access'] ?></td>
    <td><?php echo $user['status'] ?></td>
    <td><?php echo $user['enabled'] ?></td>
    <td><?php echo get_ratio_color($userratio) ?></td>
    <td><?php echo mksize($user['uploaded']) ?></td>
    <td><?php echo mksize($user['downloaded']) ?></td>
    <td><?php echo $user['class'] ?></td>
    <td><input type='checkbox' name="warndisable[]" id="warndisable" value='<?php echo $user['id'] ?>' ></td>
    </tr> <?php
} ?>
</table>
</div>

<?php echo $data['pager'] ?>

<div class='ttform'>
    <div class='text-center'>
	<input type='submit' name='disable'  id='disable' class='btn btn-danger  btn-sm' value="Disable Selected Accounts">
    <input type='submit' name='warn' id='warn' class='btn btn-danger  btn-sm' value="Warn Selected"><br><br>
    <input type='submit' name='enable' id='enable' class='btn btn-success  btn-sm' value="Enable Selected Accounts">
    <input type='submit' name='unwarn' id='unwarn' class='btn btn-success  btn-sm' value="Remove Warning Selected"><br><br>
	Mod Comment (reason):<br>
     <input type='text' name='warnpm' />
    </div>
</div>
</form>