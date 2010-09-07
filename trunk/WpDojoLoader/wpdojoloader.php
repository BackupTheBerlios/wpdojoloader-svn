<?php
/* 
Plugin Name: WpDojoLoader
Plugin URI: http://wpdojoloader.berlios.de/
Description: WpDojoloader allows you to include dojo widgets into wordpress
Version: 0.0.50
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
require_once(dirname(__FILE__). '/config.php');

if (PHP_VERSION>='5') {
	require_once(dirname(__FILE__).'/domxml-php4-to-php5.php'); 	//Load the PHP5 converter
 	require_once(dirname(__FILE__).'/xslt-php4-to-php5.php'); 		//Load the PHP5 converter 
}

if (!class_exists("WpDojoLoader")) {
	
	session_start();
	
	/**
	 * this class manages the parsing of posts and pages and inserts dojo content
	 */
	class WpDojoLoader {
		
		/*
		 * Config Variables -> don't change here, use the config.php
		 */
				
		//javascript libraries
		var $loadTinyMce = false;     	//load TinyMCE
		var $loadLocalDojo = false;   	//load a local version of dojo instead of google
		var $loadOpenLayers = false;  	//load the openlayers api, used for some custom widgets
		var $loadOpenStreetMap = false;	//load the openstreetmap api, used for some custom widgets
		var $loadjqueryui = false;  //load jqueryui
		
		//general options
		var $customLoaderEnabled = false; //if this is set to true, the custom loader is enabled which contains some other none dojo elements  //TODO deprecated 
		var $import_wpdtemplate  = true;  //auto import the wpd_template.xml template file
		var $import_wpddata      = true;  //auto import the wpd_data.xml content file								  
		
		var $debugmode = false;      //if this is set to true dojoloader makes some debug output -> use only for debugging
		var $addcontenttags = true;  //add the <data> tags in the xml -> used for testing.php
		var $datagridcontent = "../../uploads";
		var $plugindir = ""; //this is used for images included in content tags (currently for the documentation)
		var $loadjqscrollto = false;  //load the jquery scrollto plugin
		var $session_authentication = false; //for ajax-load.php
		
		/*****************************/
		
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		//TODO to remove
		//var $iscodeelem = false; //this is set to true if a <code> element is found, no other elements will be parsed until the </code> element 
		var $customtemplates = array(array());   //if this is not empty the selected templates will loaded 
		var $customuid = "";
		var $contentgroup = "";
		var $ajaxload = false;   //used for ajax-load.php -> don't change unless if you know what you are doing :)
		var $times = array("xsl_parse" => "", "script_time" => "");
		
		
		function WpDojoLoader() { //constructor
			
			//load settings from the config.php
			global $gl_loadTinyMce;
			global $gl_loadLocalDojo;
			global $gl_loadOpenLayers;
			global $gl_loadOpenStreetMap;
			global $gl_loadjqueryui;			
			global $gl_customLoaderEnabled; 
			global $gl_import_wpdtemplate;
			global $gl_import_wpddata;								  
			global $gl_debugmode;
			global $gl_addcontenttags;
			global $gl_datagridcontent;
			global $gl_plugindir;
			global $gl_loadjqscrollto;
			global $gl_session_authentication;
			
			$this->loadTinyMce = $gl_loadTinyMce;
			$this->loadLocalDojo = $gl_loadLocalDojo;
			$this->loadOpenLayers = $gl_loadOpenLayers;
			$this->loadOpenStreetMap = $gl_loadOpenStreetMap;
			$this->loadjqueryui = $gl_loadjqueryui;			
			$this->customLoaderEnabled = $gl_customLoaderEnabled; 
			$this->import_wpdtemplate = $gl_import_wpdtemplate;
			$this->import_wpddata = $gl_import_wpddata;								  
			$this->debugmode = $gl_debugmode;
			$this->addcontenttags = $gl_addcontenttags;
			$this->datagridcontent = $gl_datagridcontent;
			$this->plugindir = $gl_plugindir;
			$this->loadjqscrollto = $gl_loadjqscrollto;
			$this->session_authentication = $gl_session_authentication;	
		}
		
		/*
		function WpDojoLoader($isdebug) { //constructor
			$this->debugmode = $isdebug;
		}
		*/
		
		/**
		 * returns true if the the plugin is activated in the dojoloader settings, otherwise false
		 * (NOT on the plugin page)
		 * @return boolean
		 */
		function isActive() {	
			if (function_exists("get_option"))
			{
				$adminoptions = get_option($this->adminOptionsName);
				$dojoLoaderAdminOptions = array();
				
				if (!empty($adminoptions)) {
					foreach ($adminoptions as $key => $option)
						$dojoLoaderAdminOptions[$key] = $option;
				}	
				if ($dojoLoaderAdminOptions["activate"] == "true") {
					return true;
				}
			}
			return false;
		}
		
		/**
		 * shows a debug message when debugmode is true
		 */
		function debug($text, $object)
		{
			if ($this->debugmode == true)
			{
				echo "<br/>";
				echo "########&nbsp;<br/>".$text."<br/>&nbsp;########<br/>";
				
				if ($object != null)
				{
					var_dump($object);
				}
				
				echo "<br/>";
			}				
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
			
			if (!isset($dl_adminLoader)) {
				$dl_adminLoader = new WpDojoLoader_AdminLoader();
			}
			
			$dl_adminLoader->initAdminOptions();
		}
		
		/**
		 * activate TinyMce Plugin
		 * @return 
		 */
		function activateTinyMcePluginButtons() {
			if (!function_exists("get_user_option"))				return;						if (!function_exists("add_filter"))				return;						
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
			
			$obj = $xpathcontext->xpath_eval('//post/@id'); // get all post elements with attribute id
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
			
			if (!function_exists("get_bloginfo"))
				return null;
			
			$result = array();
			
			//add the wpurl			
			$node = $domdocument->create_element( 'option' );
			$attr = $domdocument->create_attribute  ( "name"  , "wpurl"  );
			$text = $domdocument->create_text_node( get_bloginfo("wpurl") );
			$node->append_child( $attr );
			$node->append_child( $text );
			array_push($result, $node);	
			
			//add the wordpress upload dir
			$node = $domdocument->create_element( 'option' );
			$attr = $domdocument->create_attribute  ( "name"  , "wpuploaddir"  );
			$uploaddir = wp_upload_dir();
			$path = $uploaddir["basedir"]; 		
			$text = $domdocument->create_text_node($path);
			$node->append_child( $attr );
			$node->append_child( $text );
			array_push($result, $node);	
			
			//add the wordpress upload dir
			$node = $domdocument->create_element( 'option' );
			$attr = $domdocument->create_attribute  ( "name"  , "datagridcontent"  ); 		
			$text = $domdocument->create_text_node($this->datagridcontent);
			$node->append_child( $attr );
			$node->append_child( $text );
			array_push($result, $node);
			
			//add session id
			$node = $domdocument->create_element( 'option' );
			$attr = $domdocument->create_attribute  ( "name"  , "ssid"  );
			
			$sid = htmlspecialchars(SID);	
			$text = $domdocument->create_text_node(session_id());
			$node->append_child( $attr );
			$node->append_child( $text );
			array_push($result, $node);
			
			//add true if it is a ajax load
			if ($this->ajaxload)
			{
				$node = $domdocument->create_element( 'option' );
				$attr = $domdocument->create_attribute  ( "name"  , "ajaxload"  ); 		
				$text = $domdocument->create_text_node("true");
				$node->append_child( $attr );
				$node->append_child( $text );
				array_push($result, $node);
			}
			//add the contentgroup
			if ($this->contentgroup != "")
			{			
				$node = $domdocument->create_element( 'option' );
				$attr = $domdocument->create_attribute  ( "name"  , "contentgroup"  );
				$text = $domdocument->create_text_node( $this->contentgroup );
				$node->append_child( $attr );
				$node->append_child( $text );
				array_push($result, $node);	
			}
			
			if (count($result) > 0)		
				return $result;
				
			return null;	
		}
		
		
		/**
		 *
		 */
		function &getFirstChild(&$domdocument)
		{
			$de = $domdocument->document_element();
			return $de;
			$this->debug("getFirstChild",$de->node_name());
			$kids = $de->children();
			$nkids = count ($kids);
			if ($nkids > 0)
			{
				return $kids[0];
			}
			
			return null;
		}
    
	
	    /**
	     *
	     */
	    function getChildren(&$domdocument,$elementname)
	    {
	      $result = array();
	
	      $de = $domdocument->document_element();
	      $kids = $de->children();
	      
	      foreach($kids as $node)
	      {
	        if (strtolower($node->node_name()) == strtolower($elementname))
	        {
	          array_push($result, $node);	  
	        }
	      } 
	      
	      if (count($result) > 0)		
					return $result;
					
				return null;	      
	    }		
		
		
		/**
		 * 
		 * 
		 */
		function getImportElements($xpathcontext,$domdocument,&$parentnode, $importtype) {
			//$result = array();
			
			$this->debug("getTemplateElements",$importtype);
			
			$obj = $xpathcontext->xpath_eval("//import[@filename and @type = '$importtype']"); //get all template elements
			if ($obj) {
				$nodeset = $obj->nodeset;
				if ($nodeset != null) {
					foreach ($nodeset as $node) {
						
						$this->debug("getTemplateElements",$node->get_attribute("filename"));
						$this->debug("getTemplateElements",$node);
						
						
						//add 			
						$filename = $node->get_attribute("filename");								
						$this->debug("getTemplateElements1111",$filename);
	
						//$filename = dirname(__FILE__)."/".$filename;
						$uploaddir = wp_upload_dir();
						$filename = $uploaddir["basedir"]."/".$filename;
						if (!file_exists($filename))
						{
							$filename = $node->get_attribute("filename");	
							$filename = dirname(__FILE__)."/".$filename;
						}
						
						if (file_exists($filename))
						{
							$this->debug("getTemplateElements2222",$filename);
							$tpl = domxml_open_file($filename);
							
							$this->debug($tpl->dump_mem(true),null);
							
	            			$templates = $this->getChildren($tpl, $importtype);
				            //var_dump($templates);
	            			foreach ($templates as $template)
				            {
				              $nd = $template->clone_node(true);
											$parentnode->append_child($nd);
											$attr = $domdocument->create_attribute("filename",$filename);
											$nd->append_child( $attr );
				            }
							//$attr = $domdocument->create_attribute  ( "filename"  , $filename  );
							//$tpltxt = $tpl->dump_mem(true);
							//$this->debug($tpltxt,null);
							$text = $domdocument->create_text_node( $tpltxt );
							//$parentnode->append_child( $attr );
							$parentnode->append_child( $text );
						} else
						{
							$this->debug("file does not exist",$filename);
						}						
					} //foreach
				}
			}
			
			
			if (count($result) > 0)		
				return $result;
				
			return null;	
		}
			
		
		/**
		 * get the main content element from the xml document
		 * @return 
		 * @param $xpathcontext Object
		 */
		function getRootElement($xpathcontext) {
			$node = $xpathcontext->xpath_eval('/root'); //get content element
			return $node->nodeset[0];
		}
		
		
		function getContentElement($xpathcontext) {
			$node = $xpathcontext->xpath_eval('/root/data'); //get content element
			return $node->nodeset[0];
		}
		
		/**
		 * returns a array with the adminoptions
		 * @return array
		 */
		function getAdminOptions() {	
			if (function_exists("get_option"))
			{
				$adminoptions = get_option($this->adminOptionsName);
				$dojoLoaderAdminOptions = array();
				
				if (!empty($adminoptions)) {
					foreach ($adminoptions as $key => $option)
						$dojoLoaderAdminOptions[$key] = $option;
				}				
				return $dojoLoaderAdminOptions;
			}
		}
		
		/*
		 * add the dojo.require lines from the admin options 
		 */
		function addDojoRequireLines() {
			
			//dojo version 1.4 from google
			//echo '<SCRIPT TYPE="text/javascript"  SRC="http://ajax.googleapis.com/ajax/libs/dojo/1.4/dojo/dojo.xd.js" djConfig="parseOnLoad:false" ></SCRIPT>';
			//echo '<script type="text/javascript">';
			
			//dojo version 1.5 from google
			echo '<SCRIPT TYPE="text/javascript"  SRC="http://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js" djConfig="parseOnLoad:false" ></SCRIPT>';
						
			//some dojo styles
			echo '<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5/dijit/themes/claro/claro.css" />';
			echo '<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.5/dojox/grid/resources/claroGrid.css" />';
			
			echo '<script type="text/javascript">';
			$adminOptions = $this->getAdminOptions();			
			//var_dump($adminOptions);
			$require = "";
			for ($i=0;$i<count($adminOptions['require']);$i++) {
				echo stripslashes(trim($adminOptions['require'][$i]))."\n";
			}
			
			echo '</script>';
		}
		
		/**
		 * adds css lines from the admin options
		 */
		function addDojoCssLines() {
			
			echo '<style type="text/css">';
			$adminOptions = $this->getAdminOptions();
						
			//var_dump($adminOptions);
			$require = "";
			for ($i=0;$i<count($adminOptions['css']);$i++) {
				echo stripslashes(trim($adminOptions['css'][$i]))."\n";
			}
			
			echo '</style>';
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
		 * removes all calltemplate elements
		 * -> used for ajax-load.php
		 */
		function removeCallTemplate(&$dom) {
	      $elements = $dom->get_elements_by_tagname("calltemplate");
	      foreach ($elements as $elem) {
	        $prnt = $elem->parent_node();
	        $prnt->remove_child($elem);
	      }
		}
		
		/**
		 * move the content elements in the data section to the /root/contentlist section
		 * 
		 */
		function moveContent(&$xpathcontext,&$parentnode) {
		  $node = $xpathcontext->xpath_eval('/root/data/content'); //get content element
		  //return $node->nodeset[0];
			
		  if ($node === false)
		  	return;
		  
		  foreach ($node->nodeset as $nd)
		  {
		  	$prnt = $nd->parent_node();
		  	$prnt->remove_child($nd);
		  	$parentnode->append_child($nd);
		  }
		}
		
		/**
		 * adds the plugin dir to images source ... if you are using multi mode
		 *
		 */
		function changeImgSource(&$xpathcontext) {
			
			if ($this->plugindir == "")
			{
				return;
			}
			
			$node = $xpathcontext->xpath_eval('//img'); //get all img elements
		  	//return $node->nodeset[0];		  
		  	
			if ($node === false)
		  		return;
			
			foreach ($node->nodeset as &$nd)
		  	{
		  		$src = $nd->get_attribute("src");
		  	  	$src = $this->plugindir."/".$src;
		  		$nd->set_attribute("src",$src);	
		  	}		
		}
		
		/**
		 * enriches the xmlstring with linked posts, pages and gridstructures
		 * @return 
		 */
		function enrichXmlString($xmlstring) {
			$dom   = domxml_open_mem($xmlstring);
			$xpath = $dom->xpath_new_context();
			
			$elem_content = $this->getRootElement($xpath);
			if ($elem_content != null) {
				$elem_posts = $dom->create_element("posts");
				$elem_pages = $dom->create_element("pages");
				$elem_options = $dom->create_element("options");
				$elem_templates = $dom->create_element("templates");
				$elem_contents = $dom->create_element("contentlist");
				$elem_content->append_child($elem_posts);
				$elem_content->append_child($elem_pages);
				$elem_content->append_child($elem_options);
				$elem_content->append_child($elem_templates);
				$elem_content->append_child($elem_contents);
				
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
				
				//add template elements
				$this->getImportElements($xpath,$dom,$elem_templates,"template");
				$this->getImportElements($xpath,$dom,$elem_contents,"content");
				$this->moveContent($xpath,$elem_contents);
								
				// if there are customtemplate names we use only this for calltemplate
				if ($this->customtemplates[0][0] != "")
				{
					$this->removeCallTemplate($dom);
					$cnt = $this->getContentElement($xpath);
					
					foreach ($this->customtemplates as $tpl) {
						$elem_tpl = $dom->create_element("calltemplate");
						$attr1 = $dom->create_attribute("name",$tpl[0]);
						$elem_tpl->append_child( $attr1 );
						
						$attr2 = $dom->create_attribute("uid",$tpl[1]);
						$elem_tpl->append_child( $attr2 );
						
						$cnt->append_child( $elem_tpl );
					}	
				}
				/* */	
				$this->replaceGridStructures($xpath);
				$this->changeImgSource($xpath);
			}
			return $dom->dump_mem(true);
		}
		
		
		/**
		 * 
		 * @return 
		 * @param $xmlstring Object
		 */
		function xml_translate($xmlstring) {
			
			if ($this->debugmode == true)
			{
				echo $xmlstring;
			}
			
			$arguments = array(
     			'/_xml' => $xmlstring
			);
			$xh = xslt_create();
			
			$xslfile = dirname(__FILE__). '/wpdojoloader.xsl';
			$this->debug($xslfile,$xslfile);
			//$result = xslt_process($xh, 'arg:/_xml', 'wp-content/plugins/wpdojoloader/wpdojoloader.xsl', NULL, $arguments);
			$time_start = microtime(false);
			$result = xslt_process($xh, 'arg:/_xml', $xslfile, NULL, $arguments);
			
			$time_end = microtime(false);
			$this->times["xsl_parse"] = $time_end - $time_start." s";
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
			$xd .= "<root>";
			
			if ($this->addcontenttags)
			{
				$xd .= "<data>";	
			}
 			
			//auto import the wpd_template.xml template file
			if ($this->import_wpdtemplate)
			{
				$xd .= '<import type="template" filename="wpd_template.xml" />';	
			}
			
			//auto import the wpd_data.xml content file	
			if ($this->import_wpddata)
			{
				$xd .= '<import type="content" filename="wpd_data.xml" />';	
			}
			
			$xd .= $xmldata;
			
			if ($this->addcontenttags)
			{
				$xd .= "</data>";	
			}
			
			$xd .= "</root>";
				
			$xd = str_replace("&lt;","<",$xd);
			$xd = str_replace("&gt;",">",$xd);
			
			if ($this->debugmode)
			{
				echo "<!-- BEGIN XML".$xd." END XML -->"; //debug only
			}
						
			$xd = $this->enrichXmlString($xd);

			if ($this->debugmode)
			{
				echo "<!-- BEGIN XML".$xd." END XML -->"; //debug only
			}
			
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

				$this->debug($inner,null);
				
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
			/*
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/themes/tundra/tundra.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/grid/resources/Grid.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/grid/resources/tundraGrid.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/highlight/resources/highlight.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/layout/resources/ResizeHandle.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/dojox/layout/resources/ScrollPane.css" />' . "\n";
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/resources/dojo.css" />' . "\n";
			*/
			//echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/css/wpdojoloader.css" />' . "\n";
			
			if ($this->loadjqueryui) {
				echo '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7/themes/smoothness/jquery-ui.css" />' . "\n";
			}
			
			
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
				
				if ($this->loadjqueryui) {			
					
					wp_enqueue_script('jqueryui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js', array('prototype'), '0.1');
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
					
					//version 1.4
					//wp_enqueue_script('dojo', 'http://ajax.googleapis.com/ajax/libs/dojo/1.4/dojo/dojo.xd.js', array('prototype'), '0.1');
				}
				
				
				//wp_enqueue_script('osmdatamanager', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/trm/Application.js', array('prototype'), '0.1');
				
				//add the wpdojoloader js functions
				wp_enqueue_script('wpdojoloader', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/wpdojoloader.js', array('prototype'), '0.1');				
				
				//jquery jquery.scrollTo-min.js
				if ($this->loadjqscrollto)
				{
					wp_enqueue_script('loadjqscrollto', get_bloginfo('wpurl') . '/wp-content/plugins/wpdojoloader/js/jquery.scrollTo-min.js', array('prototype'), '0.1');
				}					
			}
			
			$this->addDojoRequireLines();
			$this->addDojoCssLines();
		}
				
		/**
		 * filter content
		 * @return 
		 * @param $content Object[optional]
		 */
		function addContent($content = '') {
			
			if ($this->session_authentication)
			{
				$_SESSION["wpd_session_auth"] = null;	
			}
			
			$rslt = "";
			$time_start = microtime(false);
			$rslt .= $this->parseContent($content);
			$time_end = microtime(false);
			if ($this->debugmode)
			{
				$this->times["script_time"] = $time_end - $time_start;
				var_dump($this->times);
			}
			
			if ($this->session_authentication)
			{
				$_SESSION["wpd_session_auth"] = uniqid("wpd_session_auth");
			}
			return $rslt;	
		}
		
		function addTitle($content = '') {
			return "<div style=\"border:solid 1px red;\">".$content."</div>";
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
		
	if (function_exists("add_action"))
	{
		//test
		add_action('admin_menu', 'WpDojoLoader_showAdmin');
		if ($dl_dojoLoader->isActive()) {
			//Actions
			add_action('wp_print_scripts',array(&$dl_dojoLoader, 'addHeaderCode'), 1);
					
			//Filters
			//add_action('the_post', array(&$dl_dojoLoader, 'addTitle'),1);
			//add_filter('the_title', array(&$dl_dojoLoader, 'addTitle'),1);
			
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
	
}



?>