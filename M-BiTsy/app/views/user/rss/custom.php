What is RSS? Take a look at the <a href="http://wikipedia.org/wiki/RSS_%28file_format%29">Wiki</a> to <a href="http://wikipedia.org/wiki/RSS_%28file_format%29">learn more</a>.<br /><br />

<form action="<?php echo URLROOT; ?>/rss/submit" method="post">
<div align="left">
		Categories: (Leave blank for All)<br />
		<?php
		while ($row = $data['stmt']->fetch(PDO::FETCH_LAZY)) {
			echo '<input type="checkbox" name="cats[]" value="' . $row['id'] . '" /> ' . htmlspecialchars("$row[parent_cat] - $row[name]") . '<br />';
		} ?>
	</div><br>
	<div align="left">
		<?php echo Lang::T("USER"); ?>:<br>
		<input type="number" name="user" /> User (id)<br />
	</div><br>
	<div align="left">
		<?php echo Lang::T("INCLUDE_DEAD"); ?>:<br>
		<input type="checkbox" name="incldead" value="1" />
	</div><br>
	<div align="left">
		<input type="submit" value="Get Link" />
	</div>
</form>
<br />
<div align="left">
	Quick information regarding our RSS:
	<ul>
		<li>Our RSS feeds are properly validated by true RSS 2.0 XML Parsing Standards. Visit FeedValidator.org to validate.</li>
		<li>Our feeds display only the latest 50 uploaded Torrents as default.</li>
	</ul>
</div>