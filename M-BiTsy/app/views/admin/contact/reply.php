<?php
$array = $data['res2']->fetch(PDO::FETCH_ASSOC);
?>
<center>
<b>Answering to <a href='<?php echo URLROOT; ?>/admincontact/viewpm?id=<?php echo $array['id']; ?>'>
<i><?php echo $array["subject"]; ?></i></a> sent by <i><?php echo $data['receiver']; ?></i></b></center>

<form method=post name=message action='<?php echo URLROOT; ?>/admincontact/takeanswer'>
<div class="text-center"> 
    <b>Message:</b><br>
    <textarea name=msg cols=90 rows=15><?php echo htmlspecialchars($array['msg']); ?></textarea><br>
    <button type="submit" class="btn ttbtn">Send it!</a>
    <input type=hidden name=receiver value=<?php echo $data['receiver']; ?>>
    <input type=hidden name=answeringto value=<?php echo $data['answeringto']; ?>>
</div>
</form>