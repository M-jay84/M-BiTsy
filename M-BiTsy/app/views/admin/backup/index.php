<?php
echo ("<table style='text-align:center;' width='100%'>");
echo ("<tr>");
echo ("<th><b>File</b></th>");
echo ("<th><b>Size</b></th>");
echo ("<th><b>Download</b></th>");
echo ("<th><b>Delete</b></th>");
echo ("</tr>");
foreach ($data['data1'] as $row) {
    echo ("<tr>");
    echo ("<td>" . $row['name'] . ".".$row['ext']."</td>");
    echo ("<td>" . $row['size'] . " KByte</td>");
    echo ("<td><a href='" . URLROOT . "/adminbackup/download?filename=" . $row['name'] . ".".$row['ext']."'><i class='fa fa-download tticon-red' title='Download'></i></a></td>");
    echo ("<td><a href='" . URLROOT . "/adminbackup/delete?filename=" . $row['name'] . ".".$row['ext']."'><i class='fa fa-trash-o tticon-red' title='Delete'></i></a></td>");
    echo ("</tr>");
}
echo ("</table><br>");
echo ("<center><a href='" . URLROOT . "/adminbackup/submit'>Backup Database</a> (or create a CRON task on " . URLROOT . "/adminbackup/submit)</center>");