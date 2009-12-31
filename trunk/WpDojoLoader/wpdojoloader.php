<?php
/* 
Plugin Name: WpDojoLoader
Plugin URI: http://wpdojoloader.berlios.de/
Description: WpDojoloader allows you to include dojo widgets into wordpress
Version: 0.0.41
Author: Dirk Lehmeier
Author URI: http://wpdojoloader.berlios.de/
 
	Copyright 2009  Dirk Lehmeier

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

require_once(dirname(__FILE__). '/wpdojoloader_admin.php');

if (PHP_VERSION>='5') {
	require_once(dirname(__FILE__).'/domxml-php4-to-php5.php'); 	//Load the PHP5 converter
 	require_once(dirname(__FILE__).'/xslt-php4-to-php5.php'); 		//Load the PHP5 converter 
}

if (!class_exists("WpDojoLoader")) {
	
	/**
	 * this class manages the parsing of posts and pages and inserts dojo content
	 */
	class WpDojoLoader {
		
		/******************************
		 * 
		 * BEGIN Config Section
		 * 
		 ******************************/
		
		//javascript libraries
		var $loadTinyMce = false;     	//load TinyMCE
		var $loadLocalDojo = false;   	//load a local version of dojo instead of google
		var $loadOpenLayers = false;  	//load the openlayers api, used for some custom widgets
		var $loadOpenStreetMap = false;	//load the openstreetmap api, used for some custom widgets
		
		//general options
		var $customLoaderEnabled = false; //if this is set to true, the custom loader is enabled which contains 
										  //some other none dojo elements
		
		/*****************************
		 * 
		 * END Config Section
		 * 
		 *****************************/
		
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		var $iscodeelem = false; //this is set to true if a <code> element is found, no other elements will be parsed until the </code> element 
		
		function WpDojoLoader() { //constructor
			//nothing to be done at the moment
		}
		
		/**
		 * returns true if the the plugin is activated in the dojoloader settings, otherwise false
		 * (NOT on the plugin page)
		 * @return boolean
		 */
		function isActive() {	
			$adminoptions = get_option($this->adminOptionsName);
			$dojoLoaderAdminOptions = array();
			
			if (!empty($adminoptions)) {
				foreach ($adminoptions as $key => $option)
					$dojoLoaderAdminOptions[$key] = $option;
			}	
			if ($dojoLoaderAdminOptions["activate"] == "true") {
				return true;
			}
			return false;
		}
		
		/**
		 * this function is called when the plugin will be deactivated in the plugin section
		 * the stored options will be deleted
		 * @return 
		 */
		function deactivatePlugin() {
			delete_option($this->adminOptionsName);	
		}
		
		/**
		 * this function is called when the plugin is activated in the plugin section
		 * @return 
		 */
		function activatePlugin() {
			$dojoLoaderAdminOptions = array(
				'activate' => 'true',
				'gridstructure' => array(array('name' => 'gridstructure_1', 'structure' => 'name,link'))
				);
								
			update_option($this->adminOptionsName, $dojoLoaderAdminOptions);
		}
		
		/**
		 * activate TinyMce Plugin
		 * @return 
		 */
		function activateTinyMcePluginButtons() {
			
			if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		    	return;
		 
		   	// Add only in Rich Editor mode
		   	if ( get_user_option('rich_editing') == 'true') {
				add_filter('mce_external_plugins', array(&$this,'add_myplugin_tinymce_plugin'));
		     	add_filter('mce_buttons', array(&$this, 'register_myplugin_button'));
			}
		}
		
		/***
		 * register a tiny mce button for the plugin
		 * @return 
		 * @param $buttons Object
		 */
		function register_myplugin_button($buttons) {
			array_push($buttons, "|", "wpdojoloader_plugin");
		   	return $buttons;
		}
		 
		/**
		 * load the tinymce plugin
		 * @return 
		 * @param $plugin_array Object
		 */ 
		function add_myplugin_tinymce_plugin($plugin_array) {
			$plugin_array['wpdojoloader_plugin'] = get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/tinymce/editor_plugin.js';
		   	return $plugin_array;
		}
		
		/**
		 * returns all linked wordpress posts as xml element
		 * @return 
		 * @param $xpathcontext Object
		 */
		function getPostElements($xpathcontext,$domdocument) {
			$result = array();
			
			$obj = $xpathcontext->xpath_eval('//pos22t/@id'); // get all post elements with attribute id
			if ($obj) {
				$nodeset = $obj->nodeset;
				if ($nodeset != null) {
					foreach ($nodeset as $node) {
						$pstid = $node->value;
						$pst = get_post($pstid);
						if ($pst != null) {
							$cnt = $this->executeParse($pst->post_content,"[dojocontent]","[/dojocontent]");
							$node = $domdocument->create_element( 'postcontent' );
							$attr = $domdocument->create_attribute  ( "id"  , "$pstid"  );
		  					$cdata = $domdocument->create_cdata_section( $cnt );
							$node->append_child( $attr );
		  					$node->append_child( $cdata );
							array_push($result, $node);	
						}
					}
				}
			}
		
			if (count($result) > 0)		
				return $result;
				
			return null;
		}
		
		
		/**
		 * returns all linked wordpress pages as xml element
		 * @return 
		 * @param $xpathcontext Object
		 */
		function getPageElements($xpathcontext,$domdocument) {
			$result = array();
			
			$obj = $xpathcontext->xpath_eval('//page/@id'); // get all post elements with attribute id
			if ($obj) {
				$nodeset = $obj->nodeset;
				if ($nodeset != null) {
					foreach ($nodeset as $node) {
						$pstid = $node->value;
						$pst = get_post($pstid);
						if ($pst != null) {
							$cnt = $this->executeParse($pst->post_content,"[dojocontent]","[/dojocontent]");
							
							$node = $domdocument->create_element( 'pagecontent' );
							$attr = $domdocument->create_attribute  ( "id"  , "$pstid"  );
		  					$cdata = $domdocument->create_cdata_section( $cnt );
							$node->append_child( $attr );
		  					$node->append_child( $cdata );
							array_push($result, $node);	
						}
					}
				}
			}
		
			if (count($result) > 0)		
				return $result;
				
			return null;
		}
		
		
		/**
		 * returns some options as xml element
		 * @return 
		 * @param $xpathcontext Object
		 * @param $domdocument Object
		 */
		function getOptionElements($xpathcontext,$domdocument) {
			$result = array();
			
			//add the wpurl			
			$node = $domdocument->create_element( 'option' );
			$attr = $domdocument->create_attribute  ( "name"  , "wpurl"  );
			$text = $domdocument->create_text_node( get_bloginfo("wpurl") );
			$node->append_child( $attr );
			$node->append_child( $text );
			
			array_push($result, $node);	
			
			if (count($result) > 0)		
				return $result;
				
			return null;	
		}
		
		
		/**
		 * get the main content element from the xml document
		 * @return 
		 * @param $xpathcontext Object
		 */
		function getContentElement($xpathcontext) {
			$node = $xpathcontext->xpath_eval('/wpcontentroot'); //get content element
			return $node->nodeset[0];
		}
		
		/**
		 * returns a array with the adminoptions
		 * @return array
		 */
		function getAdminOptions() {	
			$adminoptions = get_option($this->adminOptionsName);
			$dojoLoaderAdminOptions = array();
			
			if (!empty($adminoptions)) {
				foreach ($adminoptions as $key => $option)
					$dojoLoaderAdminOptions[$key] = $option;
			}				
			return $dojoLoaderAdminOptions;
		}
		
		/**
		 * returns a comma seperated string with the fieldnames from the given structure
		 * grid structures are stored in the admin options
		 * @return 
		 * @param $aStructurename Object
		 */
		function getFieldnames($aStructurename) {
			$options = $this->getAdminOptions();
			
			for ($i=0;$i<count($options['gridstructure']);$i++) {
				$gs_name = $options['gridstructure'][$i]['name'];
				$gs_value = $options['gridstructure'][$i]['structure'];
				if (strtolower($gs_name) == strtolower($aStructurename)) {
					return $gs_value; 
				}
			}			
			return "";
		}
		
		/**
		 * adds the fieldnames from the wordpress plugin options to the datagrid elements
		 * @return 
		 * @param $xpathcontext Object
		 */
		function replaceGridStructures($xpathcontext) {
			$obj = $xpathcontext->xpath_eval('//datagrid'); // get all post elements with attribute id
			if ($obj) {
				$nodeset = $obj->nodeset;
				if ($nodeset != null) {
					foreach ($nodeset as $node) {
						$fields = $this->getFieldnames($node->get_attribute("structurename"));
						$node->set_attribute("fieldnames",$fields);				
					}
				}
			}	
		}
		
		/**
		 * enriches the xmlstring with linked posts, pages and gridstructures
		 * @return 
		 */
		function enrichXmlString($xmlstring) {
			$dom   = domxml_open_mem($xmlstring);
			$xpath = $dom->xpath_new_context();
			
			$elem_content = $this->getContentElement($xpath);
			if ($elem_content != null) {
				$elem_posts = $dom->create_element("posts");
				$elem_pages = $dom->create_element("pages");
				$elem_options = $dom->create_element("options");
				$elem_content->append_child($elem_posts);
				$elem_content->append_child($elem_pages);
				$elem_content->append_child($elem_options);
				
				//add post elements		
				$posts = $this->getPostElements($xpath,$dom);
				if ($posts != null) {
					foreach ($posts as $pst) {
						$elem_posts->append_child($pst);	
					}	
				}
				
				//add page elements
				$pages = $this->getPageElements($xpath,$dom);
				if ($pages != null) {
					foreach ($pages as $pg) {
						$elem_pages->append_child($pg);	
					}	
				}
				
				//add options elements
				$options = $this->getOptionElements($xpath,$dom);
				if ($options != null) {
					foreach ($options as $opt) {
						$elem_options->append_child($opt);	
					}	
				}
				
				$this->replaceGridStructures($xpath);
			}
			return $dom->dump_mem(true);
		}
		
		
		/**
		 * 
		 * @return 
		 * @param $xmlstring Object
		 */
		function xml_translate($xmlstring) {
			$arguments = array(
     			'/_xml' => $xmlstring
			);
			$xh = xslt_create();
			
			$result = xslt_process($xh, 'arg:/_xml', 'wp-content/plugins/wpdojoloader/wpdojoloader.xsl', NULL, $arguments);
			if ($result) {
				return $result;	
			} else {
				return null;
			}
			
			xslt_free($xh);
		}
		
		
		/**
		 * parse a given xml raw data string
		 * @return 1 if successful otherwise 0
		 * @param $xmldata string
		 */
		function parseXML($xmldata) {
						
			//wrap xml document around the xmldata from the page or post
			$xd = "<?xml version=\"1.0\"?>";
			$xd .= "<wpcontentroot>";
			$xd .= $xmldata;
			$xd .= "</wpcontentroot>";
			$xd = str_replace("&lt;","<",$xd);
			$xd = str_replace("&gt;",">",$xd);
			
			$xd = $this->enrichXmlString($xd);
			
			//echo "<!-- BEGIN XML".$xd." END XML -->"; //debug only
			
			$rslt = ($this->xml_translate($xd));
			return $rslt;
		}
				
						
		/**
		 * replaces the content between a given start and end tag
		 * @return the new content if start and endtag were found, otherwise false
		 * @param $aContent Object
		 * @param $aStartTag Object
		 * @param $aEndTag Object
		 */
		function replaceContent($aContent, $aStartTag, $aEndTag) {
			$p1 = strpos($aContent,$aStartTag);  //first occurence of the start tag
			$p2 = strpos($aContent,$aEndTag);	 //first occurence of the end tag 
			$rslt = "";
			
			//has starttag and andtag
			if (is_int($p1) && (is_int($p2)) ) {
				$pre = substr($aContent,0,$p1);
				$suf = substr($aContent,$p2 + strlen($aEndTag),strlen($aContent) - $p1);
				$inner = substr($aContent,$p1 + strlen($aStartTag), ($p2 - strlen($aStartTag)) - ($p1));
							
				$htmldata = $this->parseXML($inner); 
				if ($htmldata != null) {
					//echo "<!-- BEGIN CONTENT ".$htmldata." END CONTENT -->"; //debug only	
					return $pre.$htmldata.$suf;
				} else {
					return $pre."<i>error parsing the xml structure</i>".$suf;
				}
			}
			return false;
		}
		
		
		/**
		 * 
		 * @return 
		 * @param $content Object
		 * @param $aStartTag Object
		 * @param $aEndTag Object
		 */
		function executeParse($content, $aStartTag, $aEndTag) {			
			$rslt = $content;
			do {
				$c1 = $this->replaceContent($rslt,$aStartTag,$aEndTag);
				if (is_string($c1)) {
					$rslt = $c1;	
				}
				
			} while (is_string($c1));

			return $rslt;
		}
		
		/**
		 * parse given content and replaces data between [dojocontent] [/dojocontent] tags with dojo elements
		 * @return 
		 * @param $content Object
		 */
		function parseContent($content) {
			$rslt = $content;
			$rslt = $this->executeParse($rslt,"[dojocontent]","[/dojocontent]");
			return $rslt;
		}
		
		/**
		 * adds javascript functions
		 * @return 
		 */
		function addHeaderCode() {
			//add some css files
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/themes/tundra/tundra.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/grid/resources/Grid.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/grid/resources/tundraGrid.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/highlight/resources/highlight.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/layout/resources/ResizeHandle.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/layout/resources/ScrollPane.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/resources/dojo.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/wpdojoloader.css" />' . "\n";
			
			if (function_exists('wp_enqueue_script')) {
				//load jquery
				wp_enqueue_script('jquery');
				
				//load a custom tiny mce				
				if ($this->loadTinyMce) {
					wp_enqueue_script('tiny_mce', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/tinymce/jscripts/tiny_mce/tiny_mce.js', array('prototype'), '0.1');
				}
				
				//load the tiny mce from wordpress
				//wp_enqueue_script('tiny_mce', get_bloginfo('wpurl') . '/wp-includes/js/tinymce/tiny_mce.js', array('prototype'), '0.1');
				//wp_enqueue_script('tiny_mce', get_bloginfo('wpurl') . '/wp-includes/js/tinymce/wp-tinymce.js', array('prototype'), '0.1');
				
				//load openlayers api
				if ($this->loadOpenLayers) {			
					wp_enqueue_script('openlayers', 'http://openlayers.org/api/OpenLayers.js', array('prototype'), '0.1');
				}
				
				//load openstreetmap api
				if ($this->loadOpenStreetMap) {
					wp_enqueue_script('openstreetmap', 'http://www.openstreetmap.org/openlayers/OpenStreetMap.js', array('prototype'), '0.1');
				}
				
				//add local dojo version, e.g. if you have custom dojo widgets
				if ($this->loadLocalDojo) {
					wp_enqueue_script('dojo', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/dojo/dojo/dojo.js', array('prototype'), '0.1');
				} else {
					//add the dojo toolkit from ajax.googleapis.com
					
					//version 1.3.1
					//wp_enqueue_script('dojo', 'http://ajax.googleapis.com/ajax/libs/dojo/1.3.1/dojo/dojo.xd.js', array('prototype'), '0.1');
					wp_enqueue_script('dojo', 'http://ajax.googleapis.com/ajax/libs/dojo/1.4/dojo/dojo.xd.js', array('prototype'), '0.1');
				}
				
				
				//wp_enqueue_script('osmdatamanager', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/trm/Application.js', array('prototype'), '0.1');
				
				//add the wpdojoloader js functions
				wp_enqueue_script('wpdojoloader', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/wpdojoloader.js', array('prototype'), '0.1');				
			}
		}
				
		/**
		 * filter content
		 * @return 
		 * @param $content Object[optional]
		 */
		function addContent($content = '') {
			$rslt = "";
			$rslt .= $this->parseContent($content); 
			return $rslt;	
		}
	}
}

//Initialize the WpDojoLoader class
if (class_exists("WpDojoLoader")) {
	$dl_dojoLoader = new WpDojoLoader();
}

//Initialize the WpDojoLoader_AdminLoader class
if (class_exists("WpDojoLoader_AdminLoader")) {
	$dl_adminLoader = new WpDojoLoader_AdminLoader();
}

//Initialize the admin and users panel
if (!function_exists("WpDojoLoader_showAdmin")) {
	function WpDojoLoader_showAdmin() {
		global $dl_adminLoader;
		
		if (!isset($dl_adminLoader)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('Dojo Loader', 'Dojo Loader', 9, basename(__FILE__), array(&$dl_adminLoader, 'printAdminPage'));
		}
	}	
}

//Actions and Filters	
if (isset($dl_dojoLoader)) {
	//test
	add_action('admin_menu', 'WpDojoLoader_showAdmin');
	if ($dl_dojoLoader->isActive()) {
		//Actions
		add_action('wp_print_scripts',array(&$dl_dojoLoader, 'addHeaderCode'), 1);
				
		//Filters
		add_filter('the_content', array(&$dl_dojoLoader, 'addContent'),1); 
	}
	
	// init tinymce plugin
	add_action('init', array(&$dl_dojoLoader, 'activateTinyMcePluginButtons') );

	//called when the plugin is activated
	register_activation_hook( __FILE__, array(&$dl_dojoLoader, 'activatePlugin') );
	
	//called when the plugin is deactivated => cleanup a bit
	register_deactivation_hook( __FILE__, array(&$dl_dojoLoader, 'deactivatePlugin') );
}



?>