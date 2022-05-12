</div>

<?php
if (Config::get('RIGHTNAV')) {?>
<div class="col-sm-2">
    <?php require APPROOT . '/views/admin/admincp/right.php'; ?>
</div>
<?php
} ?>

</div>
</div>
<footer class="mt-auto">
<hr />
<ul class="list-unstyled text-center card">
    <li><?php printf(Lang::T("POWERED_BY_TT"), VERSION);?></li>
    <li><?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];?></li>
    <li><?php printf(Lang::T("PAGE_GENERATED_IN"), $totaltime);?></li>
    <li><a href="https://torrenttrader.uk" target="_blank">torrenttrader.uk</a> -|- <a href='<?php echo URLROOT; ?>/rss'><i class="fa fa-rss-square"></i> <?php echo Lang::T("RSS_FEED"); ?></a> - <a href='<?php echo URLROOT; ?>/rss/custom'><?php echo Lang::T("FEED_INFO"); ?></a></li>
    <li>Bootstrap v5.1.0 -|- jQuery 3.4.1</li>
	<li>Update By: <a href="https://github.com/M-jay84/M-BiTsy" target="_blank">M-jay</a> 2020</li>
  </ul>
</footer>
<!-- Dont Change -->
<script src="<?php echo URLROOT; ?>/assets/js/jquery-3.3.1.min.js"></script>
<script src="<?php echo URLROOT; ?>/assets/js/popper.js"></script>
<script src="<?php echo URLROOT; ?>/assets/js/bootstrap.min.js"></script>
<script src="<?php echo URLROOT; ?>/assets/js/java_klappe.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<!-- ajax shoutbox -->
<script type="text/javascript">
    var frm = $('#contactForm1');

    frm.submit(function (e) {

        e.preventDefault();

        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),
            success: function (data) {
                console.log('Submission was successful.');
            },
            complete: function(){
              $("#message").focus().val('');
              $('#shoutboxstaff').load('<?php echo URLROOT; ?>/adminshoutbox/loadchat');
            },
            error: function (data) {
                console.log('An error occurred.');
            },
        });
    });
</script>
<script>
        function updatestaffShouts(){
            // Assuming we have #shoutbox
            $('#shoutboxstaff').load('<?php echo URLROOT; ?>/adminshoutbox/loadchat');
        }
        setInterval( "updatestaffShouts()", 300000 );
		updatestaffShouts();
    </script>
   
</body>
</html>
<?php ob_end_flush();?>