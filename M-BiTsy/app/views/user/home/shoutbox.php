<?php
Style::begin(Lang::T("SHOUTBOX"));
if ($_SESSION['loggedin']) {
    ?><!-- ajax shoutbox -->
    <p id="shoutbox"></p>
     
	<form id='contactForm1' name='contactForm1' action='<?php echo URLROOT ?>/shoutbox/add' method='post'>
    <div class="row">
        <div class="col-md-12"> <?php
           echo shoutbbcode("contactForm1", "message"); ?>
        </div>
    </div>
    </form> <?php
} else {
    echo '<p id="shoutbox"></p>';
}
Style::end();