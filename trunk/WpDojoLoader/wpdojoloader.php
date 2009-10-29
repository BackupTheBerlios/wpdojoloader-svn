<?php
/* 
Plugin Name: WpDojoLoader 
Plugin URI: http://wpdojoloader.berlios.de/
Version: v0.01
Author: <a href="http://wpdojoloader.berlios.de/">Dirk Lehmeier</a>
Description: Dojo Loader Plugin
 
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

//this is the current version

require_once(dirname(__FILE__). '/dojogenerator.php');
require_once(dirname(__FILE__). '/wpdojoloader_admin.php');

if (!class_exists("WpDojoLoader")) {
	
	/**
	 * this class manages the parsing of posts and pages and inserts dojo content
	 */
	class WpDojoLoader {
		
		var $dojogenerator = null;
		var $dojocontent = "";
		//var $currentdata = "";
		var $currentdata = array();  //array containing the data inside a a xml element
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		var $iscodeelem = false; //this is set to true if a <code> element is found, no other elements will be parsed until the </code> element 
		
		function WpDojoLoader() { //constructor
			$this->dojogenerator = new DojoGenerator();	
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
		function deactivate() {
			delete_option($this->adminOptionsName);	
		}
		
		/**
		 * called from the xml parser for element data
		 * @return 
		 * @param $parser Object
		 * @param $data Object
		 */		
		function characterData($parser,$data){
			if ((trim($data) != "") && ($data != null)) {
				$this->currentdata[sizeof($this->currentdata)-1] .= $data;
			}
		}
		
		/**
		 * returns true if the elementname is a valid wpdojoloader xml element, otherwise false
		 * @return 
		 * @param $aElementname Object
		 */
		function isValidElement($aElementname) {
			if (strcmp($aElementname,"TABCONTAINER") == 0)
				return true;
			
			if (strcmp($aElementname,"CONTENTPANE") == 0)
				return true;
				
			if (strcmp($aElementname,"DATAGRID") == 0)
				return true;
				
			if (strcmp($aElementname,"POST") == 0)
				return true;
			
			if (strcmp($aElementname,"FISHEYE") == 0)
				return true;
			
			/* currently not activated it's a little bit buggy at the moment 
			if (strcmp($aElementname,"HIGHLIGHT") == 0)
				return true;
			
			if (strcmp($aElementname,"CODE") == 0)
				return true;
			*/
			
			if (strcmp($aElementname,"LINK") == 0)
				return true;
			
			if (strcmp($aElementname,"SCROLLPANE") == 0)
				return true;
				
			if (strcmp($aElementname,"WPCONTENTROOT") == 0)
				return true;	
				
			if (strcmp($aElementname,"ACCORDIONPANE") == 0)
				return true;	
				
			if (strcmp($aElementname,"ACCORDIONCONTAINER") == 0)
				return true;	
				
			if (strcmp($aElementname,"BUTTON") == 0)
				return true;
			
			if (strcmp($aElementname,"DYNAMIC") == 0)
				return true;
					
			return false;
		}

		/**
		 * parse a xml start element
		 * @return 
		 * @param $parser Object
		 * @param $name Object
		 * @param $attributes Object
		 */
		function startElement($parser, $name, $attributes) {
			array_push($this->currentdata,""); //
						
			//if a element within the wpdojoloader is not a wpdojoloader element,
			//this element will be inserted (e.g. <li> or <img> elements), includeing it's attributes
			if ((! $this->isValidElement($name)) || $this->iscodeelem) {
				$elemstr = "";
				$elemstr .= "<".$name.""; 		//insert the start element
				foreach ($attributes as $key => $value) {   //insert the attributes from the start element
				   $elemstr .= " ".$key."=\"".$value."\"";
			   	}	
				$elemstr .= ">";
				
				$this->dojocontent .= $elemstr;
				return;
			}
			
		   	switch ($name) {
			    case 'TABCONTAINER':
					$this->dojocontent .= $this->dojogenerator->getTabContainer_start($attributes['STYLE']);
					break;
				case 'CONTENTPANE':
					$this->dojocontent .= $this->dojogenerator->getContentPane_start($attributes['TITLE'],$attributes['STYLE'],$attributes['RESIZE']);
					break;
				 case 'DATAGRID':
					$this->dojocontent .= $this->dojogenerator->getDataGrid_start($attributes['STORETYPE'],$attributes['STRUCTURENAME'],$attributes['FILENAME'], $attributes['STYLE']);
					break;
				case 'POST':
					$this->dojocontent .= $this->dojogenerator->getPost_start($attributes['ID']);
					break;
				case 'FISHEYE':
					$this->dojocontent .= $this->dojogenerator->getFisheyeLite_start();
					break;
				
				/* currently not activated it's a little bit buggy at the moment  
				case 'HIGHLIGHT':
					$this->dojocontent .= $this->dojogenerator->getHighlight_start($attributes['LANG']);
					$this->iscodeelem = true; 
					break;
				case 'CODE':
					$this->dojocontent .= $this->dojogenerator->getCode_start($attributes['LANG']);
					$this->iscodeelem = true; 
					break;
				*/
				
				case 'LINK':
					$this->dojocontent .= $this->dojogenerator->getLink_start($attributes['TYPE'],$attributes['ID']);
					break;
				case 'SCROLLPANE':
					$this->dojocontent .= $this->dojogenerator->getScrollPane_start($attributes['ORIENTATION'],$attributes['STYLE']);
					break;
				case 'ACCORDIONCONTAINER':
					$this->dojocontent .= $this->dojogenerator->getAccordionContainer_start($attributes['STYLE'],$attributes['DURATION']);
					break;
				case 'ACCORDIONPANE':
					$this->dojocontent .= $this->dojogenerator->getAccordionPane_start($attributes['TITLE'],$attributes['SELECTED']);
					break;
				case 'BUTTON':
					$this->dojocontent .= $this->dojogenerator->getButton_start($attributes['FUNCTION']);
					break;
				case 'DYNAMIC':
					$this->dojocontent .= $this->dojogenerator->getDynamicPost_start();
					break;
		   	}
		}
		
		/**
		 * parse a xml close element
		 * @return 
		 * @param $parser Object
		 * @param $name Object
		 */
		function closeElement($parser, $name) {
						
			$cd = array_pop($this->currentdata); //get the data inside a element which was not parsed
			$this->dojocontent .= $cd;  		 //add to the dojocontent string
			
			if (! $this->isValidElement($name)) {
				$this->dojocontent .= "</".$name.">"; //insert the element end tag
				return;
			}
			
			/* used for code highlightning
			if (($this->iscodeelem) && ($name != "CODE")) { 
				$this->dojocontent .= "</".$name.">"; //insert the element end tag
				return;
			}
			*/
			
			switch ($name) {
			    case 'TABCONTAINER':
					$this->dojocontent .= $this->dojogenerator->getTabContainer_end();
					break;
				case 'CONTENTPANE':
					$this->dojocontent .= $this->dojogenerator->getContentPane_end();
					break;
				case 'DATAGRID':
					$this->dojocontent .= $this->dojogenerator->getDataGrid_end();
					break;
				case 'POST':
					$this->dojocontent .= $this->dojogenerator->getPost_end();
					break;
				case 'FISHEYE':
					$this->dojocontent .= $this->dojogenerator->getFisheyeLite_end();
					break;
					
				/* currently not activated it's a little bit buggy at the moment 
				case 'HIGHLIGHT':
					$this->dojocontent .= $this->dojogenerator->getHighlight_end();
					$this->iscodeelem = false; 
					break;
				case 'CODE':
					$this->dojocontent .= $this->dojogenerator->getCode_end();
					$this->iscodeelem = false;
					break;
				*/
					
				case 'LINK':
					$this->dojocontent .= $this->dojogenerator->getLink_end();
					break;
				case 'SCROLLPANE':
					$this->dojocontent .= $this->dojogenerator->getScrollPane_end();
					break;
				case 'ACCORDIONCONTAINER':
					$this->dojocontent .= $this->dojogenerator->getAccordionContainer_end();
					break;
				case 'ACCORDIONPANE':
					$this->dojocontent .= $this->dojogenerator->getAccordionPane_end();
					break;
				case 'BUTTON':
					$this->dojocontent .= $this->dojogenerator->getButton_end();
					break;
				case 'DYNAMIC':
					$this->dojocontent .= $this->dojogenerator->getDynamicPost_end();
					break;
		   }
		}
		
		
		/**
		 * parse a given xml data string
		 * @return 1 if successful otherwise 0
		 * @param $xmldata string
		 */
		function parseXML($xmldata) {
			$xml_parser = xml_parser_create();
			
			xml_set_element_handler($xml_parser, array(&$this,'startElement'),array(&$this,'closeElement'));
			xml_set_character_data_handler($xml_parser, array(&$this,"characterData"));
			
			//wrap xml document around the xmldata from the page or post
			$xd = "<?xml version=\"1.0\"?>";
			$xd .= "<wpcontentroot>";
			$xd .= $xmldata;
			$xd .= "</wpcontentroot>";
			$xd = str_replace("&lt;","<",$xd);
			$xd = str_replace("&gt;",">",$xd);
			
			
			echo "<!-- BEGIN XML".$xd." END XML -->"; //debug only
			
			$rslt = xml_parse($xml_parser, $xd);
			xml_parser_free($xml_parser);
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
				
				$this->dojocontent = "";				
				if ($this->parseXML($inner) == 1) {
					echo "<!-- BEGIN CONTENT ".$this->dojocontent." END CONTENT -->"; //debug only
					return $pre.$this->dojocontent.$suf;	
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
			
			
			/*
			$rslt = $content;
			$c1 = $this->replaceContent($rslt,$aStartTag,$aEndTag);
				if (is_string($c1)) {
					$rslt = $c1;	
				}
			*/
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
				wp_enqueue_script('tiny_mce', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/tinymce/jscripts/tiny_mce/tiny_mce.js', array('prototype'), '0.1');
				
				//load the tiny mce from wordpress
				//wp_enqueue_script('tiny_mce', get_bloginfo('wpurl') . '/wp-includes/js/tinymce/tiny_mce.js', array('prototype'), '0.1');
				//wp_enqueue_script('tiny_mce', get_bloginfo('wpurl') . '/wp-includes/js/tinymce/wp-tinymce.js', array('prototype'), '0.1');
				
				//add the dojo toolkit from ajax.googleapis.com
				wp_enqueue_script('dojo', 'http://ajax.googleapis.com/ajax/libs/dojo/1.3.1/dojo/dojo.xd.js', array('prototype'), '0.1');
				
				//add the wpdojoloader js functions
				wp_enqueue_script('wpdojoloader', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/wpdojoloader.js', array('prototype'), '0.1');				
			}
		}
		
		//hello
		function test_Trunk() {
			return "";
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
	
	//called when the plugin is deactivated => cleanup a bit
	register_deactivation_hook( __FILE__, array(&$dl_dojoLoader, 'deactivate') );
}



?>