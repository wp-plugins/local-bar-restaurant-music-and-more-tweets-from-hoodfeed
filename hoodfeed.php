<?php
/*
Plugin Name: Hood Feed Local Tweets
Plugin URI: http://wordpress.org/#
Description: Displays recent tweets by local businesses in a sidebar. To define a geographic bounds, first get an API key
Author: Mike Waud (contact@hoodfeed.com)
Version: 1.0
Author URI: http://hoodfeed.com/
*/
// based off Wickett Twitter Widget

if ( !function_exists('wpcom_time_since') ) :
/*
 * from WordPress.com
 */

function wpcom_time_since( $original, $do_more = 0 ) {
        // array of time period chunks
        $chunks = array(
                array(60 * 60 * 24 * 365 , 'year'),
                array(60 * 60 * 24 * 30 , 'month'),
                array(60 * 60 * 24 * 7, 'week'),
                array(60 * 60 * 24 , 'day'),
                array(60 * 60 , 'hour'),
                array(60 , 'minute'),
        );

        $today = time();
        $since = $today - $original;

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
                $seconds = $chunks[$i][0];
                $name = $chunks[$i][1];

                if (($count = floor($since / $seconds)) != 0)
                        break;
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

        if ($i + 1 < $j) {
                $seconds2 = $chunks[$i + 1][0];
                $name2 = $chunks[$i + 1][1];

                // add second item if it's greater than 0
                if ( (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) && $do_more )
                        $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
        return $print;
}
endif;

class Hoodfeed_Widget extends WP_Widget {
    var $max_rpp = 10;
    var $cache_lifetime = 300;
    var $cache_fail_lifetime = 300;
    var $default_rpp = 7;

    function Hoodfeed_Widget() {
        $widget_ops = array(
            'classname' => 'hoodfeed',
            'description' => __( "Display tweets from local businesses")
        );
        $this->WP_Widget('hoodfeed-widget', __('Hoodfeed'), $widget_ops);
    }

    function widget( $args, $instance ) {
		extract( $args );
                
		$api_key = urlencode( $instance['api_key'] );
		if ( empty($api_key) ) return;
		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = __( 'Local Tweets' );
		$rpp = absint( $instance['rpp'] );  // # of Updates to rpp

		$before_tweet = esc_html($instance['beforetweet']);

		echo "{$before_widget}{$before_title}<a href='" . clean_url( "http://hoodfeed.com" ) . "'>{$title}</a>{$after_title}";
                
		if ( !$tweets = wp_cache_get( 'widget-hoodfeed-' . $this->number , 'widget' ) ) {
                    $url = "http://hoodfeed.com/api/search/basic.json?";
                    $url .= 'api_key='.$api_key;
                    $url .= '&rpp='.$rpp;
                    $hoodfeed_wp_api_json_url = clean_url( $url, null, 'raw' );
                    $response = wp_remote_get( $hoodfeed_wp_api_json_url, array( 'User-Agent' => 'Hoodfeed Widget' ) );

			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 == $response_code ) {
				$raw_tweets = wp_remote_retrieve_body($response);
                                $tweets = json_decode($raw_tweets);
				$expire = $this->cache_lifetime;
				if ( !is_array( $tweets ) || isset( $tweets['error'] ) ) {
					$tweets = 'error';
					$expire = $this->cache_fail_lifetime;
				}
			} else {
				$tweets = 'error';
				$expire = $this->cache_fail_lifetime;
				wp_cache_add( 'widget-hoodfeed-response-code-' . $this->number, $response_code, 'widget', $expire);
			}

			wp_cache_add( 'widget-hoodfeed-' . $this->number, $tweets, 'widget', $expire );
		}

		if ( 'error' != $tweets ) {
			echo "<ul class='tweets'>\n";

			foreach ( (array) $tweets as $tweet ) {

				if ( empty( $tweet->text ))
					continue;

				$text = make_clickable(wp_specialchars($tweet->text));
				$text = preg_replace_callback('/(^|\s)@(\w+)/', array($this, '_widget_twitter_username'), $text);
				$text = preg_replace_callback('/(^|\s)#(\w+)/', array($this, '_widget_twitter_hashtag'), $text);

                                $link_title = urlencode($tweet->business_title);

                                $time = wpcom_time_since(strtotime($tweet->tweeted_at)).' ago';
				echo "<li>";
                                echo '<strong><a href="http://hoodfeed.com/?b_id='.$tweet->b_id.'&title='.$link_title.'">'.$tweet->business_title.'</a></strong><br/>';
                                echo $before_tweet . $text . $before_timesince;
                                echo '<p align="right"><a href="http://hoodfeed.com/?t_id='.$tweet->id.'">'.$time.'</a></p>';
                                echo "</li>\n";
				$tweets_out++;
			}
                        echo '<li><a href="http://hoodfeed.com">More local tweets @ hoodfeed.com</a></li>';
			echo "</ul>\n";
                } else {
			if ( 401 == wp_cache_get( 'widget-hoodfeed-response-code-' . $this->number , 'widget' ) )
				echo "<p>" . __("Error: Please make sure your hoodfeed api_key is correct.") . "</p>";
			else
				echo "<p>" . __("Error: Hoodfeed did not respond. Please wait a few minutes and refresh this page.") . "</p>";
                }
                
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['api_key'] = strip_tags(stripslashes($new_instance['api_key']));
		$instance['api_key'] = str_replace('http://twitter.com/', '', $instance['api_key']);
		$instance['api_key'] = str_replace('/', '', $instance['api_key']);
		$instance['api_key'] = str_replace('@', '', $instance['api_key']);
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['rpp'] = absint($new_instance['rpp']);

		wp_cache_delete( 'widget-hoodfeed-' . $this->number , 'widget' );
		wp_cache_delete( 'widget-hoodfeed-response-code-' . $this->number, 'widget' );

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, 
                    array(
                        'api_key' => '',
                        'title' => '',
                        'rpp' => $this->default_rpp,
                    ));

		$api_key = esc_attr($instance['api_key']);
		$title = esc_attr($instance['title']);
		$rpp = absint($instance['rpp']);
		if ( $rpp < 1 || $this->max_rpp < $rpp )
			$rpp = $this->default_rpp;

		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:') . '
		<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />
		</label></p>
		<p><label for="' . $this->get_field_id('api_key') . '">' . __('API Key:') . '
		<input class="widefat" id="' . $this->get_field_id('api_key') . '" name="' . $this->get_field_name('api_key') . '" type="text" value="' . $api_key . '" />
		</label></p>
		<p><label for="' . $this->get_field_id('rpp') . '">' . __('Maximum number of tweets to show:') . '
			<select id="' . $this->get_field_id('rpp') . '" name="' . $this->get_field_name('rpp') . '">';

		for ( $i = 1; $i <= $this->max_rpp; ++$i )
			echo "<option value='$i' " . ( $rpp == $i ? "selected='selected'" : '' ) . ">$i</option>";

		echo '		</select>
		</label></p>';
	}

	function _widget_twitter_username( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]@<a href='" . clean_url( 'http://twitter.com/' . urlencode( $matches[2] ) ) . "'>$matches[2]</a>";
	}

	function _widget_twitter_hashtag( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]<a href='" . clean_url( 'http://search.twitter.com/search?q=%23' . urlencode( $matches[2] ) ) . "'>#$matches[2]</a>";
	}
}

/**
 * 
 * @param string $api_key Your api_key to access the HoodFeed API for a set geographic area
 * @param string $title Text above the tweet list
 * @param int $rpp Number of tweets to show
 */
function hoodfeed_show_tweets($api_key, $title = 'Neighborhood Tweets', $rpp = 7) {
    $args = array(
//        'before_widget' => '<li id="hoodfeed-widget-0" class="widget hoodfeed">',
//        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
        'widget_id' => 'hoodfeed-widget-0'
    );
    $instance = array(
        'api_key' => $api_key,
        'title' => $title,
        'rpp' => $rpp
    );
    $widget = new Hoodfeed_Widget;
    $widget->widget($args, $instance);
}


add_action("widgets_init", "hoodfeed_widget_init");
function hoodfeed_widget_init() {
    register_widget('Hoodfeed_Widget');
}



?>