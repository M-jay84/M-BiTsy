<?php

class Redirect
{

    // Instant Redirect
    public static function to($url)
    {
        if (!headers_sent()) {
            header("Location: " . $url, true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
            echo '</noscript>';
            exit();
        }
    }

    // Redirect With Timed Message
    public static function autolink($al_url, $al_msg, $al_time = 5)
    {
        Style::error_header("info");
        Style::begin("Info");
        echo "\n<meta http-equiv=\"refresh\" content=\"$al_time; url=$al_url\">\n"; ?>
        <center>
            <b><?php echo $al_msg; ?></b><br>
            <b>Redirecting ...</b>&nbsp;
            <b>[ <a href='<?php echo $al_url; ?>'>link</a> ]</b>&nbsp;
            </center>
        <?php
        Style::end();
        Style::error_footer();
        die();
    }

}