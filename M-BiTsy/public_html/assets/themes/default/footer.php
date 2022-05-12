<?php
if (Config::get('MIDDLENAV')) {?>
  <?php Blocks::middle();
}?>

</div>

<?php
if (Config::get('RIGHTNAV')) {?>
<div class="col  ttsidebar">
    <?php Blocks::right();?>
</div>
<?php
} ?>

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
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<script src="<?php echo URLROOT; ?>/assets/js/overlib.js"></script>
<script type="module">
      import Tags from "https://cdn.jsdelivr.net/gh/lekoala/bootstrap5-tags@master/tags.js";
      Tags.init("select[multiple]");
</script>
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
              $('#shoutbox').load('<?php echo URLROOT; ?>/shoutbox/chat');
            },
            error: function (data) {
                console.log('An error occurred.');
            },
        });
    });
</script>
    <script>
        function updateShouts(){
            // Assuming we have #shoutbox
            $('#shoutbox').load('<?php echo URLROOT; ?>/shoutbox/chat');
        }
        setInterval( "updateShouts()", 300000 );
		updateShouts();
    </script>
<script>
let items = document.querySelectorAll('.carousel .carousel-item')

items.forEach((el) => {
    const minPerSlide = 12
    let next = el.nextElementSibling
    for (var i=1; i<minPerSlide; i++) {
        if (!next) {
            // wrap carousel by using first child
        	next = items[0]
      	}
        let cloneChild = next.cloneNode(true)
        el.appendChild(cloneChild.children[0])
        next = next.nextElementSibling
    }
})
</script>
<script>
$(document).ready(function(){
	$("#search-box").keyup(function(){
		$.ajax({
		type: "POST",
		url: "<?php echo URLROOT; ?>/message/findUser",
		data:'keyword='+$(this).val(),
		beforeSend: function(){
			$("#search-box").css("background","#FFF url(<?php echo URLROOT; ?>/LoaderIcon.gif) no-repeat 165px");
		},
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#search-box").css("background","#FFF");
		}
		});
	});
});

function userCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
</script>
  </body>
</html>
<?php ob_end_flush();?>