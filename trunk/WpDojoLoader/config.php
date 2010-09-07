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
	$gl_loadTinyMce = false;     	//load TinyMCE
	$gl_loadLocalDojo = false;   	//load a local version of dojo instead of google
	$gl_loadOpenLayers = false;  	//load the openlayers api, used for some custom widgets
	$gl_loadOpenStreetMap = false;	//load the openstreetmap api, used for some custom widgets
	$gl_loadjqueryui = false;  //load jqueryui
	
	//general options
	$gl_customLoaderEnabled = false; //if this is set to true, the custom loader is enabled which contains some other none dojo elements  //TODO deprecated 
	$gl_import_wpdtemplate  = true;  //auto import the wpd_template.xml template file
	$gl_import_wpddata      = true;  //auto import the wpd_data.xml content file								  
	
	$gl_debugmode = false;      //if this is set to true dojoloader makes some debug output -> use only for debugging
	$gl_addcontenttags = true;  //add the <data> tags in the xml -> used for testing.php
	$gl_datagridcontent = "../../uploads";
	$gl_plugindir = ""; //this is used for images included in content tags (currently for the documentation)
	$gl_loadjqscrollto = false;  //load the jquery scrollto plugin
	$gl_session_authentication = true; //used for ajax-load.php ... if true you must have a valid session to get content from ajax-load.php -> if the session expires you can't load with ajax-load 
	
	/*****************************
	 * 
	 * END Config Section
	 * 
	 *****************************/


?>