<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
    <div class="author-info">
          <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 30 ) ); ?>
			
			<h2 class="entry-title"><?php the_title(); ?></h2>
            
            <div class="date-post"><?php the_time('F j,  Y') ?></div>
            
            </div>
    
    <div class="entry-meta">
		
        <?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
        
	</div><!-- .entry-meta -->
		
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	
      
    <?php if(isFacebookCommentUsed()){include("comments-fb-post.php");}else{comments_template();}?>
    
</article><!-- #post-<?php the_ID(); ?> -->
