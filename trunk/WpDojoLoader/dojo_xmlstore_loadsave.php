<?php 
	header('content-type: text/xml');  
	
	
	if (PHP_VERSION>='5') 
	{
		require_once(dirname(__FILE__).'/domxml-php4-to-php5.php'); 	//Load the PHP5 converter
	 	require_once(dirname(__FILE__).'/xslt-php4-to-php5.php'); 		//Load the PHP5 converter 
	}
	
	/**
	 * this file provides some helper functions to load and 
	 * save data from an dojo xmlstore from or into xml files
	 * //TODO provide some functions to load and save from a database
	 */
	
	//returns the attributevalue from an attributearray
	function getAttributeValue($name, $att)
	{
		foreach($att as $i)
	    	{
		    if($i->name()==$name)
		      return $i->value();       
	    	}	    
	}
	
	//returns a node with a given id
	function &getNodeById (&$node, $id) {
          
          $nodeid = getAttributeValue('ID',$node->attributes());
 	  if ($nodeid == $id)
	    return $node;

          if ($node->type == XML_ELEMENT_NODE) {
                //print ($indent . $node->tagname . "\n");
                $kids = $node->children ();
                $nkids = count ($kids);
                if ($nkids > 0) {
                     
                     for ($i = 0; $i < $nkids; $i++)
		     {
                          $nn = getNodeById($kids[$i], $id);
			  if ($nn != null)
                            return $nn;
                     }
               }
          }
	  return null;
     }
	
 
	
	//$action  		= $_GET['action'];
	$filename  		= $_GET['filename'];
	$contents 		= NULL;
	
	$putdata = fopen("php://input", "r");
	if ($_SERVER['REQUEST_METHOD'] == "PUT")
	{   
	   //add put data into the contents variable
	   $contents = "";	   	   
	   while(!feof($putdata)){
		$contents .= fread($putdata, 8192);
	   };
	   
           //load the source xml file	
	   $srcxml = domxml_open_file($filename);     //the source xml file	
	   $newxml = domxml_open_mem($contents);     //changed content as xml
	   $srcxpath = $srcxml->xpath_new_context();  //xpath context for source xml
	   $newxpath = $newxml->xpath_new_context();  //xpath context for changed content		   
		
	   $id = $newxpath->xpath_eval('./@ID'); //get the id from the changed element
	   $cnt = $newxpath->xpath_eval('.');   
	   
	   //get the node which will be replaced
           $nd1 = getNodeById($srcxml->root() ,$id->nodeset[0]->value);
	   
	   $prnt = $nd1->parent_node();
	   $prnt->replace_child($cnt->nodeset[0],$nd1);
	   
	   //echo $srcxml->dump_mem(true);
	   //echo $filename; 	  	
	   //echo $srcxml->dump_file($filename,false,true);
	   $srcxml->dump_file($filename,false,true);
	}
	
	if (isset($contents ))
	{
		//echo "<test>".html_entity_decode($contents)."</test>";
	} else
	{	
		//TODO extension �berpr�fen !!!!!
		if (isset($filename))
		{
			//echo $filename;
			if (file_exists($filename))
			{
				//load the given file as xml and echo the contents
				$dom   = domxml_open_file($filename);
				echo $dom->dump_mem(true);
			} else
			{
				echo "<error>file does not exist -  $filename</error>";
			}		
		}
	}


?>
