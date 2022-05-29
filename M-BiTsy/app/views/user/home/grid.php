<?php
Style::begin(Lang::T("Grid")); ?>
<div class="row"> <?php

foreach ($data['sql'] as $row) { ?>
    <div class="col-md-3 text-center col">
        <a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"] ?>'>
        <img src="<?php echo MDBS::getimage($row); ?>" width="250" height="350" title="<?php echo CutName($row['name'], 40); ?>"></a><br>
        <p class="text-center"><?php echo CutName($row['name'], 30); ?></p>
        <p class="text-center"><font color="#00cc00"><b>S</b></font> <?php echo $row['seeders']; ?><font color="#FF0000"><b> L </b></font><?php echo $row['leechers']; ?></p>
    </div> <?php
} ?>

</div><?php
Style::end();