jQuery(document).ready(function() {
	try {
		if (!dojo)  //if dojo is not initialized this will throw an exception, probably there is a better way ?
			return;
		
		//load some dojo stuff
		dojo.require("dijit.form.TextBox");
		dojo.require("dojo.parser");
		dojo.require("dijit.layout.TabContainer");
		dojo.require("dijit.layout.ContentPane");
		dojo.require("dojox.layout.ResizeHandle"); 
		dojo.require("dojox.grid.DataGrid");
		dojo.require("dojox.data.CsvStore");
		dojo.require("dojox.widget.FisheyeLite");
		dojo.require("dojox.highlight");
		dojo.require("dojox.layout.ScrollPane");
		dojo.require("dijit.layout.AccordionContainer");

		//Load the XML language
		dojo.require("dojox.highlight.languages.xml")
		//Load the HTML language
		dojo.require("dojox.highlight.languages.html")

				
		dojo.addOnLoad(function(){
			dojo.parser.parse();
			
			//init a FisheyeLite for a li element			
			dojo.query(".wpdojoloader_fisheyelite li").forEach(function(n){
				new dojox.widget.FisheyeLite({},n);
			});
			
			//init a FisheyeLite for a img element
			dojo.query(".wpdojoloader_fisheyelite img").forEach(function(n){
				new dojox.widget.FisheyeLite({properties: {
										          height:1.75,
										          width:1.75
										        }
										      },n);
			});
			
			//init a Highlightner			
			dojo.query(".wpdojoloader_highlight").forEach(function(n){
				var cd1 = dojox.highlight.processString(n.innerHTML,n.getAttribute("lang"));
				n.innerHTML = cd1.result;
			});
					
			//init a datagrid
			jQuery('.wpdojoloader_datagrid').each(function(){
			
				//init a store
				var storetype = jQuery(this).parent().attr('storetype');
				var uploaddir = jQuery(this).parent().attr('uploaddir'); 
				var dataStore = null;
				switch (storetype) {
					case "csv": //create a csv store
						//var storeurl = uploaddir + "/" + jQuery(this).parent().attr('filename');
						//console.debug(uploaddir);
						var storeurl = "wp-content/" + jQuery(this).parent().attr('filename');
						dataStore = new dojox.data.CsvStore({
							url: storeurl,
							label: "Title"
						//seperator: ";"  //supported by dojo 1.4 ?
						});
						break;
				}
				
								
				if (!dataStore) 
					return;
				
				var id1 = jQuery(this).attr('id');
				var fields = jQuery(this).parent().attr('fieldnames'); //list of fieldnames, seperated with comma
				var layoutGrid = new Array();
				var lst1 = fields.split(","); //split the fieldlist
				if (lst1) {
					for (var i=0;i<lst1.length;i++) {
						var fo = {
							field: lst1[i],
							name: lst1[i]
						}
						layoutGrid.push(fo); 
					}
				} 
								
				//called when a cell is clicked				
				/*
				dijit.byId(id1).onCellClick = function(event) {
					//console.debug(event.cellNode.textContent);
					var content = event.cellNode.textContent;  //content of the cell
					//this regexp checks if the content is valid url
					var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
					if (regexp.test(content)) { 
						window.open(content);  //open the url in a new window
					}	
				}
				*/
				
				//called when a row is clicked
				dijit.byId(id1).onRowClick = function(event){					
					//check all cells if there is a valid url and open it
					dojo.query("[role=gridcell]", event.rowNode).forEach(
					    function(element) {
					        //console.debug(element.innerHTML);
							var content = element.innerHTML;
							var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
							if (regexp.test(content)) { 
								window.open(content);  //open the url in a new window
							}
					    }
					);
				}
				
				
				/* you can use this for custom row styleing
				dijit.byId(id1).onStyleRow = function(inrow){
				}
				*/
				
				dijit.byId(id1).setStructure(layoutGrid);	
				dijit.byId(id1).setStore(dataStore, {/* query  name: "*" */}, {ignoreCase: true});
			});
		});
		
	} catch (e) {
		//console.error(e);
	}
		
});
