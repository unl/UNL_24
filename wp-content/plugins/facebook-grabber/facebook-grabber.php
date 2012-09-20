<?php
/*
Plugin Name: Facebook Grabber
Plugin URI: http://unl24.unl.edu/
Description: Scour open graph of Facebook for posts
Version: 0.1
Author: Seth Meranda
Author URI: http://unl24.unl.edu/
License: GPL2
*/
/*  Copyright YEAR  Seth Meranda  (email : seth@unl.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require("urlinker.php");

function fbGrabber_CSS(){
	echo '<link rel="stylesheet" type="text/css" href="'.get_option('siteurl').'/wp-content/plugins/facebook-grabber/fb_grabber.css" />';
}
add_action('admin_init', 'fbGrabber_CSS');
add_action('init', 'fbGrabber_CSS');
add_action( 'admin_menu', 'fbGrabber_menu' );

function fbGrabber_menu() {
	add_menu_page( 'Facebook Grabber', 'FB Grabber', 'manage_options', 'facebookgrabber', 'fbGrabber_post_display' );
}

function fbGrabber_post_display() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	// URL of Facebook Open Graph search. Returns JSON results
	$graphURL = 'https://graph.facebook.com/search?q=UNL24&type=post';
	$graphJSON = json_decode(file_get_contents($graphURL), true);
	
	echo '<h1>Public posts associated with #UNL24</h1><p> To use a post, copy the HTML from the corresponding textarea. Next, create a new post by pasting into the editor box. <strong>Make sure you are in the HTML editor mode!</strong></p>';
	
	echo '<ul class="admin_fb_list">';
	
	foreach ($graphJSON['data'] as $item) {
	    if ($item['message']) { // we need a message in order for this to work
    	    echo '<li><div class="fb_post_wrap" data-id="'.$item['id'].'">';
    	    echo '<div class="fb_user">';
        	    echo '<img class="fb_user_photo" src="https://graph.facebook.com/'.$item['from']['id'].'/picture" alt="'.$item['from']['name'].'" />';
    	    echo '</div>';
    	    echo '<div class="fb_post">';
    	    echo '<span class="fb_username fb_link">'.$item['from']['name'].'</span>';
    	    echo '<p class="fb_message">' . htmlEscapeAndLinkUrls($item['message']) . '</p>';
    	    if ($item['picture']) {
    	        echo '<div class="fb_shared_content">';
    	        if ($item['link']) {
    	            echo '<a href="' . $item['link'] . '" class="fb_story_link"><img src="' . $item['picture'] . '" alt="' . $item['name'] . '" /></a>';
    	        } else {
    	            echo '<img src="' . $item['picture'] . '" alt="' . $item['name'] . '" />';
    	        }
    	        if ($item['name']) {
    	            echo '<p class="fb_shared_name"><a href="' . $item['link'] . '" class="fb_story_link">'.$item['name'].'</a></p>';
    	        }
				echo '<div style="clear:both;"></div>';
				echo '</div>';
	 	    }
    	    
    	    echo '</div>';
			echo '<div class="bbp-actions" style="font-size:12px; width:100%; padding:0; margin-top:1em;">' .
				 	'<img align="top" src="http://g.etfv.co/http://www.facebook.com"><a style="margin-left: 0.3em;">posted with #UNL24</a>' .
			     '</div>';
    	    echo '</div>';
    	    echo '<textarea>';
    	        echo '<div class="fb_post_wrap" data-id="'.$item['id'].'">';
        	    echo '<div class="fb_user">';
            	    echo '<img class="fb_user_photo" src="https://graph.facebook.com/'.$item['from']['id'].'/picture" alt="'.$item['from']['name'].'" />';
        	    echo '</div>';
        	    echo '<div class="fb_post">';
        	    echo '<span class="fb_username fb_link">'.$item['from']['name'].'</span>';
        	    echo '<p class="fb_message">' . htmlEscapeAndLinkUrls($item['message']) . '</p>';
        	    if ($item['picture']) {
        	        echo '<div class="fb_shared_content">';
        	        if ($item['link']) {
        	            echo '<a href="' . $item['link'] . '" class="fb_story_link"><img src="' . $item['picture'] . '" alt="' . $item['name'] . '" /></a>';
        	        } else {
        	            echo '<img src="' . $item['picture'] . '" alt="' . $item['name'] . '" />';
        	        }
        	        if ($item['name']) {
        	            echo '<p class="fb_shared_name"><a href="' . $item['link'] . '" class="fb_story_link">'.$item['name'].'</a></p>';
        	        }
					echo '<div style="clear:both;"></div>';
					echo '</div>';
        	    }
        	    
        	    echo '</div>';
				echo '<div class="bbp-actions" style="font-size:12px; width:100%; padding:0; margin-top:1em;">' .
						 '<img align="top" src="http://g.etfv.co/http://www.facebook.com"><a style="margin-left: 0.3em;">posted with #UNL24</a>' .
				     '</div>';
        	    echo '</div>';
    	    echo '</textarea>';
    	    echo '</li>';
	    }
	}
	
	echo '</ul>';
}
?>