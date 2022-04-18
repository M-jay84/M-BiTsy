<br><?php
if (Users::get('class') == _ADMINISTRATOR) { ?>
<div class="border ttborder">
    <center><b>Admin Only</b></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminbackup"><img src="<?php echo URLROOT; ?>/assets/images/admin/db_backup.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BACKUPS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminblock"><img src="<?php echo URLROOT; ?>/assets/images/admin/blocks.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BLOCKS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminmessage"><img src="<?php echo URLROOT; ?>/assets/images/admin/message_spy.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("MESSAGE_SPY"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminexceptions"><img src="<?php echo URLROOT; ?>/assets/images/admin/sql_error.png" border="0" width="20" height="20" alt="" />&nbsp;<b>SQL Error</b></a></li> 
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminstylesheet"><img src="<?php echo URLROOT; ?>/assets/images/admin/themes.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("THEME_MANAGEMENT"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminconfig"><img src="<?php echo URLROOT; ?>/assets/images/admin/config.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("Settings"); ?></b></a></li>
    </ul>
</div> <?php
} ?>
<br>
<div class="border ttborder">
    <center><b>Community</b></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminfaq"><img src="<?php echo URLROOT; ?>/assets/images/admin/faq.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("FAQ"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminforumcat"><img src="<?php echo URLROOT; ?>/assets/images/admin/forums.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("FORUM_MANAGEMENT"); ?></b></a></li>
	<li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminmessage/masspm"><img src="<?php echo URLROOT; ?>/assets/images/admin/mass_pm.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("MASS_PM"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminnews"><img src="<?php echo URLROOT; ?>/assets/images/admin/news.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("NEWS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminteam"><img src="<?php echo URLROOT; ?>/assets/images/admin/teams.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TEAMS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminpoll"><img src="<?php echo URLROOT; ?>/assets/images/admin/polls.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("POLLS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminrule"><img src="<?php echo URLROOT; ?>/assets/images/admin/rules.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("RULES"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminbonus"><img src="<?php echo URLROOT; ?>/assets/images/admin/seedbonus.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Seed Bonus</b></a></li>
    </ul>
</div>
<br>
<div class="border ttborder">
    <center><b>Torrents</b></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincategorie"><img src="<?php echo URLROOT; ?>/assets/images/admin/torrent_cats.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TORRENT_CAT_VIEW"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintorrentlang/torrentlang"><img src="<?php echo URLROOT; ?>/assets/images/admin/torrent_lang.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TORRENT_LANG"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintorrent"><img src="<?php echo URLROOT; ?>/assets/images/admin/torrents.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("TORRENTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintorrent/free"><img src="<?php echo URLROOT; ?>/assets/images/admin/free_leech.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Freeleech Torrents</b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/Adminsnatch"><img src="<?php echo URLROOT; ?>/assets/images/admin/hitnrun.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Hit & Runs</b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincomment"><img src="assets/images/admin/comments.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("LATEST_COMMENTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminpeer"><img src="<?php echo URLROOT; ?>/assets/images/admin/peer_list.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("PEERS_LIST"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminban/torrent"><img src="<?php echo URLROOT; ?>/assets/images/admin/banned_torrents.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BANNED_TORRENTS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admintag"><i class='fa fa-tag'></i>&nbsp;<b><?php echo Lang::T("Torrent Tags"); ?></b></a></li>
    </ul>
</div>