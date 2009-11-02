=== Plugin Name ===
Tags: twitter, sidebar, tweet, feed, feeds, local, deal, geo, news, restaurant, bar, music, concert, event, sports, plugin, plugins
Requires at least: 2.0.2
Tested up to: 2.9
Stable tag: trunk

== Description ==

The HoodFeed wordpress plugin is a sidebar widget that displays local tweets for a given geographic area.
There are several public API keys, "wdc_public" is Washington, DC and "nyc_public" is New York City.  To
get your own API key, email contact@hoodfeed.com with your blog name, address and general area you would
like updates for.  API keys are free and we are very responsive, but we like to know who is using the API
so we can keep our loads even.

== Installation ==

1. Upload `hoodfeed.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Depending on whether you use widges:
    (using widgets) Set up the sidebar just like a normal widget (Appearance > Widgets > Available Widges.  Specify
    your chosen API key in the setup pane.
        OR
    (no widgets) in sidebar.php of your theme folder, add <?php hoodfeed_show_tweets('wdc_public', 'Neighborhood Tweets', 7) ?>
    where 'wdc_public' would be your API key, 'Neighborbood Tweets' would be your title, and 7 (or any
    other number less then 10) would be the number of tweets to show.

== Frequently Asked Questions ==

= How do I get more info =

You can visit us at [HoodFeed.com][http://hoodfeed.com]

= How do I get an API Key =

Email contact@hoodfeed.com

= How fast is the lookup =

Our data is cached and lookup optimized, but you can also speed things on your end by enabling the Wordpress object cache

== Screenshots ==

1. From pqliving.com, these are local tweets for the Penn Quarter neighborhood of Washington, DC

== Changelog ==

= 1.0 =
* Initial release