<?php
class InstagrabberApi
{

	function __construct(){

	}

	static function instagrabber_getInstagramRedirectURI($stream = false)
	{
			$stream = !$stream ? '' : '&streamid='.$stream;
			return get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action='.INSTAGRABBER_PLUGIN_CALLBACK_ACTION.$stream;
	}

	// gets Instagram login/authorization page URI
		static function instagrabber_getAuthorizationPageURI($stream = false)
		{
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			$InstagramRedirectURI = self::instagrabber_getInstagramRedirectURI($stream);
			
			if (empty($InstagramClientID) || empty($InstagramClientSecret) || empty($InstagramRedirectURI))
				return null;

			
			
			// API: http://instagr.am/developer/auth/
			return 'https://api.instagram.com/oauth/authorize/?client_id='.$InstagramClientID.'&redirect_uri='.urlencode($InstagramRedirectURI).'&response_type=code';
		}


		// handler for Integram redirect URI
		static function instagrabber_deal_with_instagram_auth_redirect_uri()
		{
			// API: http://instagr.am/developer/auth/
			global $wpdb;
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			$stream = isset($_GET['streamid']) ? $_GET['streamid'] : false;
			$InstagramRedirectURI = self::instagrabber_getInstagramRedirectURI($stream);
			
			if (empty($InstagramClientID) || empty($InstagramClientSecret) || empty($InstagramRedirectURI))
				exit;
				
			$auth_code = $_GET['code'];
			
			if (empty($auth_code))
			{
				print('<p>&nbsp;<br />There was a problem requesting the authorization code:</p>');
				
				$error = $_GET['error'];
				$error_reason = $_GET['error_reason'];
				$error_description = $_GET['error_description'];
				if (!empty($error) && !empty($error_reason) && !empty($error_description))
					print('<p><strong>'.$error_description.'</strong></p>');
				
				exit;
			}
			
			// CURL POST request for getting the user access token from the code
			$request_uri = 'https://api.instagram.com/oauth/access_token';
			
			$data = array(	'client_id' => $InstagramClientID,
							'client_secret' => $InstagramClientSecret,
							'grant_type' => 'authorization_code',
							'redirect_uri' => $InstagramRedirectURI,
							'code' => $auth_code
							);
			
			$ch = curl_init($request_uri);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			//echo curl_errno($ch);
			curl_close($ch);
			
			$decoded_response = json_decode($response);
			// print_r($decoded_response);
			// 	die();
			// get user data from the response
			$access_token = $decoded_response->access_token;
			$username = $decoded_response->user->username;
			$bio = $decoded_response->user->bio;
			$website = $decoded_response->user->website;
			$profile_picture = $decoded_response->user->profile_picture;
			//$full_name = $decoded_response->user->full_name;
			$id = $decoded_response->user->id;
			
			if (!empty($access_token))
			{
				if(!isset($_GET['streamid'])){
					update_option('instagrabber_instagram_user_accesstoken', $access_token);
					update_option('instagrabber_instagram_user_username', $username);
					update_option('instagrabber_instagram_user_userid', $id);
					update_option('instagrabber_instagram_user_profilepicture', $profile_picture);
				}else{
					Database::update_access_token($access_token, $id, $_GET['streamid']);
				}
				
				// now we reload the main page and close the popup
				//Database::insert_stream($username.' Stream', 'user', $id, $access_token);
				
				?>
				
				<script type="text/javascript">
					window.opener.location = window.opener.location;
					self.close();
				</script>
				
				<?php
			}
			else
				print('<p>There was a problem getting the required authorization!</p>');

			exit;
			
			// accessible with URL:
			// http://[HOST]/wp-admin/admin-ajax.php?action=instagrabber_redirect_uri
		}

		static function curl_file_get_contents($url)
		{
			$curl = curl_init();
			$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

			curl_setopt($curl,CURLOPT_URL,$url.'&count=2000');
			// TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
			// the number of seconds to wait while trying to connect
			curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 6); 	

			curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
			curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($curl, CURLOPT_TIMEOUT, 12);	

			$contents = curl_exec($curl);
			curl_close($curl);
			return $contents;
		}

		static function instagrabber_getInstagramUserStream($stream)
		{

			$accessToken = $stream->access_token;
			$userid = $stream->userid;

			if (empty($accessToken))
				return null;
			$lastid = $stream->last_id != NULL ? '&max_tag_id='.$stream->last_id : '';
			$file_contents = self::curl_file_get_contents('https://api.instagram.com/v1/users/'.$userid.'/media/recent/?access_token='.$accessToken.$lastid);
			

			if (empty($file_contents))
				return null;

			$photo_data = json_decode($file_contents);

			return $photo_data;
		}
		static function instagrabber_TagStream($stream)
		{
			$accessToken = $stream->access_token;
			$tag = $stream->tag;

			if (empty($accessToken))
				return null;
			$lastid = $stream->last_id != NULL ? '&max_tag_id='.$stream->last_id : '';
			$file_contents = self::curl_file_get_contents('https://api.instagram.com/v1/tags/'.$tag.'/media/recent?access_token='.$accessToken.$lastid);


			if (empty($file_contents))
				return null;

			$photo_data = json_decode($file_contents);

			return $photo_data;
		}
}
?>