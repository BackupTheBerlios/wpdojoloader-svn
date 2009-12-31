=== WpDojoLoader ===
Contributors: lehmeier
Donate link: http://wpdojoloader.berlios.de/
Tags: wordpress, dojo, datagrid, accordion, scrollpane, grid, table, nested, titlepane, tab, widget, fisheye, xml, xsl
Requires at least: 2.8.2
Tested up to: 2.8.5
Stable tag: 0.0.41

== Description ==

WpDojoLoader allows you to include dojo widgets into wordpress.
At the moment you can add them into posts and pages.

The following widgets are supported at the moment:

* tabcontainer
* bordercontainer
* contentpane
* datagrid
* fisheye
* scrollpane
* accordionpane
* accrodioncontainer
* titlepane

To add a widget the plugin uses a simple xml structure so that you can create nested tabs or
include a datagrid into a tab or a accordion.
You can see some examples on the plugin homepage.

Since 0.0.4 the xml structure is translated with xsl so that it is very easy to extend.

== Installation ==

Unpack the plugin files int '/wp-content/plugins/'
The directoryname of the plugin must be 'wpdojoloader'


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

= Does it work with a version less then 2.8.2 =
Maybe, i haven't tested it. So if it works, let me know about that.

= I am getting a ‘xml cannot be parsed’ error. How can i fix this? =
You probably have copied and pasted from the example pages.
Then it's possible that the quote characters are wrong. 

== Screenshots ==

1. Here you can see a accordion
2. This is a datagrid with data from a csv file
3. Nested Tabs
4. Editor with plugin

== Changelog ==

= 0.0.41 =
* fixed a bug with a foreach warning 
* added a dojo titlepane widget

= 0.0.4 =
* switched to dojo 1.4.0
* xml to html translation now with xsl (check the wpdojoloader.xsl)

= 0.0.3 =
* added a plugin for the editor in wordpress so that you can easy insert xml templates for the widgets

= 0.0.2 =

* fixed a bug, the "activate dojoloader = yes" option was not saved in the options when the plugin was activated the first time
* added a dojo bordercontainer
* added a box container with optional fadein and fadeout animation
* added a rounded corner css class

= 0.0.1 =
* First plugin version