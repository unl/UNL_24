<?php
/*
Template Name: Photo Gallery
*/
?>

<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> xmlns:fb="https://www.facebook.com/2008/fbml">
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="shorcut icon" type="image/x-ico" href="<?php bloginfo('url'); ?>/favicon.ico" />

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>


<script>
$(document).ready(function(){

		// fade in #footer-logo
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#footer-logo').fadeIn();
			} else {
				$('#footer-logo').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#footer-logo a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
	
		// scroll body to 0px on click
		$('#back-top').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
	});
	
	

	$(".anchor_post").each(function () {
		var id = this.id;
		
		$("#"+id).click(function (){
			
			
			$(".anchor_post").parent().removeClass("anchor_post_current");
			$(this).parent().addClass("anchor_post_current");
			
			if(id=='back-top') 
				return
			
			anchor_href = $("#"+id).attr('href'); // #mm_yyyy
			if($(anchor_href).length==false){
				location.href='/date/'+anchor_href.substring(4)+'/'+anchor_href.substring(1,3)
			}
			else
			{
				var p = $(anchor_href);
				var position = p.position();


				$('body,html').animate({
					scrollTop: (position.top-60)
				}, 800);
			}
			
		});
	});
	
	
	
	

});
</script>

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

</head>



<body <?php body_class(); ?>>


<nav id="access" role="navigation">

<h3 class="assistive-text"><?php _e( 'Main menu', 'twentyeleven' ); ?></h3>
				<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
				<div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to primary content', 'twentyeleven' ); ?></a></div>
				<div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to secondary content', 'twentyeleven' ); ?></a></div>
                
  <?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
			
            <div id="timeline-bar">

          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="no-margin" rel="home" title="The First Timeline WordPress Theme"><img class="logo" src="<?php if(get_option('tl_logo_header')!=""){echo get_option('tl_logo_header');}else{echo get_bloginfo('stylesheet_directory')."/images/logo.png";} ?>"></a>
          
                     
            <?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			

            
            </div>
            
            
			<?php get_search_form(); ?>
      
            
</nav>


<div id="page" class="hfeed timeline-separator">




<section id="gallery">
			<div id="content" role="main">

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

    <div class="post-gallery">
           
        <div class="entry-content">
            <?php the_content(); ?>
                
        </div><!-- .entry-content -->
       
        
    </div><!-- #post-## -->


<div class="post-gallery-comment">
<?php if(isFacebookCommentUsed()){include("comments-fb-post.php");}else{comments_template();}?>
</div>


<div class="sidebar-post-gallery">
 <?php if(get_option('tl_fanpage_facebook')!="") { ?>
		<script type="text/javascript">FB.init("1690883eb733618b294e98cb1dfba95a");</script>
<fb:fan profile_id="<?php echo get_option('tl_fanpage_facebook');?>" stream="0" connections="30" logobar="0" width="230" height="400" css="<?php bloginfo('template_url'); ?>/box-like-style.css?5"></fb:fan>
        <?php } ?>

</div>

</div>
<?php endwhile; ?>

		</section><!-- #primary -->


<?php get_footer(); ?>