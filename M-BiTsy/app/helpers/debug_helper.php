<?php

// Display Array To View
function debugviewdata($data)
{
    if (DEBUG_VIEW_DATA) {
        $viewdata = "<br>Print View Data Array<br><pre>" . print_r($data, true) . "</pre><br>";
    }

    return $viewdata;
}

// Display SQL Array
function debugsqldata($data)
{
    if (DEBUG_SQL_DATA) {
        $sqldata = "<br>Print SQL Data Array<br><pre>" . print_r($data, true) . "</pre><br>";
    }

    return $sqldata;
}

// SQL Speed
function debugsqlspeed($time = '')
{
    if (DEBUG_SQL_SPPED) {
        if ($time == 'start') {
            $GLOBALS['query'] = array_sum(explode(" ", microtime()));
        } else {
            $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['query'];
            $sqlspeed = "<br>Print SQL Speed<br><pre>" . print_r($totaltime) . "</pre><br>";
        }
    }

    return $sqlspeed;
}

// Check Page Speed
function debugpagespeed($time = '')
{
    if (DEBUG_PAGE_SPEED) {
        if ($time == 'start') {
            $GLOBALS['page'] = array_sum(explode(" ", microtime()));
        } else {
            $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['page'];
            $pagespeed = "<br>Print Page Speed<br>" . var_dump($totaltime) . "<br>";
        }
    }

    return $pagespeed;
}

// Run Page Speed
$GLOBALS['debugpagespeed'] = debugpagespeed('start');

// Show Debug Data
function debugdata()
{
    if (DEBUG_PAGE_SPEED) {
        $pagespeed = debugpagespeed('end');
        echo $pagespeed;
    }

    if (DEBUG_VIEW_DATA) {
        global $debugviewdata;
        echo $debugviewdata;
    }
}