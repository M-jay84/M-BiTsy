<?php
if ($_SESSION['loggedin'] === true && Users::get("control_panel") == "yes") {
    Style::block_begin(Lang::T("AdminCP"));
    ?>
    <select name="admin" style="width: 95%" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value; }">
    <option value="-1">Navigation</option>
    <option value="<?php echo URLROOT; ?>/adminsearch/advanced">Advanced User Search</option>
    <option value="<?php echo URLROOT; ?>/adminavatar">Avatar Log</option>
    <option value="<?php echo URLROOT; ?>/adminbackup">Backups</option>
    <option value="<?php echo URLROOT; ?>/adminban/ip">Banned Ip's</option>
    <option value="<?php echo URLROOT; ?>/adminban/torrent">Banned Torrents</option>
    <option value="<?php echo URLROOT; ?>/adminblock&amp;do=view">Blocks</option>
    <option value="<?php echo URLROOT; ?>/admincensor/cheats">Detect Possibe Cheats</option>
    <option value="<?php echo URLROOT; ?>/adminban/email">E-mail Bans</option>
    <option value="<?php echo URLROOT; ?>/adminfaq">FAQ</option>
    <option value="<?php echo URLROOT; ?>/admintorrent/free">Freeleech Torrents</option>
    <option value="<?php echo URLROOT; ?>/admincomment">Latest Comments</option>
    <option value="<?php echo URLROOT; ?>/adminmessage/masspm">Mass PM</option>
    <option value="<?php echo URLROOT; ?>/adminmessage">Message Spy</option>
    <option value="<?php echo URLROOT; ?>/adminnews&amp;do=view">News</option>
    <option value="<?php echo URLROOT; ?>/adminpeer">Peers List</option>
    <option value="<?php echo URLROOT; ?>/adminpoll&amp;do=view">Polls</option>
    <option value="<?php echo URLROOT; ?>/adminreport&amp;do=view">Reports System</option>
    <option value="<?php echo URLROOT; ?>/adminrule&amp;do=view">Rules</option>
    <option value="<?php echo URLROOT; ?>/adminlog">Site Log</option>
    <option value="<?php echo URLROOT; ?>/adminteam/create">Teams</option>
    <option value="<?php echo URLROOT; ?>/adminstylesheet">Theme Management</option>
    <option value="<?php echo URLROOT; ?>/admincategorie&amp;do=view">Torrent Categories</option>
    <option value="<?php echo URLROOT; ?>/admintorrentlangs&amp;do=view">Torrent Languages</option>
    <option value="<?php echo URLROOT; ?>/admintorrent">Torrents</option>
    <option value="<?php echo URLROOT; ?>/admingroup&amp;do=view">Usergroups View</option>
    <option value="<?php echo URLROOT; ?>/adminwarning">Warned Users</option>
    <option value="<?php echo URLROOT; ?>/adminwhoswhere">Who's Where</option>
    <option value="<?php echo URLROOT; ?>/admincensor">Word Censor</option>
    <option value="<?php echo URLROOT; ?>/adminforumcat">Forum Management</option>
    </select>
    <?php
    Style::block_end();
}