<?php

if (!class_exists("WpDojoLoader_CustomGenerator")) {
	
	/**
	 * used for creating other html output, for example a tinymce frontend editor
	 * @return 
	 */
	class WpDojoLoader_CustomGenerator {
		
		var $adminOptionsName = "WpDojoLoaderAdminOptions";
		var $prevId = "";  			//contains the last id which was returned from getId()
		
		function WpDojoLoader_CustomGenerator() { //constructor
			
		}
		
		/**
		 * returns a unique id for dojo widgets
		 * @return 
		 */
		function getId() {
			$this->id_cnt++;
			$newid = "wpdojoloader_custom_id_".$this->id_cnt;
			$this->prevId = $newid;
			return $newid;
		}
		
		/**
		 * returns the start element for a frontend editable post
		 * @return 
		 */
		function getDynamicPost_start() {
			$wpid = get_the_ID(); //the current wordpress post id
			$id1 = $this->getId();
			$rslt = "<div class=\"wpdojoloader\" wpid=\"$wpid\">";
			$rslt .= "<div class=\"wpdojoloader_buttonmenu\" >";
			$rslt .= "<button onClick=\"wpdojoloader_initTinyMce(this)\" dojoType=\"dijit.form.Button\" >Edit</button>";
			$rslt .= "<button onClick=\"wpdojoloader_savePost(this)\" dojoType=\"dijit.form.Button\" >Save</button>";
			$rslt .= "<button onClick=\"wpdojoloader_hideTinyMce(this)\" dojoType=\"dijit.form.Button\" >Cancel</button>";
			$rslt .= "<span class=\"wpdojoloader_tinymcestatus\"></span>";
			$rslt .= "</div>"; //end div wpdojoloader_buttonmenu
			$rslt .= "<div id=\"$id1\" class=\"wpdojoloader_dynamiccontent\">";
			return $rslt;
		}
		
		/**
		 * returns "</div></div>"
		 * @return 
		 */
		function getDynamicPost_end() {
			return "</div></div>";			
		}
		
		function getOsmMap_start() {
			$id1 = " id=\"".$this->getId()."\" ";
			$rslt = "<div $id1 class=\"wpdojoloader_osmmap\" >";
			//$rslt = "<div dojoType=\"wpd.widget.OsmMapWidget\" >";
			return $rslt;
		}
		
		function getOsmMap_end() {
			return "</div>";
		}
		
	} //end class WpDojoLoader_CustomGenerator
	
} //end if (!class_exists("WpDojoLoader_CustomGenerator")) {
?>