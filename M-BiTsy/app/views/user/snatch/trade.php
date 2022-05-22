<p class='text-center'>
    To solve this problem you must to keep seeding these torrents for <?php Config::get('HNR_SEEDTIME') ?> hours or until ratio becomes 1:1<br>
    But if you want a fast way, you can trade to delete these recordings With of Upload<p>

<form method="post" action="<?php echo URLROOT; ?>/snatch/trade">
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?php echo Lang::T("TORRENT_NAME"); ?></th>
                    <th><i class='fa fa-upload tticon' title='Uploaded'></i></th>
                    <th><i class='fa fa-download tticon' title='Downloaded'></i></th>
                    <th><?php echo Lang::T("SEED_TIME"); ?></th>
                    <th><?php echo Lang::T("DELETE"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)) :
                    $torid = $row['tid'];
                    $tosize = $row['size'];
                    $upload = $row['uploaded'];
                    $points = $row['seedbonus'];
                    $smallname = htmlspecialchars(CutName($row['name'], 40));
                    $dispname = "<b>" . $smallname . "</b>"; ?>
                    <tr>
                        <td align='left' class='table_col1'><a href=<?php echo URLROOT ?>/torrent?id=<?php $row['tid'] ?>&hit=1><?php echo $dispname ?></a></td>
                        <td class="table_col2">
                            <font color="#27B500"><?php echo mksize($row['upload']); ?></font>
                        </td>
                        <td class="table_col1">
                            <font color="#FF2200"><?php echo mksize($row['download']); ?></font>
                        </td>
                        <td class="table_col2"><?php echo ($row['uploaded']) ? TimeDate::mkprettytime($row['last_time']) : '---'; ?></td>
                        <td class="table_col1" align="left">
                            <?php
                            if ($points >= 100) { ?>
                                <input type='hidden' name='torid' value="<?php echo $torid; ?>">
                                <input type="submit" class="button" name="requestpoints" value="Delete">&nbsp; <?php echo Lang::T("SNATCHLIST_COST"); ?> <font color="#FF2200"><b>100</b></font> <?php echo Lang::T("SNATCHLIST_POINTS_OF_SEED_BONUS"); ?>
                            <?php } else { ?>
                                <font color="#FF1200">&nbsp;<?php echo Lang::T("SNATCHLIST_YOU_DONT_HAVE_ENOUGH"); ?> <b><?php echo Lang::T("SNATCHLIST_SEEDBONUS"); ?></b> <?php echo Lang::T("SNATCHLIST_FOR_TRADING"); ?></font><?php
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                if ($upload > $tosize) { ?>
                                <input type='hidden' name='torid' value="<?php echo $torid; ?>">
                                <div style="margin-top:2px"><input type="submit" class="button" name="requestupload" value="Delete">&nbsp; <?php echo Lang::T("SNATCHLIST_COST"); ?> <font color="#FF2200"><b><?php echo mksize($tosize); ?></b></font> <?php echo Lang::T("SNATCHLIST_UPLOAD"); ?></div>
                            <?php } else { ?>
                                <div style="margin-top:2px">
                                    <font color="#FF1200">&nbsp;<?php echo Lang::T("SNATCHLIST_YOU_DONT_HAVE_ENOUGH"); ?> <b><?php echo Lang::T("SNATCHLIST_UPLOAD"); ?></b> <?php echo Lang::T("SNATCHLIST_FOR_TRADING"); ?></font>
                                </div> <?php
                                                                                                                                                                                                                                } ?>
                        </td>
                    </tr><?php
                        endwhile; ?>
            </tbody>
        </table>
    </div>
</form>