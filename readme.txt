=== Plugin Name ===
Tags: twitter, sidebar, tweet, feed, feeds, local, deal, geo, news, restaurant, bar, music, concert, event, sports, plugin, plugins
Requires at least: 2.0.2
Tested up to: 2.9
Stable tag: trunk
Contributors: hoodfeed

== Description ==

The HoodFeed wordpress plugin is a sidebar widget that displays local tweets for a given geographic area.
Currently we're available in Washington, DC and New York City, with other cities coming soon.

There are several public API keys, "wdc\_public" is Washington, DC and "nyc\_public" is New York City.  To
get your own API key, email contact@hoodfeed.com with your blog name, address and general area you would
like updates for.  API keys are _**free**_ and we are very responsive, but we like to know who is using the API.

== Installation ==

1. Upload `hoodfeed.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Depending on whether you use widgets:
    (using widgets) Set up the sidebar just like a normal widget (Appearance > Widgets > Available Widges.  Specify
    your chosen API key in the setup pane.
        OR
    (no widgets) in sidebar.php of your theme folder, add `<?php hoodfeed_show_tweets('wdc_public', 'Neighborhood Tweets', 7) ?>`
    where 'wdc_public' would be your API key, 'Neighborbood Tweets' would be your title, and 7 (or any
    other number less then 10) would be the number of tweets to show.

== Frequently Asked Questions ==

= What cities do you cover? =

Currently we have data for Washington, DC and New York City.  This will be expanding soon.

= When will my city by covered? =

Where do you live?  Sending us an email is the best way to fast-track support for your city

= How do I get more info =

You can visit us at [HoodFeed.com](http://hoodfeed.com "HoodFeed")

= How do I get an API Key =

Email contact@hoodfeed.com or use "wdc\_public" or "nyc\_public".  New keys will be released "soon"

= How fast is the lookup =

Our data is cached and lookup optimized, but you can also speed things on your end by enabling the Wordpress object cache

== Screenshots ==

1. From pqliving.com, these are local tweets for the Penn Quarter neighborhood of Washington, DC
2. Closeup of the plugin in action

== Changelog ==

= 1.0.1 =
* Added new screenshot
* Cleaned up readme.txt

= 1.0 =
* Initial release