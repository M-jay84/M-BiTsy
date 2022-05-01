</div>
</div>
</div>

<!-- Footer -->
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
  </body>
</html>
<?php ob_end_flush();?>