=== WpDojoLoader ===
Contributors: lehmeier
Donate link: http://wpdojoloader.berlios.de/
Tags: wordpress, dojo, datagrid, accordion, scrollpane, grid, table, nested, titlepane, tab, widget, fisheye, xml, xsl,jquery,jquery-ui,content management,cms,templates,ajax
Requires at least: 2.8.2
Tested up to: 3.0.0
Stable tag: 0.0.50

== Description ==

WpDojoLoader allows you to include widgets or simple html templates into wordpress.
You are also able to import content from external xml files which is directly displayed
in your posts. So you have some simple content management methods for wordpress.
Another new feature is Ajaxloading of posts. Check the wpdojoloader homepage for examples.

At the moment you can add widgets into posts and pages. And the plugin also provides a sub-plugin to add content
into the wordpress sidebar.

The following (dojo) widgets are supported at the moment:

* tabcontainer
* bordercontainer
* contentpane
* datagrid
* fisheye
* scrollpane
* accordionpane
* accrodioncontainer
* titlepane

New are the JQuery Widgets (at the monent only 2)

* JQUery Accordion
* JQuery Tabs

To add a widget the plugin uses a simple xml structure so that you can create nested tabs or
include a datagrid into a tab or a accordion.
You can see some examples on the plugin homepage.

Since 0.0.4 the xml structure is translated with xsl so that it is very easy to extend.

== Installation ==

Unpack the plugin files int '/wp-content/plugins/'
The directoryname of the plugin must be 'wpdojoloader'


== Frequently Asked Questions ==

= Where do i get a documentation =
http://wpdojoloader.berlios.de

= How to use the plugin =
Vistit http://wpdojoloader.berlios.de there is a getting started tutorial.

= Does it work with a version less then 2.8.2 =
Maybe, i haven't tested it. So if it works, let me know about that.

= I am getting a "xml cannot be parsed" error. How can i fix this? =
You probably have copied and pasted from the example pages.
Then it's possible that the quote characters are wrong. 

== Screenshots ==

1. Here you can see a accordion
2. jQuery Tabs
3. Nested Tabs
4. Editor with plugin

== Changelog ==

= 0.0.50 =

* support for xml templates
* jquery-ui support
* import xml content and xml templates
* changed syntax for accordion and tabcontainer
* ajax loading support
* support for the jquery scrollTo plugin
* xmlstore support for the datagrid

= 0.0.42 =
* commented out a debug line

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