<?php usermenu($data['id']);
if ($data['count']) {
    print($data['pagerbuttons']);
    torrenttable($data['res']);
    print($data['pagerbuttons']);
} else {
    print("<br><br><center><b>" . Lang::T("UPLOADED_TORRENTS_ERROR") . "</b></center><br />");
}