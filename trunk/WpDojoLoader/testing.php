<?php

require_once(dirname(__FILE__). '/wpdojoloader.php');

$filename  		= $_GET['filename'];

class WpDojoLoaderTester {
	
	var $dl_dojoLoader;
	
  //constructor
  function WpDojoLoaderTester(){
    $this->dl_dojoLoader = new WpDojoLoader();
    $this->dl_dojoLoader->debugmode = true;
  }
	
  function addSeperator($text)
  {
  	echo "<br>########################&nbsp;".$text."&nbsp;############################<br>";  	
  }
  
  function run($srcfile)
  {
  	
  	$dom   = domxml_open_file($srcfile);
  	$rawdata = $dom->dump_mem(true);
  	$rawdata = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>","",$rawdata);
  	
  	
  	$this->addSeperator("Rawdata");
  	echo "<br>".$rawdata."<br>";
  	
  	$this->addSeperator("Content");
  	$rawdata  = "[dojocontent]".$rawdata."[/dojocontent]";
  	$content = $this->dl_dojoLoader->addContent($rawdata);
  	echo $content;	
  	$this->addSeperator("Ende");
  
  }
  
	
}

?>
<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/dojo/1.4/dojo/dojo.xd.js" djConfig="parseOnLoad:true" 
			type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.4/dijit/themes/tundra/tundra.css"
        />
		
		
		<script type="text/javascript">
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.layout.TabContainer");
			dojo.require("dijit.layout.ContentPane");
			dojo.require("dojox.layout.ResizeHandle");
			dojo.require("dojox.grid.DataGrid");
			dojo.require("dojox.data.CsvStore");
			dojo.require("dojox.widget.FisheyeLite");
			dojo.require("dojox.highlight");
			dojo.require("dojox.layout.ScrollPane");
			dojo.require("dijit.layout.AccordionContainer");
			dojo.require("dijit.form.Button");
			dojo.require("dijit.layout.BorderContainer");
			dojo.require("dijit.TitlePane");
			dojo.require("dojox.html.entities");
			//Load the XML language
			dojo.require("dojox.highlight.languages.xml");
			//Load the HTML language
			dojo.require("dojox.highlight.languages.html");
			dojo.require("dojox.data.XmlStore");
		</script>
		
		<script type="text/javascript">	

			dojo.ready(function(){
				  //dojo.registerModulePath("trm","../../trm");		
				  
				  console.debug("alo");
				  //alert('hi');
				  //var foo=new dojox.data.XmlStore();
				  //init();
				  //dojo.parser.parse();      
				  
			  });
		</script>
		
	</head>

	<body>
		<?php
			$dojotest = new  WpDojoLoaderTester();
			$dojotest->run($filename);
		?>
	</body>

</html>






