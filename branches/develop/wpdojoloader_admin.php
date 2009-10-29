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


?>