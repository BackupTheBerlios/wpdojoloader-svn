<?php
	
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
				'gridstructure' => array(array('name' => 'gridstructure_1', 'structure' => 'name,link')),
				'require' => array('dojo.require("dijit.Dialog");'),
				'xslt' => array('<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">'),
				'css' => array('.wpdojoloader {}')
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
		 * init the admin options, used for activation
		 */
		function initAdminOptions()
		{
			//styles
			$fp = @fopen(dirname(__FILE__)."/css/wpdojoloader.txt", "r") or die ("can not open file wpdojoloader.css.css");
		  	$i=0;
			while($line = fgets($fp, 1024)){
		  		$adminOptions['css'][$i] = trim($line);
		  		$i++;
		  	}
		  	fclose($fp);
			
		  	//dojo require lines
		  	$fp = @fopen(dirname(__FILE__)."/require.txt", "r") or die ("can not open file require.txt");
		  	$i=0;
			while($line = fgets($fp, 1024)){
		  		$adminOptions['require'][$i] = trim($line);
		  		$i++;
		  	}
		  	fclose($fp);
		  	
		  	update_option($this->adminOptionsName, $adminOptions); //store options
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
				
				//dojo require option
				if (isset($_POST['wpdojoloader_dojorequire'])) {
				   
					for ($i=count($adminOptions['require']);$i>-1;$i--) {
						unset($adminOptions['require'][$i]);	
					}
					
				   $lines = preg_split("/\r\n/", $_POST["wpdojoloader_dojorequire"]);
				   $i = 0;
				   foreach ($lines as $key => $value) {
				      //$adminOptions['require'][$i] = trim($value);
				      $adminOptions['require'][$i] = $value;
				      $i++;
				    }
				}
									
				//css option
				if (isset($_POST['wpdojoloader_css'])) {
				   
					for ($i=count($adminOptions['css']);$i>-1;$i--) {
						unset($adminOptions['css'][$i]);	
					}
					
				   $lines = preg_split("/\r\n/", $_POST["wpdojoloader_css"]);
				   $i = 0;
				   foreach ($lines as $key => $value) {
				      //$adminOptions['require'][$i] = trim($value);
				      $adminOptions['css'][$i] = $value;
				      $i++;
				    }
				}
				
				//xslt option
//				if (isset($_POST['wpdojoloader_xslt'])) {
//					
//					for ($i=count($adminOptions['xslt']);$i>-1;$i--) {
//						unset($adminOptions['xslt'][$i]);	
//					}
//					
//				   $lines = preg_split("/\r\n/", $_POST["wpdojoloader_xslt"]);
//				   $i = 0;
//				   foreach ($lines as $key => $value) {
//				      $adminOptions['xslt'][$i] = ($value);
//				      $i++;
//				    }
//				}
								
								
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
			
			//load require options array into a string 
			$require = "";
			for ($i=0;$i<count($adminOptions['require']);$i++) {
				if ($i == 0) {
					$require = trim($adminOptions['require'][$i]);	
				} else {
					$require .= "\n".trim($adminOptions['require'][$i]);	
				}
			}
			if ($require == "")
			{
				$require .= "Hallo"; //TODO
			}
			
			//load xslt options array into a string 
//			$xslt = "";
//			for ($i=0;$i<count($adminOptions['xslt']);$i++) {
//				if ($i == 0) {
//					$xslt = ($adminOptions['xslt'][$i]);	
//				} else {
//					$xslt .= "\n".($adminOptions['xslt'][$i]);	
//				}
//			}
			
			//load css options array into a string 
			$css = "";
			for ($i=0;$i<count($adminOptions['css']);$i++) {
				if ($i == 0) {
					$css = ($adminOptions['css'][$i]);	
				} else {
					$css .= "\n".($adminOptions['css'][$i]);	
				}
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
			
			<hr />
			
			<h2>Styles</h2>
			<p>
				<textarea type="text" name="wpdojoloader_css" style="width:800px;height:150px;"><?php echo stripslashes($css); ?></textarea>
			</p>
			<hr />
			
			<h2>Dojo Require</h2>
			<p>
				<textarea type="text" name="wpdojoloader_dojorequire" style="width:400px;height:150px;"><?php echo stripslashes($require); ?></textarea>
			</p>
			
			<!--<hr />
			<h2>XSL Transformation</h2>
			<p>
				<textarea type="text" name="wpdojoloader_xslt" style="width:800px;height:300px;"><?php echo stripslashes($xslt); ?></textarea>
			</p>				
			-->
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


?>