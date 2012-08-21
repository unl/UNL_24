<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				get_sidebar( 'footer' );
			?>
</div><!-- #page -->

<div id="footer-logo">
<a href="#top" title="Back Top"><img class="logo" src="<?php if(get_option('tl_logo_footer')!=""){echo get_option('tl_logo_footer');}else{echo get_bloginfo('stylesheet_directory')."/images/logo-footer.png";} ?>"></a>
</div>

			<div id="site-generator">
                A day in the life at <a href="http://www.unl.edu/">University of Nebraska&ndash;Lincoln</a> on September 24, 2012. Layout inspired by <a href="http://www.facebook.com/about/timeline">Facebook Timeline</a>.
    </div>

	</footer><!-- #colophon -->

<?php wp_footer(); ?>


<?php 
// google tracking analytics
if(get_option('tl_google_track')!="") { ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo get_option('tl_google_track');?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php } ?>
</body>
</html>