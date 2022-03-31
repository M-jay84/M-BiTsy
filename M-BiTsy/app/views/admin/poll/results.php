<div class='table-responsive'><table class='table'><thead>
<tr>
<th>Username</th>
<th>Question</th>
<th>Voted</th>
</tr> <?php
while ($res = $data['poll']->fetch(PDO::FETCH_LAZY)) {
    $user = DB::raw('users', 'username,id', ['id'=>$res['userid']])->fetch();
    $option = "option" . $res["selection"];
    if ($res["selection"] < 255) {
        $vote = DB::raw('polls', $option, ['id'=>$res['pollid']])->fetch();
    } else {
        $vote["option255"] = "Blank vote";
    }
    $sond = DB::raw('polls', 'question', ['id'=>$res['pollid']])->fetch();
    ?>
    <tr>
    <td><b><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $user["id"]; ?>"><?php echo Users::coloredname($user['id']); ?></a></b></td>
    <td><?php echo $sond['question']; ?></td>
    <td><?php echo $vote["$option"]; ?></td>
    </tr>
    <?php
} ?>
</table></div>