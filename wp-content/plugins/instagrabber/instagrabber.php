<?php 
/*
Plugin Name: Wordpress Instagrabber 
Plugin URI: http://johan-ahlback.com/plugins/instagrabber
Description: Import your instagrams photos as a post, display them in a widget or embed the link in post
Version: 1.1
Author: Johan Ahlbäck
Author URI: http://johan-ahlback.com
*/

/**
 * Copyright (c) `20-08-2011` Your Name. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

register_activation_hook(__FILE__,'instagrabber_install');

function instagrabber_install() {
   global $wpdb;

   $table_name = $wpdb->prefix . "instagrabber_streams";
      
   $sql = "CREATE TABLE $table_name (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name text,
  type text,
  userid bigint(20) DEFAULT NULL,
  tag text,
  access_token text,
  auto_post tinyint(1) DEFAULT NULL,
  auto_tags tinyint(1) DEFAULT NULL,
  post_type text,
  post_status text,
  last_id text,
  image_attachment text,
  created_by int(11) DEFAULT '1',
  placeholder_title text NOT NULL,
  taxonomy text,
  tax_term int(11) DEFAULT NULL,
  tags_tax text,
  PRIMARY KEY (id)
);";
	

	$table_name_images = $wpdb->prefix . "instagrabber_images";
      
   $sql .= "CREATE TABLE $table_name_images (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  pic_id varchar(256) NOT NULL DEFAULT '',
  pic_url varchar(256) DEFAULT NULL,
  pic_thumbnail text,
  pic_link varchar(256) DEFAULT NULL,
  pic_timestamp datetime DEFAULT NULL,
  time_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  caption text,
  tags text,
  comment_count int(11) DEFAULT NULL,
  like_count int(11) DEFAULT NULL,
  published tinyint(1) DEFAULT '0',
  media_id bigint(20) DEFAULT NULL,
  stream int(11) DEFAULT NULL,
  user_name text,
  user_id text,
  UNIQUE KEY id (id)
);";
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
}

require_once('database.class.php');
require_once('instagrabber-api.class.php');
require_once('list_stream.class.php');
require_once('list_streams.class.php');
// plugin base URLs and prefixes
define('INSTAGRABBER_PLUGIN_CALLBACK_ACTION', 'instagrabber_redirect_uri');

// Instagram base URLs
define('INSTAGRABBER_DEVELOPER_URL', 'http://instagram.com/developer/');

	Class Instagrabber
	{
		public $pluginfolder = '';

		function __construct(){
			//set plugin folder
			$this->pluginfolder = plugin_dir_url( __FILE__ );

			//add pages
			add_action('admin_menu', array( $this, 'admin_menu' ) );
			
			// Instagrabber auth
			$api = new InstagrabberApi;
			
			add_action('wp_ajax_instagrabber_redirect_uri', array($api, 'instagrabber_deal_with_instagram_auth_redirect_uri') );

			//save options
			add_action('admin_init', array( $this, 'save_client_info' ));
			add_action('admin_init', array($this, 'save_stream'));
			add_action('admin_init', array($this, 'delete_stream'));

			add_action('admin_init', array($this, 'post_images'), 25);
			
			// wp_cron
			add_action('instagrabber_scheduled_post_creation_event', array( $this, 'instagrabber_automatic_post_creation'));

			// Add wp_cron intervals
			add_filter('cron_schedules',array( $this, 'instagrabber_cron_definer'));

			//add scripts
			add_action('admin_enqueue_scripts', array($this, 'my_scripts_method'));

			//add stream javascript
			add_action('wp_ajax_instagrabber_load_taxonomies', array($this, 'get_categories') );
			
			add_action('wp_ajax_instagrabber_load_terms', array($this, 'get_categories_terms') );

			add_action('wp_ajax_instagrabber_load_tags', array($this, 'get_tags') );
		}

		/**
		 * Add scripts
		 */
		function my_scripts_method() {
		    wp_deregister_script( 'intagrabber_stream' );
		    wp_register_script( 'intagrabber_stream', $this->pluginfolder.'js/instagrabber_stream.js', array('jquery'));
		    
		}    

		/**
		 * Add pages
		 */
		function admin_menu(){
			$page = add_menu_page( 'Instagrabber', 'Instagrabber', 'edit_published_posts', 'instagrabber', array($this, 'admin_page'), $this->pluginfolder.'icon.png' );

			add_submenu_page( 'instagrabber', 'New Stream', 'Add stream', 'edit_published_posts', 'instagram-add', array($this, 'admin_stream_page') );

			add_submenu_page( 'instagrabber', 'Instagrabber settings', 'Settings', 'manage_options', 'instagram-settings', array($this, 'admin_settings_page') );
		}

		/**
		 * Display Streams or a single stream if $_GET['stream'] is set
		 */
		function admin_page(){
		
			global $current_user;

			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');

			$current_user = wp_get_current_user();
			
			?>
				<div class="wrap">
					<div id="icon-options-general" class="icon32"><br></div>
					<h2  class="">Instagrabber Streams</h2>
					<?php if (!isset($_GET['stream'])):
						//List streams
						//Create an instance of our package class...
					    $testListTable = new List_Streams();
					    //Fetch, prepare, sort, and filter our data...
					    $testListTable->prepare_items();
					?>
						<form id="" method="get" action="">
			            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
			            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			            <!-- Now we can render the completed list table -->
			            <?php $testListTable->display() ?>
			        </form>
			    	<?php else:
			    		$this->import_images($_GET['stream']);
			    		//Create an instance of our package class...
					    $testListTable = new List_Stream();
					    //Fetch, prepare, sort, and filter our data...
					    $testListTable->prepare_items();
			    	?>
				    	<?php 
				    	// check if Authorize is done
				    	if (!Database::get_access_token($_GET['stream'])): ?>
					    	<p>You must Authorize this stream</p>
					    	<input type="button" value="Authorization" id="authsubmit" >
					    	<script type="text/javascript">
									var InstagramAuthWindow = null;
									
									jQuery(document).ready(function() {

										jQuery('authform').attr('action', '');
										
										jQuery('#authsubmit').click(function() {
											
											InstagramAuthWindow = window.open('<?php print(InstagrabberApi::instagrabber_getAuthorizationPageURI($_GET["stream"])); ?>', 'InstagramAuthorization', 'width=800,height=400');

										});
									});
								</script>
				    	<?php else: ?>
				    	<?php $stream = Database::get_stream($_GET['stream']);
				    			// Do not show streams to other users
				    			if($stream->created_by != $current_user->ID && !current_user_can('manage_options'))
				    				wp_die('You do not have sufficient permissions to access this page.', 'You do not have sufficient permissions to access this page.');
				    	?>
				    		<form id="movies-filter" method="get">
					            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
					            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']?>" />
					            <input type="hidden" name="stream" value="<?php echo $_REQUEST['stream']?>" />
					            <!-- Now we can render the completed list table -->
					            <?php $testListTable->display() ?>
					        </form>
				    	<?php endif ?>
			    		
					<?php endif ?>
					
				</div>
			<?php
		}

		/**
		 * Get taxonomies for ajax
		 */
		function get_categories($post_type = 'post', $old = 'none'){
			
			if(isset($_GET['type']))
				$post_type = $_GET['type'];

			$taxonomies = get_object_taxonomies($post_type,'objects');
			?>
			<select name="taxonomy" id="taxonomy">
				<option value="none">None</option>
			<?php foreach ($taxonomies as $key => $tax): 
				if($tax->hierarchical != 1)
					continue;
			?>
				<option value="<?php echo $key ?>" <?php selected($key, $old) ?>><?php echo $key ?></option>
			<?php endforeach ?>
			</select>
			<?php
			if(isset($_GET['type']))
				die();
			
		}

		/**
		 * Get terms
		 * TODO: Use built in categories dropdown.
		 */
		function get_categories_terms($taxonomy = 'category', $old = 'none'){

			if(isset($_GET['tax']))
				$taxonomy = $_GET['tax'];

			if ($taxonomy != 'none') {				
				$terms = get_terms( $taxonomy, array('hide_empty' => 0) );
				?>
				<select name="terms" id="terms">
					<option value="none">None</option>
				<?php foreach ($terms as $key => $term): ?>
					<option value="<?php echo $term->term_id ?>" <?php selected($term->term_id, $old) ?>><?php echo $term->name ?></option>
				<?php endforeach ?>
				</select>
				<?php
			
			}
			if(isset($_GET['tax']))
				die();
		}

		/**
		 * Get taxonomies with tag functionallity
		 */
		function get_tags($post_type = 'post', $old = 'none'){
			if(isset($_GET['type']))
				$post_type = $_GET['type'];

			$taxonomies = get_object_taxonomies($post_type,'objects');
			?>
			<select name="taxonomy_tag" id="taxonomy_tag">
				<option value="none">None</option>
			<?php foreach ($taxonomies as $key => $tax): 
				if($tax->hierarchical != 0 || $key == 'post_format' )
					continue;
			?>
				<option value="<?php echo $key ?>" <?php selected($key, $old) ?>><?php echo $key ?></option>
			<?php endforeach ?>
			</select>
			<?php
			if(isset($_GET['type']))
				die();
		}

		/**
		 * Create/edit stream page
		 */
		function admin_stream_page(){
			wp_enqueue_script( 'intagrabber_stream' );
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			?>
				<div class="wrap">
					<div id="icon-options-general" class="icon32"><br></div>
					<h2  class="">Instagrabber Add Stream</h2>
					<?php if (!$InstagramClientID || !$InstagramClientSecret): ?>
						<p>You must configure this plugin. Go to settings page</p>
					<?php else: ?>
						<?php
							$name = false;
							$placeholder = false;
							$user = get_current_user_id();
							$image_attachment = false;
							$type = 'tag';
							$tag = false;
							$createpost = 0;
							$post_type = 'post';
							$status = false;
							$autotag = 0;
							$taxonomy = 'category';
							$term = 'none';
							$tag_tax = 'post_tag';

							if (isset($_GET['stream'])) {
								$stream = Database::get_stream($_GET['stream']);
								if ($stream) {
									$name = $stream->name;
									$placeholder = $stream->placeholder_title;
									$user = $stream->created_by;
									$image_attachment = $stream->image_attachment;
									$type = $stream->type;
									$tag = $stream->tag;
									$createpost = $stream->auto_post;
									$post_type = $stream->post_type;
									$status = $stream->post_status;
									$autotag = $stream->auto_tags;
									$taxonomy = $stream->taxonomy;
									$term = $stream->tax_term;
									$tag_tax = $stream->tags_tax;
								}
							}
						?>
						<form id="authform" method="post" action="admin.php?page=instagram-add">
							<input type="hidden" name="action" value="instagrabber_new_stream">
							<input type="hidden" name="instagram-add-streams" value="instagram-add-streams">
							<?php if (isset($_GET['stream'])): ?>
							<input type="hidden" name="instagram-update-stream" value="<?php echo $_GET['stream']; ?>">
							<?php endif; ?>
							<input type="hidden" name="instagram-user" value="<?php echo $user ?>">
							<table class="form-table">
							<tbody>
							
									<tr valign="top">
										<th scope="row">
											<label for="name">Name: </label>
										</th>
										<td>
											<input type="text" class="regular-text" name="name" id="name" value="<?php echo $name ?>">
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="placeholder">Placeholder title: <em>(When posting an image)</em></label>
										</th>
										<td>
											<input type="text" class="regular-text" name="placeholder" id="placeholder" value="<?php echo $placeholder ?>"><br>
											<p>You can use %user% to get the username for the image</p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="type">Type: </label>
										</th>
										<td>
											<select name="type" id="type">
												<option value="tag" <?php selected('tag', $type) ?>>Tag</option>
												<option value="user" <?php selected('user', $type) ?>>User</option>
											</select>
										</td>
									</tr>
									
									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="tag">Tag: <em>(Only if type is tag)</em></label>
										</th>
										<td>
											<input type="text" class="regular-text" name="tag" id="tag" value="<?php echo $tag ?>">
										</td>
									</tr>

									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="createpost">Auto create post: </label>
										</th>
										<td>
											<input type="radio" value="true" name="createpost" <?php checked(1, $createpost) ?>> Yes
											<br>
											<input type="radio" value="false" name="createpost" <?php checked(0, $createpost) ?>> No
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="post_type">Post Type: </label>
										</th>
										<td id="post_type_container">
											<select name="post_type" id="post_type">
												<?php foreach ($this->post_types() as $key => $value): ?>
													<option value="<?php echo $value ?>" <?php selected($value, $post_type) ?>><?php echo $value ?></option>
												<?php endforeach ?>
											</select>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="status">Post status: </label>
										</th>
										<td>
											<select name="status" id="status">
												<option value="draft" <?php selected('draft', $status) ?>>Draft</option>
												<option value="published" <?php selected('published', $status) ?>>Publish</option>
											</select>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="taxonomy">Taxonomy: </label>
										</th>
										<td id="taxonomy_container">
											<?php $this->get_categories($post_type, $taxonomy); ?>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="term">Term: </label>
										</th>
										<td id="taxonomy_term_container">
											<?php $this->get_categories_terms($taxonomy, $term); ?>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="taxonomy_tag">Taxonomy for tags: </label>
										</th>
										<td id="taxonomy_tag_container">
											<?php $this->get_tags($post_type, $tag_tax); ?>
										</td>
									</tr>

									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="autotag">Auto set tag: <em>(Uses the tags from instagram)</em></label>
										</th>
										<td>
											<input type="radio" value="true" name="autotag" <?php checked(1, $autotag) ?>> Yes
											<br>
											<input type="radio" value="false" name="autotag" <?php checked(0, $autotag) ?>> No
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="image_attachment">Attachment type: <em>(How to attach the image to the post)</em></label>
										</th>
										<td>
											<select name="image_attachment" id="image_attachment">
												<option value="content" <?php selected('content', $image_attachment) ?>>Content</option>
												<option value="featured" <?php selected('featured', $image_attachment) ?>>Featured</option>
												<option value="both" <?php selected('both', $image_attachment) ?>>Both</option>
											</select>
										</td>
									</tr>

															
							</tbody>
						</table>
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button-primary" value="Save">
								<?php if ($InstagramClientID && $InstagramClientSecret): ?>
									<input type="button" value="Authorization" id="authsubmit" >
								<?php endif ?>
							</p>
						</form>
					<?php endif ?>
				</div>
			<?php
		}


		
		function admin_settings_page(){
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			$scheduled_publication_period = get_option('instagrabber_instagram_app_scheduled');
			?>
				<div class="wrap">
					<div id="icon-options-general" class="icon32"><br></div>
					<h2  class="">Instagrabber Settings</h2>
					<h3>Instagram configuration</h3>
					<p><strong>You can set-up an Instagram application here: <a href="<?php echo INSTAGRABBER_DEVELOPER_URL; ?>" target="_blank"><?php echo INSTAGRABBER_DEVELOPER_URL; ?></a></strong></p>
					<p>Use this URL as <em>Callback/Redirect URL</em>, when registering <em>Instagram application</em>: <strong><?php echo InstagrabberApi::instagrabber_getInstagramRedirectURI(); ?></strong></p>
					<p><strong>
						Do not forget to authorize this plugin. Because then you do not have to do it every time you create a stream. (But you have to do it on user streams)
					</strong></p>
					
					<form id="authform" method="post" action="admin.php?page=instagram-settings">
					<input type="hidden" name="action" value="instagrabber_auth">
					<input type="hidden" name="clientauthinfo" value="clientauthinfo">
					<table class="form-table">
					<tbody>
					<tr valign="top">
							<tr valign="top">
								<th scope="row">
									<label for="clientid">Instagram <em>Client ID</em></label>
								</th>
								<td>
									<input type="text" class="regular-text" name="clientid" id="clientid" value="<?php echo  $InstagramClientID ?>">
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="clientsecret">Instagram <em>Client Secret</em></label>
								</th>
								<td>
									<input type="text" class="regular-text" name="clientsecret" id="clientsecret" value="<?php echo  $InstagramClientSecret ?>">
								</td>
							</tr>
					</tr>
					<tr>
							<th scope="row">
								<label>Auto get photos <em>(And publish depending on stream setting)</em></label>
							</th>
							<td>
								<select name="scheduled">
									<option value="never"<?php if ($scheduled_publication_period === 'never') echo ' selected=selected'; ?>>never</option>
									<option value="instagrabber_oneminute"<?php if ($scheduled_publication_period === 'instagrabber_oneminute') echo ' selected=selected'; ?>>every minute</option>
                                    <option value="instagrabber_fiveminutes"<?php if ($scheduled_publication_period === 'instagrabber_fiveminutes') echo ' selected=selected'; ?>>every 5 minutes</option>
                                    <option value="instagrabber_tenminutes"<?php if ($scheduled_publication_period === 'instagrabber_tenminutes') echo ' selected=selected'; ?>>every 10 minutes</option>
                                    <option value="instagrabber_twentynminutes"<?php if ($scheduled_publication_period === 'instagrabber_twentynminutes') echo ' selected=selected'; ?>>every 20 minutes</option>
                                    <option value="instagrabber_twicehourly"<?php if ($scheduled_publication_period === 'instagrabber_twicehourly') echo ' selected=selected'; ?>>every 30 minutes</option>
                                    <option value="hourly"<?php if ($scheduled_publication_period === 'hourly') echo ' selected=selected'; ?>>hourly</option>
                                    <option value="twicedaily"<?php if ($scheduled_publication_period === 'twicedaily') echo ' selected=selected'; ?>>twice a day</option>
                                    <option value="daily"<?php if ($scheduled_publication_period === 'daily') echo ' selected=selected'; ?>>daily</option>
                                    <option value="instagrabber_weekly"<?php if ($scheduled_publication_period === 'instagrabber_weekly') echo ' selected=selected'; ?>>weekly</option>
                                    <option value="instagrabber_monthly"<?php if ($scheduled_publication_period === 'instagrabber_monthly') echo ' selected=selected'; ?>>monthly</option>
                                </select>
							</td>
						</tr>
					</tbody>
				</table>
					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button-primary" value="Save">
						<?php if ($InstagramClientID && $InstagramClientSecret): ?>
							<input type="button" value="Authorization" id="authsubmit" >
						<?php endif ?>
					</p>
				</form>
				<script type="text/javascript">
								var InstagramAuthWindow = null;
								
								jQuery(document).ready(function() {

									jQuery('authform').attr('action', '');
									
									jQuery('#authsubmit').click(function() {
										
										InstagramAuthWindow = window.open('<?php print(InstagrabberApi::instagrabber_getAuthorizationPageURI()); ?>', 'InstagramAuthorization', 'width=800,height=400');

									});
								});
							</script>							
				</div>
			<?php
		}

		

		function save_client_info(){
			if(!isset($_POST['clientauthinfo']))
				return;

			update_option('instagrabber_instagram_app_id', esc_attr($_POST['clientid']));
			update_option('instagrabber_instagram_app_secret', esc_attr($_POST['clientsecret']));

			if($_POST['scheduled'] != get_option('instagrabber_instagram_app_scheduled')){
				$this->instagrabber_remove_scheduled_event();
				
				if($_POST['scheduled'] != 'never')
					$this->instagrabber_schedule_event($_POST['scheduled']);

				update_option('instagrabber_instagram_app_scheduled', $_POST['scheduled']);
			}
			wp_redirect(admin_url( 'admin.php' ).'?page=instagram-settings');
			die();
		}


		function save_stream(){
			if(!isset($_POST['instagram-add-streams']))
				return;
			$autopost = esc_attr($_POST['createpost']) == 'true' ? 1 : 0;
			$autotags = esc_attr($_POST['autotag']) == 'true' ? 1 : 0;
			
			$taxonomy = esc_attr($_POST['taxonomy']);
			$term = $taxonomy == 'none' ? 'none' : esc_attr($_POST['terms']);
			$tag_tax = esc_attr($_POST['taxonomy_tag']);
			
			$args = array(
				'name' => esc_attr($_POST['name']),
				'type' => esc_attr($_POST['type']),
				'tag' => esc_attr($_POST['tag']),
				'auto_post' => $autopost,
				'auto_tags' => $autotags,
				'post_type' => esc_attr($_POST['post_type']),
				'post_status' => esc_attr($_POST['status']),
				'placeholder_title' => esc_attr($_POST['placeholder']),
				'created_by' => esc_attr($_POST['instagram-user']),
				'image_attachment' => esc_attr($_POST['image_attachment']),
				'taxonomy' => $taxonomy,
				'tax_term' => $term,
				'tags_tax' => $tag_tax
			);
			// echo "<pre>";
			// print_r($args);
			// echo "</pre>";
			// die();

			
			if(isset($_POST['instagram-update-stream'])){
				$args['access_token'] = Database::get_access_token($_POST['instagram-update-stream']);
				Database::update_stream($args, $_POST['instagram-update-stream']);
				$id = $_POST['instagram-update-stream'];
			}else{

				if($args['type'] == 'user')
					$args['access_token'] = '';

				$id = Database::insert_stream($args);
			}
			
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber&stream='.$id);
			die();
		}

		function delete_stream(){
			if (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'instagrabber-delete-stream')
				return false;

			Database::delete_stream($_REQUEST['streamid']);
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber');
			die();
		}

		

		// task scheduling - custom time periods
		function instagrabber_cron_definer($schedules)
		{
			// 1 minute
			$schedules['instagrabber_oneminute'] = array(
				'interval'=> 60,
				'display'=> __('Once Every Minute')
		  	);

			// 5 minutes
			$schedules['instagrabber_fiveminutes'] = array(
				'interval'=> 300,
				'display'=> __('Once Every 5 Minutes')
		  	);

			// 10 minutes
			$schedules['instagrabber_tenminutes'] = array(
				'interval'=> 600,
				'display'=> __('Once Every 10 Minutes')
		  	);

		  	// 20 minutes
			$schedules['instagrabber_twentynminutes'] = array(
				'interval'=> 1200,
				'display'=> __('Once Every 20 Minutes')
		  	);

			// 30 minutes
			$schedules['instagrabber_twicehourly'] = array(
				'interval'=> 1800,
				'display'=> __('Once Every 30 Minutes')
		  	);

		  	// 'hourly', 'twicedaily', 'daily' already defined in WordPress

			// weekly
			$schedules['instagrabber_weekly'] = array(
				'interval'=> 604800,
				'display'=> __('Once Every 7 Days')
		  	);

			// monthly
			$schedules['instagrabber_monthly'] = array(
				'interval'=> 2592000,
				'display'=> __('Once Every 30 Days')
		  	);	

			return $schedules;
		}



		function instagrabber_schedule_event($period)
		{
			if ($period == 'instagrabber_oneminute' ||
				$period == 'instagrabber_fiveminutes' ||
				$period == 'instagrabber_tenminutes' ||
				$period == 'instagrabber_twentynminutes' ||
				$period == 'instagrabber_twicehourly' ||
				$period == 'hourly' ||
				$period == 'twicedaily' ||
				$period == 'daily' ||
				$period == 'instagrabber_weekly' ||
				$period == 'instagrabber_monthly')

				wp_schedule_event(current_time('timestamp'), $period, 'instagrabber_scheduled_post_creation_event' );
		}
		function instagrabber_remove_scheduled_event()
		{
			wp_clear_scheduled_hook( 'instagrabber_scheduled_post_creation_event' );
		}

		function post_types(){
			$types = get_post_types( array('capability_type' => 'post', 'public' => true), 'names');
			foreach ($types as $key => $value) {
				if($value == 'attachment')
					unset($types[$key]);
			}
			return $types;
		}

		function import_images($stream){
			
			if(!isset($stream->id))
				$stream = Database::get_stream($stream);
			
			if ($stream->type == 'user') {
				$data = InstagrabberApi::instagrabber_getInstagramUserStream($stream);
			}elseif($stream->type == 'tag'){
				$data = InstagrabberApi::instagrabber_TagStream($stream);
			}else{
				return false;
			}

			if ($data) {
				Database::save_images_in_database($stream->id,$data->data);

			}else{
				return false;
			}

			if($stream->auto_post == 1){
				
				$images = Database::get_unpublished_images($stream->id);
				if(!empty($images)){
					$this->create_post($images);
				}
				
			}

		}

		function post_images(){

			if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'post-instagrammer')
				return;
			$image = $_REQUEST['image'];
			
			if (!is_array($image))
				$image = array($image);

			$this->create_post($image);
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber&stream='.$_REQUEST['stream']);
			die();
		}

		function create_post($images){

			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

			if(!isset($images[0]->pic_id))
				$images = Database::get_images_by_id($images);


			$stream = '';
			$last_stream = NULL;
			foreach ($images as $key => $img) {
				//error_log('posting image ' . $img->id);
				if ($img->stream != $last_stream) {

					$stream = Database::get_stream($img->stream);
					$last_stream = $img->stream;
				}

			$title_placeholder = $stream->placeholder_title;

			$category_for_post = $stream->tax_term;

			if (empty($category_for_post))
			{
					$category_for_post = 'none';
			}


			// a. if the category corresponding to the Instagram search tags
			// doesn't exist, we create it
			// $cat_id = category_exists($category_for_post);
			// 	if (!$cat_id)
			// 		$cat_id = wp_create_category($category_for_post);
			
			
			// b. post creation
			$created_post_status = $stream->post_status;
			if ($created_post_status != 'published')
				$created_post_status = 'draft';

			$insert_photo_mode = $stream->image_attachment;

			$post_args = array(
				'post_author' 	=> $stream->created_by,		// with 0, current post author id is used
				//'post_category'	=> array($cat_id),
				'post_content' 	=> $img->caption,
				'post_status'	=> 'draft', 
				'post_title'	=> str_replace(
									array(
										'%user%',
										'%tag%'
									),
									array(
									 	$img->user_name,
									 	$stream->tag
									),
									$title_placeholder),
				'post_type'		=> $stream->post_type 
			);

			
			
			// INSERT checks about correct format...

			$created_post_ID = wp_insert_post($post_args);
			if($stream->taxonomy != 'none' || $stream->tax_term != 'none')
				wp_set_post_terms($created_post_ID, array($stream->tax_term), $stream->taxonomy, false);
			// add comma separated tags to post, if specified
			$tag_to_add_to_post = $stream->auto_tags;
			if ($tag_to_add_to_post){
				$tags = unserialize($img->tags);
				wp_set_post_terms($created_post_ID, implode(', ', $tags), $stream->tags_tax, true);
			}


			// c. add Instagram pic metadata to the just created post
			update_post_meta($created_post_ID, '_instagrabber_insta_id', $img->pic_id);
			update_post_meta($created_post_ID, '_instagrabber_insta_link', $img->pic_link);

			if(isset($img->user_name))
				update_post_meta($created_post_ID, '_instagrabber_insta_authorusername', $img->user_name);

			if(isset($img->user_id))
				update_post_meta($created_post_ID, '_instagrabber_insta_authorid', $img->user_id);	
			
			
			// d. download image from Instagram and associate to po

				// if we the image is already inside the media library, we get it from there, without actually downloading it from Instagram
			$image_info = null;
			if ($img && $img->media_id)
			{
				$image_info = wp_get_attachment_image_src($photo_data->media_id, 'full');
			}

			if (!$image_info)
			{

				$tmp = download_url($img->pic_url);
			    $file_array = array(
			        'name' => basename($img->pic_url),
			        'tmp_name' => $tmp
			    );

			    if (is_wp_error($tmp))
				{
					//error_log(print_r($tmp, true));
					@unlink($file_array['tmp_name']);

					// delete just created post
			    	wp_delete_post($created_post_ID, true);
					//error_log('FAILED: tmp');
					return;
			    }

			    $attach_id = media_handle_sideload($file_array, $created_post_ID);
			    	// see: what in case the same media file is added to multiple posts (as post content, but also as featured image)
			    	// http://core.trac.wordpress.org/browser/tags/3.3.2/wp-admin/includes/media.php (media_handle_sideload() code)
			    	// http://www.trovster.com/blog/2011/07/wordpress-custom-file-upload

			    if (is_wp_error($attach_id))
				{
					//error_log(print_r($attach_id, true));
					// remove uploaded temporary file
			        @unlink($file_array['tmp_name']);

			        // delete just created post
			        wp_delete_post($created_post_ID, true);
			        //error_log('FAILED: attach_id');
					return;
			    }
				
				@unlink($file_array['tmp_name']);
			}
			else
				$attach_id = $photo_data->media_id;


			// update Instagram photo local data (better doing it as soon as we are sure we have the image, so
			// if next scheduled event occurs and the same image is present, it is less likely to be added again)
			Database::update_image_to_published($img->id, $attach_id);
				// SEE: another solution could be: update the status as soon as we get the image id and, only in case
				// there are problems getting the image and put it inside the media library, we set the status back to
				// non-published

			
			if ($insert_photo_mode === 'featured' || $insert_photo_mode === 'both')
			{
				// attach to image as featured image (post thumbnail)
				add_post_meta($created_post_ID, '_thumbnail_id', $attach_id, true);
			}
			if ($insert_photo_mode === 'content' || $insert_photo_mode === 'both'){

				if (!$image_info)
					$image_info = wp_get_attachment_image_src($attach_id, 'full');

				// insert the image inside the post, followed by post caption
				$update_post_data = array();
		  		$update_post_data['ID'] = $created_post_ID;
		  		$update_post_data['post_content'] = '<a href="'.$img->pic_link.'"><img src="'.$image_info[0].'" alt="'.esc_attr(strip_tags($img->caption)).'" width="'.$image_info[1].'" height="'.$image_info[2].'"/></a><br/>'.
		  											$img->caption;

		  		wp_update_post($update_post_data);
			}

			// the post is always created as draft and, if after post creation the image could actually be added and settings say the
			// post must be directly published, it is moved from 'draft' to 'published'
			if ($created_post_status == 'published')
			{
				$update_post_data = array();
		  		$update_post_data['ID'] = $created_post_ID;
		  		$update_post_data['post_status'] = 'publish';
		  		wp_update_post($update_post_data);
			}

				//error_log('DONE posting image ' . $img->id);
			}
		}

		function instagrabber_automatic_post_creation(){
			//check if this function is already running
			if (get_option($instagrabber_auto_post_creation) == true)
				return;

			//Mark as running
			update_option('instagrabber_auto_post_creation', true);

			//get streams
			$streams = Database::get_streams();
			foreach ($streams as $key => $stream) {
				$this->import_images($stream);
			}

			//Mark as done
			update_option('instagrabber_auto_post_creation', false);
		}


	}// class Instagrabber

	$instagrabber = new Instagrabber();
 ?>