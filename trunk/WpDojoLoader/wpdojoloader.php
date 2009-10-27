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

if (!class_exists("WpDojoLoader")) {
	
	/**
	 * used for creating dojo html output
	 * @return 
	 */
	class DojoLoader {
		
		var $id_cnt = 0;
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		var $addResizeDiv = false;	//if this is set to true, a contentpane will contain a resize handler
		var $prevId = "";  			//contains the last id which was returned from getId()
		
		function DojoLoader() { //constructor
			
		}
		
		/**
		 * returns a unique id for dojo widgets
		 * @return 
		 */
		function getId() {
			$this->id_cnt++;
			$newid = "wpdojoloader_id_".$this->id_cnt;
			$this->prevId = $newid;
			return $newid;
		}
		
		
		/**
		 * 
		 * @return 
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
		 * returns a dijit.layout.ContentPane div container
		 * @return 
		 * @param $aTitle Object
		 * @param $aStyle Object[optional]
		 * @param $aCanResize Object[optional] if this is set to "true" the contentpane will be resizeable
		 */
		function getContentPane_start($aTitle,$aStyle = null,$aCanResize=null) {
			$style = ""; //here you can set a default style
			
			if (isset($aStyle)) {
				$style = $aStyle;
			}
			
			$elemid = ""; 			
			if ($aCanResize != null) {
				if (strtolower($aCanResize) == "true") {
					$this->addResizeDiv = true;
				}
				$elemid = " id =\"".$this->getId()."\""; //add a custom id to the element
			}
			
			$rslt = "<div $elemid dojoType=\"dijit.layout.ContentPane\" class=\"tundra wpdojoloader_contentpane\" style=\"$style\" title=\"$aTitle\" >";
			return $rslt;
		}
		
		/**
		 * returns a </div>, if $aCanResize is set to "true" in the start function, a dojox.layout.ResizeHandle will be created 
		 * @return 
		 */
		function getContentPane_end() {
			$rslt = "";
			if ($this->addResizeDiv) {
				$rslt .= "<div dojoType=\"dojox.layout.ResizeHandle\" targetId=\"$this->prevId\">test</div>";
				$this->addResizeDiv = false;
			}
			$rslt .= "</div>";
			return $rslt;
		}
		
		/**
		 * returns a dijit.layout.TabContainer div
		 * @return 
		 */
		function getTabContainer_start($aStyle = null) {
			$rslt = "<div class=\"wpdojoloader\" >";
			
			$style = "width:100%;height:200px"; //default style
			if (isset($aStyle))
				$style = $aStyle;
			
			$rslt .= "<div dojoType=\"dijit.layout.TabContainer\" class=\"tundra wpdojoloader_tab\" style=\"$style\">";
			return $rslt;
		}
		
		/**
		 * returns a </div></div>
		 * @return 
		 */
		function getTabContainer_end() {
			return "</div></div>";
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
		 * returns a dojox.grid.DataGrid div
		 * @return 	
		 */
		function getDataGrid_start($aStore,$aStructurename,$aFilename = null,$aStyle = null) {
			$style = "width: 100%; height: 300px;"; //default styles
			if (isset($aStyle))
				$style = $aStyle;
			
			$filename = "";
			if (isset($aFilename))
				$filename = $aFilename;
				
			$elemid = $this->getId();
			$wud = wp_upload_dir();
			$wud = $wud['subdir'];
			
			$fieldnames = $this->getFieldnames($aStructurename);
			$rslt = "<div class=\"tundra wpdojoloader\" fieldnames=\"$fieldnames\" uploaddir=\"$wud\" storetype=\"$aStore\" filename=\"$filename\" >";
			$rslt .= "<div id=\"$elemid\" class=\"wpdojoloader_datagrid\" style=\"$style\" dojoType=\"dojox.grid.DataGrid\" rowsPerPage=\"40\">";	
			return $rslt;
		}
		
		/**
		 * 
		 * @return </div></div>
		 */
		function getDataGrid_end() {
			return "</div></div>";
		}
		
		/**
		 * returns the content of a wordpress post with the given id
		 * @return 
		 * @param $aPostId Object
		 */
		function getPost_start($aPostId) {
			$rslt = "<div class=\"wpdojoloader\">";
			
			$pst = get_post($aPostId);
			if ($pst != null) {
				$rslt .= $pst->post_content;
			}
			//debug only
			//$rslt = str_replace("[dojocontent]","[d_ojocontent]",$rslt);
			//$rslt = str_replace("[/dojocontent]","[/d_ojocontent]",$rslt);
			return $rslt;
		}
		
		/**
		 * returns a </div>
		 * @return 
		 */
		function getPost_end() {
			return "</div>";
		}
		
		/**
		 * returns div elements used for a fisheye, fisheye is created in the js functions
		 * @return 
		 */
		function getFisheyeLite_start() {
			return "<div class=\"wpdojoloader\"><div class=\"wpdojoloader_fisheyelite\">";	
		}
		
		/**
		 * returns "</div></div>"
		 * @return 
		 */
		function getFisheyeLite_end() {
			return "</div></div>";	
		}
		
		/**
		 * 
		 * @return 
		 */
		function getHighlight_start($aLanguage) {
			return "<div class=\"wpdojoloader\"><pre><code lang=\"$aLanguage\" class=\"wpdojoloader_highlight\">";
		}
		
		/**
		 * 
		 * @return 
		 */
		function getHighlight_end() {
			return "</code></pre></div>";  //
		}
		
		/**
		 * used for the google syntax highlightner
		 * @return 
		 * @param $aLanguage Object
		 */
		function getCode_start($aLanguage) {
			return "<div class=\"wpdojoloader\"><pre name=\"code\" class=\"$aLanguage\"> "; 
		}
		
		/**
		 * 
		 * @return 
		 */
		function getCode_end() {
			return "</pre></div>";
		}
		
		/**
		 * creates a link to wordpress pages, post and categories
		 * @return 
		 * @param $aLinkType String at the moment CAT, POST and PAGE
		 * @param $aLinkId String wordpress id of the target
		 */
		function getLink_start($aLinkType, $aLinkId) {
			$rslt = "<a href=\"".get_bloginfo("wpurl")."/";
			switch (strtoupper($aLinkType)) {
			    case 'CAT':
					$rslt .= "?cat=$aLinkId";
					break;
				case 'POST':
					$rslt .= "?p=$aLinkId";
					break;
				case 'PAGE':
					$rslt .= "?page_id=$aLinkId";
					break;
			}
			$rslt .= "\" >";
			return $rslt;
		}
		
		/**
		 * 
		 * @return 
		 */
		function getLink_end() {
			return "</a>";	
		}
		
		/**
		 * 
		 * @return 
		 * @param $aOrientation Object
		 * @param $aStyle Object[optional]
		 */
		function getScrollPane_start($aOrientation, $aStyle = null) {
			$style = "width: 200px; height: 100px; border: solid 1px black;"; //default style
			if (isset($aStyle)) {
				$style = $aStyle;
			}
			return "<div dojoType=\"dojox.layout.ScrollPane\" orientation=\"$aOrientation\" style=\"$style\" >";
		}
		
		/**
		 * 
		 * @return "</div>"
		 */
		function getScrollPane_end() {
			return "</div>";
		}
		
		/**
		 * 
		 * @return 
		 * @param $aStyle Object[optional]
		 * @param $aDuration Object[optional]
		 */
		function getAccordionContainer_start($aStyle = null, $aDuration = null) {
			$style = "width: 100%;height: 300px;"; //default style
			$duration = "80"; //default duration
			if (isset($aStyle)) {
				$style	= $aStyle;
			}	
			if (isset($aDuration)) {
				$duration = $aDuration;	
			}
			return "<div class=\"wpdojoloader tundra\"><div dojoType=\"dijit.layout.AccordionContainer\" style=\"$style\" duration=\"$duration\"  >";
		}
		
		/**
		 * 
		 * @return 
		 * @param $aStyle Object[optional]
		 * @param $aDuration Object[optional]
		 */
		function getAccordionContainer_end($aStyle = null, $aDuration = null) {
			return "</div></div>";
		}
		
		/**
		 * e
		 * @return dd	
		 * @param $aTitle Object
		 * @param $aSelected Object[optional]
		 */
		function getAccordionPane_start($aTitle, $aSelected = null) {
			$selected = "false";
			if (isset($aSelected)) {
				if (strtolower($aSelected) == "true" ) {
					$selected = "true";
				}
			}		
			//<div class=\"wpdojoloader_accordionpane\">	
			return "<div dojoType=\"dijit.layout.AccordionPane\" selected=\"$selected\" title=\"$aTitle\">";	
		}
		
		/**
		 * 
		 * @return 
		 */
		function getAccordionPane_end() {
			//</div>
			return "</div>";	
		}
		
	}  //end class DojoLoader
	
	
	/**
	 * functions for the wordpress admin pages
	 */
	class WpDojoLoader_AdminLoader {
		
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		
		function WpDojoLoader_AdminLoader() { //constructor
			
		}
		
				
		/**
		 * returns a array with the admin options
		 * @return 
		 */
		function getAdminOptions() {
			//delete_option($this->adminOptionsName);  //debug //TODO remove
			
			$dojoLoaderAdminOptions = array(
				'activate' => 'true',
				'gridstructure' => array(array('name' => 'gridstructure_1', 'structure' => 'name,link'))
				);
				
			$adminoptions = get_option($this->adminOptionsName);
			if (!empty($adminoptions)) {
				foreach ($adminoptions as $key => $option)
					$dojoLoaderAdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $dojoLoaderAdminOptions);

			return $dojoLoaderAdminOptions;
		}
		
		/**
		 * prints the admin page
		 * @return 
		 */
		function printAdminPage() {
			$adminOptions = $this->getAdminOptions();
			
			//store options
			if (isset($_POST['update_wpdojoloader_adminoptions'])) { 
				
				if (isset($_POST['wpdojoloader_activate'])) {
					$adminOptions['activate'] = $_POST['wpdojoloader_activate'];
				}
								
				//update existing grid structures
				for ($i=count($adminOptions['gridstructure']);$i>-1;$i--) {
					$gsname = $adminOptions['gridstructure'][$i]['name'];
					$gsvalue = $_POST[$gsname];
					$deletevalue = trim($_POST["del_".$gsname]);
					//check if the delete checkbox is selected
					if (($deletevalue == $gsname) && ($deletevalue != "")) {
						unset($adminOptions['gridstructure'][$i]);  //delete a selected structure
					} else {
						if (! empty($gsname)) {
							$adminOptions['gridstructure'][$i]['structure'] = $gsvalue; 	 
						}
					}
				}
				
				//add new grid structure
				if (isset($_POST['gridstructure_name'])) {
					$gsname  = trim($_POST['gridstructure_name']);
					$gsvalue = trim($_POST['gridstructure_value']);
					$doadd = true;
					
					//check if a gridstructure with posted gridstructure_name exist, if true nothing will happen 
					for ($i=0;$i<count($adminOptions['gridstructure']);$i++) {
						if ((strtolower($gsname)) == strtolower($adminOptions['gridstructure'][$i]['name'])) {
							$doadd = false;
							break;
						}
					}
					
					if ((! empty($gsname)) && (! empty($gsvalue)) && $doadd) {
						//create a new gridstructure array
						$idx = count($adminOptions['gridstructure']) + 1;
						$newgs = array('name'=>$gsname,'structure'=>$gsvalue);
						array_push($adminOptions['gridstructure'],$newgs);	
					}
				}
				update_option($this->adminOptionsName, $adminOptions); //store options
			}	
			
			?>
			<div class=wrap>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<h2>Wordpress Dojo Loader Plugin</h2>
			
			<h3>Activate Dojo Loader Plugin?</h3>
			<p>
				<label for="wpdojoloader_activate_yes"><input type="radio" id="wpdojoloader_activate_yes" name="wpdojoloader_activate" value="true" <?php if ($adminOptions['activate'] == "true") { _e('checked="checked"'); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="wpdojoloader_activate_no"><input type="radio" id="wpdojoloader_activate_no" name="wpdojoloader_activate" value="false" <?php if ($adminOptions['activate'] == "false") { _e('checked="checked"'); }?>/> No</label>
			</p>
						
			<hr/>
			<h2>Datagrid Structures</h2><br/>
			<i>please enter the grid fieldnames in comma seperated values</i>
			<i>e.g. name,link</i>
			<p>
				<?php
					//load the existing grid structure
				  	echo "<table>";
				  	for ($i=0;$i<count($adminOptions['gridstructure']);$i++) {
				  		echo "<tr>";
						$gs_name = $adminOptions['gridstructure'][$i]['name'];
						$gs_value = $adminOptions['gridstructure'][$i]['structure'];
						echo "<td><b>$gs_name</b></td><td><input name=\"$gs_name\" value=\"$gs_value\"/></td>";
						echo "<td>delete&nbsp;<input type=\"checkbox\" name=\"del_$gs_name\" value=\"$gs_name\"></td>";
						echo "</tr>";
				    }
					echo "</table><br/>";	  
					echo "<hr/>";
					
					//add fields for a new grid structure
					echo "create a new grid structure <br/>";
					$idx = count($adminOptions['gridstructure']) + 1;
					echo "<b><label>Structurename</label></b>&nbsp;<input value=\"gridstructure_$idx\" name=\"gridstructure_name\" />";
					echo "<label>Gridstructure</label><input name=\"gridstructure_value\" />";
					
				?>
			</p>
			
			<div class="submit">
			<input type="submit" name="update_wpdojoloader_adminoptions" value="<?php _e('Update Settings') ?>" /></div>
			</form>
			
			 </div>
			 <?php
		
		} //end function printAdminPage 
		
	} //end class WpDojoLoader_AdminLoader
	
	
	/**
	 * this class manages the parsing of posts and pages and inserts dojo content
	 */
	class WpDojoLoader {
		
		var $dojoloader = null;
		var $dojocontent = "";
		var $currentdata = "";
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		var $iscodeelem = false; //this is set to true if a <code> element is found, no other elements will be parsed until the </code> element 
		
		function WpDojoLoader() { //constructor
			$this->dojoloader = new DojoLoader();	
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
				$this->currentdata .= $data; 
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
			
			//if a element within the wpdojoloader is not a wpdojoloader element,
			//this element will be inserted (e.g. <li> or <img> elements)
			if ((! $this->isValidElement($name)) || $this->iscodeelem) {
				$this->currentdata .= "<".$name." "; 		//insert the start element
				foreach ($attributes as $key => $value) {   //insert the attributes from the start element
				   $this->currentdata .= " ".$key."=\"".$value."\"";
			   	}	
				$this->currentdata .= ">";
				return;
			}
			
			//$this->currentdata = "";
		   	switch ($name) {
			    case 'TABCONTAINER':
					$this->dojocontent .= $this->dojoloader->getTabContainer_start($attributes['STYLE']);
					break;
				case 'CONTENTPANE':
					$this->dojocontent .= $this->dojoloader->getContentPane_start($attributes['TITLE'],$attributes['STYLE'],$attributes['RESIZE']);
					break;
				 case 'DATAGRID':
					$this->dojocontent .= $this->dojoloader->getDataGrid_start($attributes['STORETYPE'],$attributes['STRUCTURENAME'],$attributes['FILENAME'], $attributes['STYLE']);
					break;
				case 'POST':
					$this->dojocontent .= $this->dojoloader->getPost_start($attributes['ID']);
					break;
				case 'FISHEYE':
					$this->dojocontent .= $this->dojoloader->getFisheyeLite_start();
					break;
				/* currently not activated it's a little bit buggy at the moment 
				case 'HIGHLIGHT':
					$this->dojocontent .= $this->dojoloader->getHighlight_start($attributes['LANG']);
					break;
				case 'CODE':
					$this->dojocontent .= $this->dojoloader->getCode_start($attributes['LANG']);
					$this->iscodeelem = true; 
					break;
				*/
				case 'LINK':
					$this->dojocontent .= $this->dojoloader->getLink_start($attributes['TYPE'],$attributes['ID']);
					break;
				case 'SCROLLPANE':
					$this->dojocontent .= $this->dojoloader->getScrollPane_start($attributes['ORIENTATION'],$attributes['STYLE']);
					break;
				case 'ACCORDIONCONTAINER':
					$this->dojocontent .= $this->dojoloader->getAccordionContainer_start($attributes['STYLE'],$attributes['DURATION']);
					break;
				case 'ACCORDIONPANE':
					$this->dojocontent .= $this->dojoloader->getAccordionPane_start($attributes['TITLE'],$attributes['SELECTED']);
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
			
			if (! $this->isValidElement($name)) {
				$this->currentdata .= "</".$name.">"; //insert end the element
				return;
			}
			
			//not needed at the moment
			if (($this->iscodeelem) && ($name != "CODE")) { 
				$this->currentdata .= "</".$name.">"; //insert end the element
				return;	
			} 
			//echo "<!-- ENDTAG $name -->"; //debug only
			switch ($name) {
			    case 'TABCONTAINER':
					$this->dojocontent .= $this->dojoloader->getTabContainer_end();
					break;
				case 'CONTENTPANE':
					$this->dojocontent .= $this->currentdata; //insert the data between <CONTENTPANE> and </CONTENTPANE>
					$this->dojocontent .= $this->dojoloader->getContentPane_end();
					break;
				case 'DATAGRID':
					$this->dojocontent .= $this->dojoloader->getDataGrid_end();
					break;
				case 'POST':
					$this->dojocontent .= $this->currentdata;
					$this->dojocontent .= $this->dojoloader->getPost_end();
					break;
				case 'FISHEYE':
					$this->dojocontent .= $this->currentdata; //insert the data between <FISHEYE> and </FISHEYE>
					$this->dojocontent .= $this->dojoloader->getFisheyeLite_end();
					break;
				/* currently not activated it's a little bit buggy at the moment 
				case 'HIGHLIGHT':
					$this->dojocontent .= $this->currentdata; //insert the data between <HIGHLIGHT> and </HIGHLIGHT>
					$this->dojocontent .= $this->dojoloader->getHighlight_end();
					break;
				case 'CODE':
					$this->dojocontent .= $this->currentdata; //insert the data between <PRE> and </PRE>
					$this->dojocontent .= $this->dojoloader->getCode_end();
					$this->iscodeelem = false;
					break;
				*/
				
				case 'LINK':
					$this->dojocontent .= $this->currentdata; //insert the data between <LINK> and </LINK>
					$this->dojocontent .= $this->dojoloader->getLink_end();
					break;
				case 'SCROLLPANE':
					$this->dojocontent .= $this->currentdata; //insert the data between <SCROLLPANE> and </SCROLLPANE>
					$this->dojocontent .= $this->dojoloader->getScrollPane_end();
					break;
				case 'ACCORDIONCONTAINER':
					//$this->dojocontent .= $this->currentdata; //insert the data between <SCROLLPANE> and </SCROLLPANE>
					$this->dojocontent .= $this->dojoloader->getAccordionContainer_end();
					break;
				case 'ACCORDIONPANE':
					$this->dojocontent .= $this->currentdata; //insert the data between <ACCORDIONPANE> and </ACCORDIONPANE>
					$this->dojocontent .= $this->dojoloader->getAccordionPane_end();
					break;
		   }
		   $this->currentdata = "";
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
					echo "<!-- BEGIN CONTENT ".$this->dojocontent." END CONTENT -->"; //TODO debug only remove
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
				wp_enqueue_script('jquery');
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