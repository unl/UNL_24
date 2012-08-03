<?php

function TIMELINE_add_pages()
{
	add_menu_page('Customize Theme', 'Timeline WP', 8, __FILE__, 'TIMELINE_toplevel_page');
}
function TIMELINE_toplevel_page()
{
    // pression of button save changes
	if(isset($_POST[submit]))
	{
		// get value of parameter	
		$tl_blog_description =  $_POST[tl_blog_description];
		$tl_category_name_in_evidence =  $_POST[tl_category_name_in_evidence];
		$tl_type_comment =  $_POST[tl_type_comment];
		$tl_fanpage_facebook =  $_POST[tl_fanpage_facebook];	
		$tl_google_track =  $_POST[tl_google_track];		
		$tl_logo_header =  $_POST[tl_logo_header];
		$tl_logo_footer =  $_POST[tl_logo_footer];
		$tl_facebook =  $_POST[tl_facebook];
		$tl_facebook_enabled =  $_POST[tl_facebook_enabled];
		$tl_twitter =  $_POST[tl_twitter];
		$tl_twitter_enabled =  $_POST[tl_twitter_enabled];
		$tl_linkedin =  $_POST[tl_linkedin];
		$tl_linkedin_enabled =  $_POST[tl_linkedin_enabled];
		$tl_youtube =  $_POST[tl_youtube];
		$tl_youtube_enabled =  $_POST[tl_youtube_enabled];
		$tl_google_plus =  $_POST[tl_google_plus];
		$tl_google_plus_enabled =  $_POST[tl_google_plus_enabled];
		$tl_email_rss =  $_POST[tl_email_rss];
		$tl_email_rss_enabled =  $_POST[tl_email_rss_enabled];
		$tl_feed_rss =  $_POST[tl_feed_rss];
		$tl_feed_rss_enabled =  $_POST[tl_feed_rss_enabled];
		
		// save value of parameter in database
		update_option("blogdescription",$tl_blog_description);
		update_option("tl_category_name_in_evidence",$tl_category_name_in_evidence);
		update_option("tl_type_comment",$tl_type_comment);
		update_option("tl_fanpage_facebook",$tl_fanpage_facebook);
		update_option("tl_google_track",$tl_google_track);		
		update_option("tl_logo_header",$tl_logo_header);
		update_option("tl_logo_footer",$tl_logo_footer);
		update_option("tl_facebook",$tl_facebook);
		update_option("tl_facebook_enabled",$tl_facebook_enabled);
		update_option("tl_twitter",$tl_twitter);
		update_option("tl_twitter_enabled",$tl_twitter_enabled);
		update_option("tl_linkedin",$tl_linkedin);
		update_option("tl_linkedin_enabled",$tl_linkedin_enabled);
		update_option("tl_youtube",$tl_youtube);
		update_option("tl_youtube_enabled",$tl_youtube_enabled);
		update_option("tl_google_plus",$tl_google_plus);
		update_option("tl_google_plus_enabled",$tl_google_plus_enabled);
		update_option("tl_email_rss",$tl_email_rss);
		update_option("tl_email_rss_enabled",$tl_email_rss_enabled);
		update_option("tl_feed_rss",$tl_feed_rss);
		update_option("tl_feed_rss_enabled",$tl_feed_rss_enabled);
	}
 
    // set default logo if user doens't write nothing 
	if(get_option('tl_logo_header')=="") update_option('tl_logo_header',get_bloginfo('stylesheet_directory')."/images/logo.png");
	if(get_option('tl_logo_footer')=="") update_option('tl_logo_footer',get_bloginfo('stylesheet_directory')."/images/logo-footer.png");
	
	// write form of setup theme
	echo "
			<style>
			.social-ico{float:left;line-height:31px;padding-left: 9px;}
			
			.notify{
			background-color: #D1E5EE;
			border: 2px solid #7AB5CD;
			color: #444444;
			height: 50px;
			left: 266px;
			margin-top: 10px;
			padding: 10px;
			width: 900px;
			
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			
			}
			
			
			#donate{
			float: right;
			position: relative;
			text-align: right;
			top: -40px;
			width: 375px;
			}
			
			#donate p{
			float: left;
			font-weight: bold;
			text-align: left;
			width: 215px;
			
			}

		</style>
		
		<div class='wrap' style='width:960px'> 
			<div id='icon-options-general' class='icon32'></div>
			<h2>Customize Theme</h2>
			<div class='notify'><strong>Thank you for using Timeline WP.</strong><br>
For support follow we on <a target='_blank' href='http://www.facebook.com/pages/Timeline-WP/129834517116754'>Fan Page Facebook</a><br>or contact to <a href='mailto:info@timeline-wp.com'>info@timeline-wp.com</a>.


<div id='donate'>
<p>If you love timeline WP, support us to improve the project and new features!</p>

<form action='https://www.paypal.com/cgi-bin/webscr' method='post'><input type='hidden' name='cmd' value='_s-xclick'><input type='hidden' name='hosted_button_id' value='GCQQL8356S37N'><input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'><img alt='' border='0' src='https://www.paypalobjects.com/it_IT/i/scr/pixel.gif' width='1' height='1'></form></div>

</div>
			<form method='post' name='options'>
				<table class='form-table'>
					<tr>
						<td colspan='2'><h3>General Settings</h3></td>
					</tr>					
					<tr>
						<td><label>Blog description</label></td>
						<td><input id='tl_blog_description' name='tl_blog_description' class='regular-text' type='text' value='".get_option('blogdescription')."' /></td>
					</tr>
					<tr>
						<td><label>Featured Category</label></td>
						<td>
						<select name='tl_category_name_in_evidence'>";
						// make combo to know what category use for evidence
						$categories=  get_categories(); 
						foreach ($categories as $category) {
							$selected = "";
							if($category->category_nicename == get_option("tl_category_name_in_evidence"))
								$selected = " selected ";
							$option = "<option $selected value='".$category->category_nicename."'>$category->cat_name</option>"; 
							echo $option;
						}
						echo "	
						</select>
						</td>
					</tr>
					<tr>
						<td><label>Comment type</label></td>
						<td>
						<select name='tl_type_comment'>";
						// set default comment type
						if(get_option('tl_type_comment')=="") update_option('tl_type_comment','Facebook');
						// array with possibles type of comments
						$arr_comment_type=array('Facebook','Wordpress');
						// make combo
						for($i=0;$i<count($arr_comment_type);$i++){
							$selected = "";
							if($arr_comment_type[$i]==get_option('tl_type_comment')) $selected = " selected ";
							$option = "<option $selected value='".$arr_comment_type[$i]."'>".$arr_comment_type[$i]."</option>";
							echo $option;
						}
						echo "
						</select>
						</td>
					</tr>
					<tr>
						<td><label>ID Fanpage Facebook <i>(if empty, the fanpage box is hidden)</i></label></td>
						<td><input id='tl_fanpage_facebook' name='tl_fanpage_facebook' class='regular-text' type='text' value='".get_option('tl_fanpage_facebook')."' /></td>
						</td>
					</tr>
					<tr>
						<td><label>ID Google Tracking Analytics</label></td>
						<td><input id='tl_google_track' name='tl_google_track' class='regular-text' type='text' value='".get_option('tl_google_track')."' /></td>
						</td>
					</tr>
					<tr>
						<td><label>Logo header <i>(height max: 110px)</i></label></td>
						<td><img style=\"background-image:url('".get_bloginfo('stylesheet_directory')."/images/bk-img.png')\" src='".get_option('tl_logo_header')."' /><br>
						<input id='tl_logo_header' name='tl_logo_header' class='regular-text' type='text' value='".get_option('tl_logo_header')."' /></td>
						</td>
					</tr>
					<tr>
						<td><label>Logo footer</label> <i>(height max: 110px)</i></td>
						<td><img style=\"background-image:url('".get_bloginfo('stylesheet_directory')."/images/bk-img.png')\" src='".get_option('tl_logo_footer')."' /><br>
						<input id='tl_logo_footer' name='tl_logo_footer' class='regular-text' type='text' value='".get_option('tl_logo_footer')."' />
						</td>
					</tr>
					<tr>
						<td colspan='2'><h3>Social network</h3></td>
					</tr>	
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/facebook.gif' style='float:left'/>
						<label class='social-ico'>Facebook</label>
						</td>
						<td>
						<input id='tl_facebook' name='tl_facebook' class='regular-text' type='text' value='".get_option('tl_facebook')."' />
						<input type='checkbox' name='tl_facebook_enabled' id='tl_facebook_enabled' value='checked' ".get_option('tl_facebook_enabled')." />
						</td>
					</tr>
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/twitter.gif' style='float:left'/>
						<label class='social-ico'>Twitter</label></td>
						<td>
						<input id='tl_twitter' name='tl_twitter' class='regular-text' type='text' value='".get_option('tl_twitter')."' />
						<input type='checkbox' name='tl_twitter_enabled' id='tl_twitter_enabled' value='checked' ".get_option('tl_twitter_enabled')." />
						</td>
					</tr>
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/linkedin.gif' style='float:left'/>
						<label class='social-ico'>Linkedin</label></td>
						<td>
						<input id='tl_linkedin' name='tl_linkedin' class='regular-text' type='text' value='".get_option('tl_linkedin')."' />
						<input type='checkbox' name='tl_linkedin_enabled' id='tl_linkedin_enabled' value='checked' ".get_option('tl_linkedin_enabled')." />
						</td>
					</tr>
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/youtube.gif' style='float:left'/>
						<label class='social-ico'>YouTube</label></td>
						<td>
						<input id='tl_youtube' name='tl_youtube' class='regular-text' type='text' value='".get_option('tl_youtube')."' />
						<input type='checkbox' name='tl_youtube_enabled' id='tl_youtube_enabled' value='checked' ".get_option('tl_youtube_enabled')." />
						</td>
					</tr>
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/google-plus.gif' style='float:left'/>
						<label class='social-ico'>Google Plus</label></td>
						<td>
						<input id='tl_google_plus' name='tl_google_plus' class='regular-text' type='text' value='".get_option('tl_google_plus')."' />
						<input type='checkbox' name='tl_google_plus_enabled' id='tl_google_plus_enabled' value='checked' ".get_option('tl_google_plus_enabled')." />
						</td>
					</tr>
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/email-rss.gif' style='float:left'/>
						<label class='social-ico'>Email RSS</label></td>
						<td>
						<input id='tl_email_rss' name='tl_email_rss' class='regular-text' type='text' value='".get_option('tl_email_rss')."' />
						<input type='checkbox' name='tl_email_rss_enabled' id='tl_email_rss_enabled' value='checked' ".get_option('tl_email_rss_enabled')." />
						</td>
					</tr>					
					<tr>
						<td><img src='".get_bloginfo('stylesheet_directory')."/images/social-icons/feed-rss.gif' style='float:left'/>
						<label class='social-ico'>Feed RSS</label></td>
						<td>
						<input id='tl_feed_rss' name='tl_feed_rss' class='regular-text' type='text' value='".get_option('tl_feed_rss')."' />
						<input type='checkbox' name='tl_feed_rss_enabled' id='tl_feed_rss_enabled' value='checked' ".get_option('tl_feed_rss_enabled')." />
						</td>
					</tr>
				</table>				
				<p class='submit'>
					<input id='submit' name='submit' class='button-primary' type='submit' value='Save Changes' />
				</p>
			</form>
		</div>";
}
add_action('admin_menu', 'TIMELINE_add_pages');
?>