<?php

if (!class_exists("DojoGenerator")) {
	
	/**
	 * used for creating dojo html output
	 * @return 
	 */
	class DojoGenerator {

		var $id_cnt = 0;
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		var $addResizeDiv = false;	//if this is set to true, a contentpane will contain a resize handler
		var $prevId = "";  			//contains the last id which was returned from getId()
		
		function DojoGenerator() { //constructor
			
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
		 * returns a dijit.layout.ContentPane div container
		 * @return 
		 * @param $aTitle Object
		 * @param $aStyle Object[optional]
		 * @param $aCanResize Object[optional] if this is set to "true" the contentpane will be resizeable
		 */
		function getContentPane_start($aTitle,$aStyle = null,$aCanResize=null,$aRegion = null) {
			$style = ""; //here you can set a default style
			
			if (isset($aStyle)) {
				$style = $aStyle;
			}
			
			$region = "";
			if (isset($aRegion)) {
				$region = " region= \"$aRegion\" ";	
			}
			
			$elemid = ""; 			
			if ($aCanResize != null) {
				if (strtolower($aCanResize) == "true") {
					$this->addResizeDiv = true;
					$style .= " position: relative; ";
				}
				$elemid = " id =\"".$this->getId()."\""; //add a custom id to the element
			}
			
			$rslt = "<div $elemid closable=\"false\" dojoType=\"dijit.layout.ContentPane\" class=\"tundra wpdojoloader_contentpane\" style=\"$style\" title=\"$aTitle\" $region >";
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
			$rslt = ""; //"<div class=\"wpdojoloader\" >";
			
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
			return "</div>";
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
			$rslt = "<div class=\"wpdojoloader_post\">";
			
			$pst = get_post($aPostId);
			if ($pst != null) {
				$rslt .= $pst->post_content;
			}
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
		 * returns the content of a wordpress page with the given id
		 * @return 
		 * @param $aPageId Object
		 */
		function getPage_start($aPageId) {
			$rslt = "<div class=\"wpdojoloader_page\">";
			
			$pst = get_page($aPageId);
			if ($pst != null) {
				$rslt .= $pst->post_content;
			}
			return $rslt;
		}
		
		/**
		 * returns a </div>
		 * @return 
		 */
		function getPage_end() {
			return "</div>";
		}
		
		
		/**
		 * returns div elements used for a fisheye, fisheye is created in the js functions
		 * @return 
		 */
		function getFisheyeLite_start() {
			return "<div class=\"wpdojoloader_fisheyelite\">";	//<div class=\"wpdojoloader\">
		}
		
		/**
		 * returns "</div></div>"
		 * @return 
		 */
		function getFisheyeLite_end() {
			return "</div>";	//</div>
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
		 * end of a link element
		 * @return 
		 */
		function getLink_end() {
			return "</a>";	
		}
		
		/**
		 * scrollpane start
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
		 * scrollpane end 
		 * @return "</div>"
		 */
		function getScrollPane_end() {
			return "</div>";
		}
		
		/**
		 * accordion container start
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
		 * accordion container end
		 * @return 
		 * @param $aStyle Object[optional]
		 * @param $aDuration Object[optional]
		 */
		function getAccordionContainer_end($aStyle = null, $aDuration = null) {
			return "</div></div>";
		}
		
		/**
		 * accordion pane start
		 * @return 	
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
		 * accordion pane end
		 * @return 
		 */
		function getAccordionPane_end() {
			//</div>
			return "</div>";	
		}
		
		/**
		 * dojo button start
		 * @return 
		 * @param $aFunction Object
		 */
		function getButton_start($aFunction) {
			$onclick = "";
			switch (strtoupper($aFunction)) {
			    case 'INITTINY':
					//$onclick = " onClick=\"wpdojoloader_initTinyMce(this)\" ";
					break;
			}
			
			$rslt = "<div class=\"wpdojoloader\"><button $onclick dojoType=\"dijit.form.Button\" >";
			return $rslt;
		}
		
		/**
		 * dojo button end
		 * @return 
		 */
		function getButton_end() {
			$rslt = "</button></div>";
			return $rslt;
		}
		
		/**
		 * 
		 * @return 
		 * @param $aClass Object
		 * @param $aStyle Object[optional]
		 * @param $aAnimation Object[optional]
		 */
		function getBox_start($aClass, $aStyle = null, $aAnimation = null) {
			$cls   = " class = \"".$aClass."\" ";
			$style = ""; //default style
			$id1 = "id=\"".$this->getId()."\"";
			
			if (isset($aStyle)) {
				$style   = " style = \"".$aStyle."\" ";	
			}
			
			$animation = "";
			if (isset($aAnimation)) {
				$animation   = " animation = \"".strtolower($aAnimation)."\" ";	
				$cls = "class = \"wpdojoloader_animation $aClass\" ";
			}
			return "<div $id1 $cls $style $animation>";
		}
		
		/**
		 * 
		 * @return 
		 */
		function getBox_end() {
			return "</div>";
		}
		
		/**
		 * 
		 * @return 
		 * @param $aDesign Object[optional]
		 * @param $aStyle Object[optional]
		 */
		function getBorderContainer_start($aDesign = null, $aStyle = null) {
			
			$style = " style=\"width:100%; heigth:200px;\" ";
			if (isset($aStyle)) {
				$style = " style =\"$aStyle\" ";	
			}
			
			$design = " design=\"headline\" "; //this is the dojo default
			if (isset($aDesign)) {
				$design = " design =\"$aDesign\" ";	
			}
			
			$rslt = "<div class=\"wpdojoloader\">";
			$rslt .= "<div dojoType=\"dijit.layout.BorderContainer\" $design $style  >";
			return $rslt;
		}
		
		/**
		 * 
		 * @return 
		 */
		function getBorderContainer_end() {
			return "</div></div>";
		}
		
		
	}  //end class DojoLoader
	
} //end if (!class_exists("DojoGenerator")) {

?>