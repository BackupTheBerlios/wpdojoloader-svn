<?php
/*
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

	/******************************
	 * 
	 * BEGIN Config Section
	 * 
	 ******************************/
	
	//javascript libraries
	var $gl_loadTinyMce = false;     	//load TinyMCE
	var $gl_loadLocalDojo = false;   	//load a local version of dojo instead of google
	var $gl_loadOpenLayers = false;  	//load the openlayers api, used for some custom widgets
	var $gl_loadOpenStreetMap = false;	//load the openstreetmap api, used for some custom widgets
	var $gl_loadjqueryui = false;  //load jqueryui
	
	//general options
	var $gl_customLoaderEnabled = false; //if this is set to true, the custom loader is enabled which contains some other none dojo elements  //TODO deprecated 
	var $gl_import_wpdtemplate  = true;  //auto import the wpd_template.xml template file
	var $gl_import_wpddata      = true;  //auto import the wpd_data.xml content file								  
	
	var $gl_debugmode = false;      //if this is set to true dojoloader makes some debug output -> use only for debugging
	var $gl_addcontenttags = true;  //add the <data> tags in the xml -> used for testing.php
	var $gl_datagridcontent = "/wordpress3/wp-content/blogs.dir/2/files";
	var $gl_plugindir = "/wordpress3/wp-content/plugins/wpdojoloader"; //this is used for images included in content tags (currently for the documentation)
	var $gl_loadjqscrollto = true;  //load the jquery scrollto plugin
	
	/*****************************
	 * 
	 * END Config Section
	 * 
	 *****************************/


?>