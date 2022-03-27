<?php usermenu($data['id']);
if ($data['count']) {
    print($data['pager']);
    torrenttable($data['res']);
    print($data['pager']);
} else {
    print("<br><br><center><b>" . Lang::T("UPLOADED_TORRENTS_ERROR") . "</b></center><br />");
}
