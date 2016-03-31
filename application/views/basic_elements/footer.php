<!-- VENDOR -->
<script src="<?php echo js_url("jquery-ui.min.js") ?>"></script>
<script src="<?php echo js_url("bootstrap.min.js") ?>"></script>
<!-- END VENDOR -->


<!-- !IMPORTANT DEPENDENCIES -->
<script src="<?php echo js_url("jquery.ui.touch-punch.min.js") ?>"></script>
<script src="<?php echo js_url("jquery.cookie.min.js") ?>"></script>
<script src="<?php echo js_url("screenfull.min.js") ?>"></script>
<script src="<?php echo js_url("jquery.autogrowtextarea.min.js") ?>"></script>
<script src="<?php echo js_url("jquery.nicescroll.min.js") ?>"></script>
<script src="<?php echo js_url("bootbox.min.js") ?>"></script>
<script src="<?php echo js_url("switchery.min.js") ?>"></script>
<script src="<?php echo js_url("toastr.min.js") ?>"></script>
<script src="<?php echo js_url("components-setup.min.js") ?>"></script>
<!-- END !IMPORTANT DEPENDENCIES -->


<!-- WRAPKIT -->
<script src="<?php echo js_url("wrapkit-utils.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-layout.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-header.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-sidebar.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-content.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-footer.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-panel.min.js") ?>"></script>
<script src="<?php echo js_url("wrapkit-setup.min.js") ?>"></script>
<!-- END WRAPKIT -->

<?php
if(isset($js)) {
    foreach($js as $value) {
        echo '<script src="'. js_url($value) .'"></script>';
    }
}
?>

<script src="<?php echo js_url("devs/main.js") ?>"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-64230601-1', 'auto');
  ga('send', 'pageview');

</script>

</body>
</html>