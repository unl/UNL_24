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
?>
<!DOCTYPE html>
<!--[if IEMobile 7 ]><html class="ie iem7"><![endif]-->
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"><![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"><![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7) ]><html class="ie" lang="en"><![endif]-->
<!--[if !(IEMobile) | !(IE)]><!--><html lang="en"><!--<![endif]-->
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
<link rel="shorcut icon" type="image/x-ico" href="http://www.unl.edu/wdn/templates_3.1/images/favicon.ico" />

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_url'); ?>/css/all.css" />

<?php 
if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'CHROME')==true)
{?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_url'); ?>/webkit.css" />
<?php	
}
?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

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
		
		$("#"+id).click(function (e){
			
			
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


				$('body').animate({
					scrollTop: (position.top-30)
				}, 800);
				
				e.preventDefault();
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
<div class="inner_wrapper">
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
                        <li class="anchor_post_current">
                            <a id="back-top" class='anchor_post' href="#top">Now</a>
                        </li>
                        <?php
                        global $wpdb;
                        $sql = "SELECT distinct post_date as date, id
                        FROM wp_posts WHERE post_type='post' and post_status='publish' order by post_date desc";
                        $results = $wpdb->get_results($sql);
                        foreach($results as $row):
                            echo "<li>
                                    <a id='anchor_post_".$row->id."' class='anchor_post' title='".mysql2date("F j, Y h:ia",$row->date)."' href='#post-".$row->id."'>".mysql2date("h:ia",$row->date)."</a>
                                 </li>";
                        endforeach;
                        ?>
                    </ul>
                </div><!-- #timeline-scroll-bar -->
            </div><!-- #box-scrool-bar -->
            
            
            
            
            </div>
            
            
			<?php get_search_form(); ?>
      
      </div>      
</nav>


<div id="page" class="hfeed timeline-separator inner_wrapper">
           
<header id="branding" role="banner">
			      
         
			<?php
				// Check to see if the header image has been removed
				$header_image = get_header_image();
				if ( ! empty( $header_image ) ) :
			?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" id="site-identity">
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
                <div class="text_wrapper">
    				<h1 id="site-title"><?php bloginfo( 'name' ); ?></h1>
    				<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
				</div>
           
             </hgroup>

			


            <div id="site-featured">
            <?php 
			$thumbnails = get_posts('numberposts=4&category_name=photos');
			foreach ($thumbnails as $thumbnail) 
			{
				if ( has_post_thumbnail($thumbnail->ID)) 
				{
					echo '<div class="featured-box"><a href="' . get_permalink( $thumbnail->ID ) . '" title="' . esc_attr( $thumbnail->post_title ) . '">';
					echo get_the_post_thumbnail($thumbnail->ID, 'thumbnail',array('class' => 'featured-size') );
					echo '</div>';
				}
			}
			?>
            
            </div>
            <div id="site-categories">
           	<?php 
            // Widget for Top Category
            dynamic_sidebar('sidebar-category-top'); 
            ?>
           
           
            </div>
                       

	</header><!-- #branding -->





	<div id="main" class="inner_wrapper">