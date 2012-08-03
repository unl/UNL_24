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

<?php 
if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'CHROME')==true)
{?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_url'); ?>/webkit.css" />
<?php	
}
?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>

<script>
$(document).ready(function(){

	// hide #footer-logo
	$("#footer-logo").hide();
	
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
			
                       
            <div id="box-scrool-bar">

            <div class="timeline-scroll-bar">
                <ul>
                <li class="anchor_post_current"><a id="back-top" class='anchor_post' href="#top">Now</a></li>
                <?php
                global $wpdb;
                $sql = "SELECT distinct date_format(post_date,'%M %Y') date,
                date_format(post_date,'%m_%Y')  as link
                FROM wp_posts WHERE post_type='post' and post_status='publish' order by date_format(post_date,'%Y/%m')desc";
                $results = $wpdb->get_results($sql);
                foreach($results as $row):
                echo "<li><a id='anchor_post_".++$count."' class='anchor_post' title='".$row->date."' href='#".$row->link."'>".$row->date."</a></li>";
                endforeach;
                ?>
                </ul>
            </div><!-- #timeline-scroll-bar -->
            </div><!-- #box-scrool-bar -->
            
            
            
            
            </div>
            
            
			<?php get_search_form(); ?>
      
            
</nav>


<div id="page" class="hfeed timeline-separator">
           
<header id="branding" role="banner">
			      
         
			<?php
				// Check to see if the header image has been removed
				$header_image = get_header_image();
				if ( ! empty( $header_image ) ) :
			?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
					// The header image
					// Check if this is a post or page, if it has a thumbnail, and if it's a big one
					if ( is_singular() &&
							has_post_thumbnail( $post->ID ) &&
							( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH ) ) ) &&
							$image[1] >= HEADER_IMAGE_WIDTH ) :
						// Houston, we have a new header image!
						echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
					else : ?>
					<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
				<?php endif; // end check for featured image or standard header ?>
			</a>
			<?php endif; // end check for removed header image ?>

			<?php
				// Has the text been hidden?
				if ( 'blank' == get_header_textcolor() ) :
			?>
				<div class="only-search<?php if ( ! empty( $header_image ) ) : ?> with-image<?php endif; ?>">
				<?php get_search_form(); ?>
				</div>
			<?php
				else :
			?>
			<?php endif; ?>


			<hgroup>
            
            <div id="photo-header"><img src="<?php bloginfo('template_url'); ?>/images/timeline-photo.jpg"></div>
            
				<h1 id="site-title"><span><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
				<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
           
                     
           	<?php 
            // Widget for Top Category
            dynamic_sidebar('sidebar-category-top'); 
            ?>
           
           
            </hgroup>
            
                       

<div id="social-icons">

<h3 class="social-icons-title">Follow me on Social Media</h3>
<?php if(get_option('tl_facebook_enabled')=='checked'){ ?>
<a title="Follow on Facebook" href="<?php echo get_option('tl_facebook');?>" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/facebook.gif" /></a> 
<?php } ?>

<?php if(get_option('tl_twitter_enabled')=='checked'){ ?>
<a href="<?php echo get_option('tl_twitter');?>" title="Follow on Twitter" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/twitter.gif" /></a> 
<?php } ?>

<?php if(get_option('tl_linkedin_enabled')=='checked'){ ?>
<a title="Follow on Linkedin" href="<?php echo get_option('tl_linkedin');?>" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/linkedin.gif" /></a> 
<?php } ?>

<?php if(get_option('tl_youtube_enabled')=='checked'){ ?>
<a title="Follow on YouTube" href="<?php echo get_option('tl_youtube');?>" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/youtube.gif" /></a> 
<?php } ?>

<?php if(get_option('tl_google_plus_enabled')=='checked'){ ?>
<a href="<?php echo get_option('tl_google_plus');?>" title="Follow on Google+" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/google-plus.gif" /></a> 
<?php } ?>

<?php if(get_option('tl_email_rss_enabled')=='checked'){ ?>
<a title="Follow with Email" href="<?php echo get_option('tl_email_rss');?>" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/email-rss.gif" /></a> 
<?php } ?>

<?php if(get_option('tl_feed_rss_enabled')=='checked'){ ?>
<a href="<?php echo get_option('tl_feed_rss');?>" title="Follow with Feed RSS" target="_blank">
<img src="<?php bloginfo('template_url'); ?>/images/social-icons/feed-rss.gif" /></a>
<?php } ?>


</div>


            <div id="site-featured">
            <?php 
			$thumbnails = get_posts('numberposts=4&category_name='.get_option('tl_category_name_in_evidence'));
			foreach ($thumbnails as $thumbnail) 
			{
				if ( has_post_thumbnail($thumbnail->ID)) 
				{
					echo '<div class="featured-box"><a href="' . get_permalink( $thumbnail->ID ) . '" title="' . esc_attr( $thumbnail->post_title ) . '">';
					echo get_the_post_thumbnail($thumbnail->ID, 'thumbnail',array('class' => 'featured-size') );
					echo '</a><a class="title-featured" href="' . get_permalink( $thumbnail->ID ) . '">'.esc_attr( $thumbnail->post_title ).'</a></div>';
				}
			}
			?>
            
            </div>
                       

	</header><!-- #branding -->





	<div id="main">