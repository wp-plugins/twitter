<?php
/*
Plugin Name:  Widget Twitter VJCK
Plugin URI: http://www.vjcatkick.com/?page_id=5475
Description: Display twitter on your sidebar!
Version: 2.0.1
Author: V.J.Catkick
Author URI: http://www.vjcatkick.com/
*/

/*
License: GPL
Compatibility: WordPress 2.6 with Widget-plugin.

Installation:
Place the widget_single_photo folder in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright V.J.Catkick - http://www.vjcatkick.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Jan 02 2009 - v0.0.1
- Initial release
* Jan 04 2009 - v0.0.2
- now data will be cached automatically
* Jan 05 2009 - v0.1.0
- Initial public release
* Jan 05 2009 - v0.1.1
- bug fix: http://twitpic.com
* Jan 05 2009 - v0.1.2
- new support: http://movapic.com (ke-tai hyakkei)
- support overflow on IE 6
* Jan 15 2009 - v0.1.3
- bug fix: source link is now open in new window
* Jan 29 2009 - v0.1.4
- added: date-time format configuration
* Jan 03 2010 - v0.1.5
- fixed: twitpic incompatibility fixed.
- added: display image option (when loaded)
* Jan 03 2010 - v0.1.6
- fixed: url position related bug
- added: hashtag link
* Jan 14 2010 - v0.1.7
- added: brightkite supported
* Jan 15 2010 - v0.1.8
- fixed: timezone issue
* Oct 23 2012 – v0.1.9
- quick fixed: support twitter api change – 10/15/2012

* Jul 11 2013 – v2.0.0
- all new code
* Jul 11 2013 – v2.0.0\1
- zip file fix


*/

?>
<style type="text/css" >
<!--
.w_tweet_tweet_wrap {
	font-size: 0.8em;
	margin: 5px 0px 20px 0px;
	padding: 5px 0px 15px 0px;
	line-height: 1.3em;
	clear: both;
	}

.w_tweet_tweet_text {
	padding: 0px 0px 2px 0px;
	border-bottom: 1px solid #f0f0f0;
	}

.w_tweet_tweet_time_tweet {
	float: left;
	margin-top: 3px;
	font-size: 0.9em;
	color: gray;
	}

.w_tweet_tweet_source {
	float: right;
	margin-top: 3px;
	font-size: 0.9em;
	text-align: right;
	}

.w_tweet_tweet_status {
	font-size: 0.8em;
	text-align: right;
	border-top: 1px solid lightgray;
	padding: 2px 0px;
	margin-top: 1.0em;
	color: lightgray;
	}

.w_tweet_tweet_photobox {
	float: left;
	border-radius: 3px;
	overflow: hidden;
	}

.w_tweet_tweet_photobox img {
	border: 1px solid lightgray;
	margin-right: 5px;
	margin-bottom: 5px;
	}



.w_tweet_tweet_profile_wrap {
	margin: 10px 0px;
	border-bottom: 1px solid lightgray;
	}

.w_tweet_tweet_profile_icon {
	width: 48px;
	height: 48px;
	border: 1px solid lightgray;
	border-radius: 3px;
	overflow: hidden;
	float: left;
	margin-right: 8px;
	margin-bottom: 4px;
	}

.w_tweet_tweet_profile_username {
	font-size: 1.0em;
	font-weight: bold;
	}

.w_tweet_tweet_profile_location {
	font-size: 0.9em;
	margin-bottom: 4px;
	}

.w_tweet_tweet_profile_description {
	font-size: 0.9em;
	}

.w_tweet_tweet_profile_meta_wrap {
	clear: both;
	}

.w_tweet_tweet_profile_meta {
	font-size: 0.8em;
	color: gray;
	text-align: right;
	padding-bottom: 3px;
	}
-->
</style>
<?php


define( 'W_TWITTER_OPTION', 'widget_twitter_vjck_2' );
define( 'W_TWITTER_CACHE', 'widget_twitter_vjck_cache' );

/*
 * __check_php_version()
 * with library limitation, this plugin requires PHP version 5.3.0 or higher
 *
 *
 */

function __check_php_version() {
	$version = phpversion();
	$vernum = explode( '.', $version );

	if( $vernum[ 0 ] < 5 ) return false;
	else if( $vernum[ 1 ] < 3 ) return false;
	return true;
} /* __check_php_version */




function __get_twitter_access_info( $kind ) {
	$output = '';

	$options = get_option( W_TWITTER_OPTION );
	$op_consumer_key = $options[ 'op_consumer_key' ];
	$op_consumer_secret = $options[ 'op_consumer_secret' ];
	$op_access_token = $options[ 'op_access_token' ];
	$op_access_secret = $options[ 'op_access_secret' ];

	switch( $kind ) {
		case w_twitter_consumer_key:
			$output = 'Qqp7yIjWANUyfSby3avbDQ';
			$output = $op_consumer_key;
			break;
		case w_twitter_consumer_secret:
			$output = 'J3UEcXgPLLJpXCeEAEwrFZxHUXNTGx9R4BGpcSfL0';
			$output = $op_consumer_secret;
			break;
		case w_twitter_access_token:
			$output = '4972251-a8ttk9oGoD7qLhmoxiKddEi1UqUKIwuOrZSgZeGstU';
			$output = $op_access_token;
			break;
		case w_twitter_access_secret:
			$output = 'XNz05Upb5F52uHM0eldcb7Q9RLRhiPgf8OZXrgIQ8';
			$output = $op_access_secret;
			break;
	} /* switch */

	return $output;
}



function widget_twitter_vjck_init() {
	if( !function_exists( 'register_sidebar_widget' ) ) {
		return;
	} /* if */
	if( !__check_php_version() ) {
		echo '<div style="width: 100%; text-align: center; background-color: pink; padding: 10px; font-size: 1.0em;" >' . '[twitter] error: PHP 5.3.0 or later requires' . '</div>';
		return;
	} /* if */

	require 'tmhOAuthHandle.php';

	function widget_twitter_vjck( $args ) {
		extract( $args );
		$output = '';


		$options = get_option( W_TWITTER_OPTION );

		/* option default value */
		$op_widget_title = $options[ 'op_widget_title' ] ? $options[ 'op_widget_title' ] : 'Twitter';

		$op_timezone_offset = $options[ 'op_timezone_offset' ] ? $options[ 'op_timezone_offset' ] : 9;
		$op_time_format = $options[ 'op_time_format' ] ? $options[ 'op_time_format' ] : 'M j Y - G:i';
		$op_exclude_reply = $options[ 'op_exclude_reply' ] ? true : false;
		$op_max_display = $options[ 'op_max_display' ] ? $options[ 'op_max_display' ] : 10;


		$tmhOAuth = new tmhOAuthExample();
		$code = $tmhOAuth->user_request( array( 'url' => $tmhOAuth->url( '1.1/account/verify_credentials' ) ) );
		if( $code == 200 ) {
			$data = json_decode( $tmhOAuth->response[ 'response' ], true );
			if( isset( $data[ 'status' ] ) ) {
				$code = $tmhOAuth->user_request( array(	'url' => $tmhOAuth->url( '1.1/statuses/user_timeline' ),
														'user_id' => $data[ 'id' ],
														'count' => $op_max_display,
														'include_rts' => 1,
												));
				if( $code == 200 ) $tweet = json_decode( $tmhOAuth->response[ 'response' ], true );
				else $output .= '<!-- error - request user timeline: ' . $code . '-->';

				$new_tweets_cache = urlencode( json_encode( $tweet ) );
				update_option( W_TWITTER_CACHE, $new_tweets_cache );
			} /* if */
		}else{
			$output .= '<!-- error verify credentials: ' . $code . '-->';
			$tweets_cache = get_option( W_TWITTER_CACHE );
			if( $tweets_cache ) $tweet = json_decode( urldecode( $tweets_cache ), true );
		} /* if else */




		/* profile section */

		$user = $tweet[ 0 ][ 'user' ];

		$output .= '<div class="w_tweet_tweet_profile_wrap" >';

			$output .= '<div class="w_tweet_tweet_profile_icon" >';
				$output .= '<a href="https://twitter.com/' . $user[ 'screen_name' ] . '" target="_blank" >';
					$output .= '<img src="' . $user[ 'profile_image_url' ] . '" border="0" />';
				$output .= '</a>';
			$output .= '</div>';

			$output .= '<div class="w_tweet_tweet_profile_text_wrap" >';

				$output .= '<div class="w_tweet_tweet_profile_username" >';
					$output .= '<a href="https://twitter.com/' . $user[ 'screen_name' ] . '" target="_blank" >' . $user[ 'name' ] . '</a>';
				$output .= '</div>';

				$output .= '<div class="w_tweet_tweet_profile_location" >';
					$output .= $user[ 'location' ];
				$output .= '</div>';

				$output .= '<div class="w_tweet_tweet_profile_description" >';
					$output .= $user[ 'description' ];
				$output .= '</div>';

			$output .= '</div>';

			$output .= '<div class="w_tweet_tweet_profile_meta_wrap" >';

				$output .= '<div class="w_tweet_tweet_profile_meta" >';
					$output .= 'since ' . date( 'F Y', strtotime( $user[ 'created_at' ] ) ) . '<br />';
					$output .= $user[ 'followers_count' ] . ' followers, ' . $user[ 'friends_count' ] . ' friends, ' . $user[ 'statuses_count' ] . ' tweets';
				$output .= '</div>';

			$output .= '</div>';

		$output .= '</div> <!-- w_tweet_tweet_profile_wrap -->';


		/* tweets section */

		$disp_cc = $op_max_display;
		foreach( $tweet as $t ) {
			$tweet_text = $t[ 'text' ];
			if( mb_strpos( $tweet_text, '@' ) === 0 && $op_exclude_reply ) continue;

			/* embeded urls to link */
			$urls = $t[ 'entities' ][ 'urls' ];
			if( count( $urls ) > 0 ) {
				foreach( $urls as $u ) {
					$pattern = '/' . urlencode( $u[ 'url' ] ) . '/s';
					$replacement = '<a href="' . $u[ 'expanded_url' ] . '" target="_blank" >' . $u[ 'expanded_url' ] . '</a>';
					$tweet_text = preg_replace( $pattern, $replacement, urlencode( $tweet_text ) );
					$tweet_text = urldecode( $tweet_text );
				} /* foreach */
			} /* if */

			/* hashtags */
			$hashtags = $t[ 'entities' ][ 'hashtags' ];
			if( count( $hashtags ) > 0 ) {
				foreach( $hashtags as $h ) {
					$pattern = '/#' . $h[ 'text' ] . '/s';
					$url = 'https://twitter.com/search/realtime?q=%23' . urlencode( $h[ 'text' ] ) . '&src=hash';
					$replacement = '<a href="' . $url . '" target="_blank" >#' . $h[ 'text' ] . '</a>';
					$tweet_text = preg_replace( $pattern, $replacement, $tweet_text );
				} /* foreach */
			} /* if */

			/* photo */
			$media = $t[ 'entities' ][ 'media' ];
			if( count( $media ) > 0 ) {
				$disp_photo_flag = true;
				foreach( $media as $m ) {
					$pattern = '/' . urlencode( $m[ 'url' ] ) . '/s';
					$replacement = '<a href="' . $m[ 'url' ] . '" target="_blank" >' . $m[ 'display_url' ] . '</a>';
					$tweet_text = preg_replace( $pattern, $replacement, urlencode( $tweet_text ) );
					$tweet_text = urldecode( $tweet_text );

					if( $m[ 'type' ] == 'photo' && $disp_photo_flag ) {

						$img_box_max = $new_width = $new_height = 100;
						$img_width = $m[ 'sizes' ][ 'small' ][ 'w' ];
						$img_height = $m[ 'sizes' ][ 'small' ][ 'h' ];

						if( $img_width > $img_height ) {
							$new_height = floor( $img_height * $new_width / $img_width );
							$img_offset = floor( ( $img_box_max - $new_height ) / 2 );
							$img_style = ' style="margin-top:' . $img_offset . 'px;" ';
						}else if( $img_width < $img_height ) {
							$new_width = floor( $img_width * $new_height / $img_height );
							$img_offset = floor( ( $img_box_max - $new_width ) / 2 );
							$img_style = ' style="margin-left:' . $img_offset . 'px;" ';
						}else{
							$img_style = "";
						} /* if elseif else */

						$img_tag_str = '<div class="w_tweet_tweet_photobox" >';
						$img_tag_str .= '<a href="' . $m[ 'url' ] . '" target="_blank" >';
						$img_tag_str .= '<img src="' . $m[ 'media_url' ] . '" border="0" width="' . $new_width . 'px" height="' . $new_height . 'px" />';
						$img_tag_str .= '</a>';
						$img_tag_str .= '</div>';
						$endline_clear = '<br clear="all" />';
						$tweet_text = $img_tag_str . $tweet_text . $endline_clear;

						$disp_photo_flag = false;
					} /* if */

				} /* foreach */
			} /* if */

			$output .= '<div class="w_tweet_tweet_wrap" >';
				$output .= '<div class="w_tweet_tweet_text" >';
					$output .= $tweet_text;
				$output .= '</div>';

				$output .= '<div class="w_tweet_tweet_time_tweet" >';
					$local_time = strtotime( $t[ 'created_at' ] ) + ( $op_timezone_offset * 3600 );
					$output .= date( $op_time_format, $local_time );
				$output .= '</div>';

				$output .= '<div class="w_tweet_tweet_source" >';
					$output .= $t[ 'source' ];
				$output .= '</div>';

			$output .= '</div> <!-- w_tweet_tweet_wrap -->';

			if( --$disp_cc <= 0 ) break;
		} /* foreach */



		/* status section */

		$output .= '<div class="w_tweet_tweet_status" >';
			$output .= 'Twitter REST API 1.1 - ';
			if( $code == 200 ) {
				$output .= 'status OK';
			} else {
				$output .= 'status cache - #' . $code . '';
			} /* if else */
		$output .= '</div>';


		echo $before_widget . $before_title . $op_widget_title . $after_title;
		echo $output;
		echo $after_widget;
	} /* widget_twitter_vjck() */



	function widget_twitter_vjck_control() {
		$options = $newoptions = get_option( W_TWITTER_OPTION );

		if( $_POST["widget_twitter_vjck_submit"] ) {
			$newoptions[ 'op_widget_title' ]		= $_POST[ 'op_widget_title' ];
			$newoptions[ 'op_timezone_offset' ]		= (int)$_POST[ 'op_timezone_offset' ];
			$newoptions[ 'op_time_format' ]			= $_POST[ 'op_time_format' ];
			$newoptions[ 'op_exclude_reply' ]		= (boolean)$_POST[ 'op_exclude_reply' ];
			$newoptions[ 'op_max_display' ]			= (int)$_POST[ 'op_max_display' ];

			$newoptions[ 'op_consumer_key' ]		= $_POST[ 'op_consumer_key' ];
			$newoptions[ 'op_consumer_secret' ]		= $_POST[ 'op_consumer_secret' ];
			$newoptions[ 'op_access_token' ]		= $_POST[ 'op_access_token' ];
			$newoptions[ 'op_access_secret' ]		= $_POST[ 'op_access_secret' ];
		} /* if */
		if( $options != $newoptions ) {
			$options = $newoptions;
			update_option( W_TWITTER_OPTION, $options );
		} /* if */

		/* value check and defaults */
		$op_widget_title = $options[ 'op_widget_title' ] ? $options[ 'op_widget_title' ] : 'Twitter';
		$op_timezone_offset = $options[ 'op_timezone_offset' ] ? $options[ 'op_timezone_offset' ] : 9;
		$op_time_format = $options[ 'op_time_format' ] ? $options[ 'op_time_format' ] : 'M j Y - G:i';
		$op_exclude_reply = $options[ 'op_exclude_reply' ] ? true : false;
		$op_max_display = $options[ 'op_max_display' ] ? $options[ 'op_max_display' ] : 10;

		$op_consumer_key = $options[ 'op_consumer_key' ];
		$op_consumer_secret = $options[ 'op_consumer_secret' ];
		$op_access_token = $options[ 'op_access_token' ];
		$op_access_secret = $options[ 'op_access_secret' ];

?>
		<p><?php _e('Widget Title:'); ?> <input style="width: 220px;" id="op_widget_title" name="op_widget_title" type="text" value="<?php echo $op_widget_title; ?>" /></p>
		<p><?php _e('Timezone Offset: (hours)'); ?> <input style="width: 220px;" id="op_timezone_offset" name="op_timezone_offset" type="text" value="<?php echo $op_timezone_offset; ?>"</p>
		<p><?php _e('Date time format:'); ?> <input style="width: 220px;" id="op_time_format" name="op_time_format" type="text" value="<?php echo $op_time_format; ?>" /></p>
		<p><input id="op_exclude_reply" name="op_exclude_reply" type="checkbox" value="1" <?php if( $op_exclude_reply ) echo 'checked';?>/> <?php _e('Exclude reply'); ?></p>
		<p><?php _e('Number of display: (max 20)'); ?> <input style="width: 220px;" id="op_max_display" name="op_max_display" type="text" value="<?php echo $op_max_display; ?>" /></p>
		<p>---</p>

		<p><?php _e('Consumer Key:'); ?> <input style="width: 220px;" id="op_consumer_key" name="op_consumer_key" type="text" value="<?php echo $op_consumer_key; ?>" /></p>
		<p><?php _e('Consumer Secret:'); ?> <input style="width: 220px;" id="op_consumer_secret" name="op_consumer_secret" type="text" value="<?php echo $op_consumer_secret; ?>" /></p>
		<p><?php _e('Access Token:'); ?> <input style="width: 220px;" id="op_access_token" name="op_access_token" type="text" value="<?php echo $op_access_token; ?>" /></p>
		<p><?php _e('Access Secret:'); ?> <input style="width: 220px;" id="op_access_secret" name="op_access_secret" type="text" value="<?php echo $op_access_secret; ?>" /></p>

  	    <input type="hidden" id="widget_twitter_vjck_submit" name="widget_twitter_vjck_submit" value="1" />


<?php
	} /* widget_twitter_vjck_control() */




	register_sidebar_widget( 'Twitter VJCK', 'widget_twitter_vjck' );
	register_widget_control( 'Twitter VJCK', 'widget_twitter_vjck_control' );
} /* widget_twitter_vjck_init() */

add_action( 'plugins_loaded', 'widget_twitter_vjck_init' );











?>