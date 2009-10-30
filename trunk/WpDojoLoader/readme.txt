=== WpDojoLoader ===
Contributors: lehmeier
Donate link: http://wpdojoloader.berlios.de/
Tags: wordpress, dojo, datagrid, accordion, scrollpane, grid, table, nested, tab, widget, fisheye
Requires at least: 2.0.2
Tested up to: 2.8.5
Stable tag: 0.0.1

== Description ==

WpDojoLoader allows you to include dojo widgets into wordpress.
At the moment you can add them into posts and pages.

The following widgets are supported at the moment:
*tabcontainer
*contentpane
*datagrid
*fisheye
*scrollpane
*accordionpane
*accrodioncontainer 

To add a widget the plugin uses a simple xml structure so that you can create nested tabs or
include a datagrid into a tab or a accordion.
You can see some examples on the plugin homepage.

== Installation ==

Unpack the plugin files int '/wp-content/plugins/'
The name of the plugindirectory must be 'wpdojoloader'


== Frequently Asked Questions ==

= How to use the plugin =

For a accordion e.g. add this lines in a post or page.
<pre><code>
[dojocontent]
	<accordioncontainer>
		<accordionpane selected=”true” title=”First One”>This is the content in the first accordion pane</accordionpane>
		<accordionpane  title=”Second One”>This is the content in the second accordion pane</accordionpane>
		<accordionpane  title=”With a post inside”><post id=”28″/></accordionpane>
	</accordioncontainer>
[/dojocontent]
</code></pre>

== Screenshots ==

1. Here you can see a accordion
2. This is a datagrid with data from a csv file
3. Nested Tabs

== Changelog ==

= 0.0.1 =
* First plugin version