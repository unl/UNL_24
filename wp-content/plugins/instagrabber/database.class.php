<?php
class Database
{
	function __construct(){

	}

	function insert_stream($args){
		global $wpdb;
		$defaults = array(
			'name' => '',
			'type' => '',
			'tag' => '',
			'access_token' => get_option('instagrabber_instagram_user_accesstoken'),
			'auto_post' => 0,
			'auto_tags' => 0,
			'post_type' => 'post',
			'post_status' => 'draft',
			'placeholder_title' => 'Instagram image',
			'created_by' => NULL,
			'image_attachment' => 'content',
			'taxonomy' => 'none',
			'tax_term' => 'none',
			'tags_tax' => 'none'
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );
		$userid = $type == 'user' ? get_option('instagrabber_instagram_user_userid') : NULL;
		$data = array(
				'name' => esc_attr($name),
				'type' => esc_attr($type),
				'tag' => esc_attr($tag),
				'access_token' => esc_attr($access_token),
				'auto_post' => esc_attr($auto_post),
				'auto_tags' => esc_attr($auto_tags),
				'post_type' => esc_attr($post_type),
				'post_status' => esc_attr($post_status),
				'placeholder_title' => esc_attr($placeholder_title),
				'created_by' => esc_attr($created_by),
				'image_attachment' => esc_attr($image_attachment),
				'taxonomy' => esc_attr($taxonomy),
				'tax_term' => esc_attr($tax_term),
				'tags_tax' => esc_attr($tags_tax)
				);

		$format = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			);

		$wpdb->insert($wpdb->prefix . 'instagrabber_streams', $data, $format);
		return $wpdb->insert_id;
	}

	function update_stream($args, $id){
		global $wpdb;
		$defaults = array(
			'name' => '',
			'type' => '',
			'tag' => '',
			'access_token' => get_option('instagrabber_instagram_user_accesstoken'),
			'auto_post' => 0,
			'auto_tags' => 0,
			'post_type' => 'post',
			'post_status' => 'draft',
			'placeholder_title' => 'Instagram image',
			'created_by' => NULL,
			'image_attachment' => 'content',
			'taxonomy' => 'none',
			'tax_term' => 'none',
			'tags_tax' => 'none'
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );

		$data = array(
				'name' => $name,
				'type' => $type,
				'tag' => $tag,
				'access_token' => $access_token,
				'auto_post' => $auto_post,
				'auto_tags' => $auto_tags,
				'post_type' => $post_type,
				'post_status' => $post_status,
				'placeholder_title' => $placeholder_title,
				'created_by' => $created_by,
				'image_attachment' => $image_attachment,
				'taxonomy' => $taxonomy,
				'tax_term' => $tax_term,
				'tags_tax' => $tags_tax
				);

		$format = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d'
			);

		$where = array(
				'id' => $id
			);
		$whereformat = array(
				'%d'
			);
		$wpdb->update($wpdb->prefix . 'instagrabber_streams', $data, $where, $format, $whereformat);
	}

	function get_streams($args = array()){
		global $wpdb;
		$defaults = array(
			'order_by' => 'name',
			'order' => 'ASC',
			'return' => OBJECT
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_streams';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];
		$streams = $wpdb->get_results("SELECT * FROM $table ORDER BY $order_by $order",$args['return']);
		return $streams;

	}

	function get_stream($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$streams = $wpdb->get_results("SELECT * FROM $table WHERE id = $id");
		return $streams[0];
	}

	function delete_stream($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$where = is_array($id) ? 'IN (' . implode(', ', $id) . ')' : '= '. $id;
		$wpdb->query( 
		$wpdb->prepare( 
			"DELETE FROM $table
			 WHERE id $where
			" 
	        )
		);

		$imgtable = $wpdb->prefix . 'instagrabber_images';
		$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM $imgtable
			 WHERE stream $where
			" 
	        )
		);
	}
	function get_access_token($stream_id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$streams = $wpdb->get_results("SELECT access_token FROM $table WHERE id = $stream_id");
		return $streams[0]->access_token;
	}
	function update_access_token($token, $userid, $stream){
		global $wpdb;

		$data = array(
				'access_token' => $token,
				'userid' => $userid
				);

		$format = array(
				'%s',
				'%d'
			);

		$where = array(
				'id' => $stream
			);
		$whereformat = array(
				'%d'
			);
		$wpdb->update($wpdb->prefix . 'instagrabber_streams', $data, $where, $format, $whereformat);
	}
	function save_images_in_database($stream_id,$images){
		global $wpdb;
		$last_id = 0;
		//$images = array_reverse($images);
		$table = $wpdb->prefix . 'instagrabber_images';
		$images = array_reverse($images);
		foreach ($images as $key => $image) {
			
			$imageindb = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE stream = $stream_id AND pic_id = '$image->id';" ) );
			
			if($imageindb)
				continue;
			
			$caption = isset($image->caption) && !empty($image->caption) ? $image->caption->text : '';
			$data = array(
					'stream'        => $stream_id,
					'pic_id'        => $image->id,
					'pic_url'       => $image->images->standard_resolution->url,
					'pic_thumbnail' => $image->images->thumbnail->url,
					'pic_link'      => $image->link,
					'pic_timestamp' => date( 'Y-m-d H:i:s', $image->created_time),
					'caption'       => $caption,
					'tags'          => serialize($image->tags),
					'comment_count' => $image->comments->count,
					'like_count'    => $image->likes->count,
					'published'     => 0,
					'media_id'      => 0,
					'user_name'		=> $image->user->username,
					'user_id'		=> $image->user->id,
				);

			$format = array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%d',
				);
			//$wpdb->hide_errors();
			$wpdb->insert($table, $data, $format);
			//$wpdb->show_errors();
			$last_id = $image->id;
		}
		
		if($last_id){
			
			$wpdb->update( 
			$wpdb->prefix . 'instagrabber_streams', 
				array( 
					'last_id' => $last_id
				), 
				array( 'id' => $stream_id ), 
				array( 
					'%s'
				), 
				array( '%d' ) 
			);
		}
		
	}

	function get_unpublished_images($stream_id,$args = array()){
		global $wpdb;
		$defaults = array(
			'order_by' => 'id',
			'order' => 'ASC',
			'return' => OBJECT
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_images';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];
		$streams = $wpdb->get_results("SELECT * FROM $table WHERE stream = $stream_id AND published = 0 ORDER BY $order_by $order",$args['return']);

		return $streams;
	}

	function update_image_to_published($id, $image_id){
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'instagrabber_images', array('published' => 1, 'media_id' => $image_id), array('id' => $id), array('%d','%d'), array('%d'));
	}

	function get_images_by_id($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';
		$where = is_array($id) ? 'IN (' . implode(', ', $id) . ')' : '= '. $id;
		$images = $wpdb->get_results("SELECT * FROM $table WHERE id ".$where." ORDER BY id DESC");
		return $images;
	}

	function get_images($stream_id, $args){
		global $wpdb;
		$defaults = array(
			'order_by' => 'id',
			'order' => 'DESC',
			'return' => OBJECT
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_images';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];
		$streams = $wpdb->get_results("SELECT * FROM $table WHERE stream = $stream_id ORDER BY $order_by $order",$args['return']);
		return $streams;
	}
}

?>