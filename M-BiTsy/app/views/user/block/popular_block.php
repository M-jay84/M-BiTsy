<?php
if ($_SESSION['loggedin'] == true) {
$tcat = implode(',', PopularCats);
$i = 0;
$q = "SELECT torrents.id, torrents.name, torrents.image1, torrents.image2, torrents.tmdb, torrents.imdb, categories.name as cat_name, categories.parent_cat as cat_parent FROM torrents LEFT JOIN categories ON torrents.category=categories.id WHERE banned = 'no' AND visible = 'yes' AND category IN ($tcat) ORDER BY torrents.seeders + torrents.leechers DESC LIMIT 10"; 
$q = DB::run($q);

Style::block_begin('Popular Movies');
print '<table>';
print '<tr>';
while ( $r = $q->fetch(PDO::FETCH_ASSOC) )
{
	print (($i && $i % 2) ? '
	<td><a href="'.URLROOT.'/torrent?id='.$r['id'].'">
	<img src="' . MDBS::getimage($r) . '" height="100" width="100" border="0" /></a></td>
	</tr>' : '
	<tr><td><a href="'.URLROOT.'/torrent?id='.$r['id'].'">
	<img src="' . MDBS::getimage($r) . '" height="100" width="80" border="0" /></a></td>');
	$i++;
} 
print '</tr>';
print '</table>';
Style::block_end();
}