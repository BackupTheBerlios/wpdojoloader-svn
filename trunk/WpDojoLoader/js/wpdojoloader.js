dojo.require("dojo.parser");
//dojo.registerModulePath("wpd", "../../wpd");  //used for custom widgets 
//dojo.registerModulePath("trm", "../../trm");  //used for osmdatamanager data

				
/**
 * this function is called on dojo.addOnLoad
 */
function wpdojoloader_addOnLoad() {
	try {
			//check if dojo is initialized
			if (!dojo.parser) {
					return;
				}
				dojo.parser.parse();
			} catch(e) {
				alert(e);
				//return;
			}
					
			//init a FisheyeLite for a li element
			initFishEye();
			/*
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
			*/
			
			//init a Highlightner			
			initHighlightner();
			/*
			dojo.query(".wpdojoloader_highlight").forEach(function(n){
				
				var code = n.innerHTML;
				
				//replace the <p> tags -> added from wordpress
				//if you want to show <p> tags in the code you have to
				//comment out this line
				code = code.replace(/<p>/g,"");   
				code = code.replace(/<\/p>/g,"");
				code = code.replace(/<br>/g,"");
				code = code.replace(/<br\/>/g,"");
				code = code.replace(/<\/br>/g,"");
				console.debug(code);
				n.innerHTML = code;
				
				//below is the dojo highlightner
				//it's not needed because translation is in xsl
				
				var cd1 = dojox.highlight.processString(code,n.getAttribute("lang"));
				n.innerHTML = cd1.result;
				
			});
			*/
			
			
			//init a animation
			dojo.query(".wpdojoloader_animation").forEach(function(n){
				var animation = n.getAttribute('animation');
				var duration = parseInt(n.getAttribute('duration'));
				var dojoanim = null;
				
				var arg = {
					node: n,
					duration: duration
				}
				
				switch (animation) {
					case "fadein":
						dojoanim = 	dojo.fadeIn(arg);
						break;
					case "fadeout":
						dojoanim = 	dojo.fadeOut(arg);
						break;
				}
				
				if (dojoanim) {
					dojoanim.play();
				}
			});
			
								
			//init a datagrid
			initDatagrid();
			/*
			jQuery('.wpdojoloader_datagrid').each(function(){
			
				//init a store
				var storetype = jQuery(this).parent().attr('storetype');
				var uploaddir = jQuery(this).parent().attr('contentdir');

				//console.debug(this.id);
				//console.debug(this.jsID);
				
				var dataStore = null;
				switch (storetype) {
					case "csv": //create a csv store
						var storeurl = uploaddir + "/" + jQuery(this).parent().attr('filename');
						//console.debug(uploaddir);
						//var storeurl = "wp-content/" + jQuery(this).parent().attr('filename');
						dataStore = new dojox.data.CsvStore({
							url: storeurl,
							label: "Title"
						    //seperator: ";"  //supported by dojo 1.4 ?
						});
						break;
					case "xml": //create a xml datastore
						var storeurl = "wp-content/plugins/wpdojoloader/dojo_xmlstore_loadsave.php?filename=" +uploaddir + "/" + jQuery(this).parent().attr('filename'); 
						dataStore = new dojox.data.XmlStore({ 
							url: storeurl, 
							urlPreventCache: false,
							id:"teststore",
							jsId:"teststore"
							//query: "*",
							//sendQuery: false
						});  
						break;
				}
								
				if (!dataStore) 
					return;
				
				var editable = jQuery(this).parent().attr('editable');
				if (!editable) 
				{
					editable = "false";
				}
				
				//load the field definitions
				var id1 = jQuery(this).attr('id');
				var fields = jQuery(this).parent().attr('fieldnames'); //list of fieldnames, seperated with comma
				if (!fields)
				{
					fields = "name,link";
				}
				
				var layoutGrid = new Array();
				var lst1 = fields.split(","); //split the fieldlist
				if (lst1) {
					for (var i=0;i<lst1.length;i++) {
						var fo = {
							field: lst1[i],
							name: lst1[i],
							width: 'auto',
							editable: editable
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
				*  /
				
				//called when a row is clicked
				dijit.byId(id1).onRowClick = function(event){
					console.debug("onRowClick");
					console.debug(event);
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
				
				//onApplyCellEdit(inValue, inRowIndex, inFieldIndex);
				dijit.byId(id1).onApplyCellEdit = function(inValue, inRowIndex, inFieldIndex){
					console.debug("onApplyCellEdit");
					console.debug(inValue);
					console.debug(inRowIndex);
					console.debug(inFieldIndex);
				}
				
				dataStore._saveCustom = function(saveComplete, saveFailed) {
					alert("savecustom");
				}
				
				dataStore._getPostUrl = function(item) {
					console.debug("_getPostUrl");
					console.debug(item);
				}
				
				dataStore._getPutUrl = function(item) {
					console.debug("_getPutUrl");
					console.debug(item);
				}
				
				
				dataStore._getFetchUrl = function(item) {
					console.debug("_getFetchUrl");
					console.debug(item);
					if (item != null)
					{
						console.debug("hallo");
						return this.url;
					} else
					{
						return "wpdojoloader/#";
					}
				}
				
				/*
				console.debug(dataStore.fetchItemByIdentity);
				dataStore.fetchItemByIdentity = function(keywordArgs)
				{
					console.debug("fetchItemByIdentity");
					console.debug(keywordArgs);
					console.debug(this.data);
					
				}
				*  /
				
				dijit.byId(id1).onApplyEdit = function(event){
					//log.error;
					console.debug("onApplyEdit")
					console.debug(event);
					//alert(event);
					//dataStore.save();
				}
			
				/* you can use this for custom row styleing
				dijit.byId(id1).onStyleRow = function(inrow){
				}
				*  /
				
				dijit.byId(id1).setStructure(layoutGrid);	
				//dijit.byId(id1).setStore(dataStore, { query: "*" ,queryOptions: {ignoreCase: true}});
				dijit.byId(id1).setStore(dataStore);
				dijit.byId(id1).startup();
			}); //end datagrid
			*/
			
			//init a osm map
			dojo.query(".wpdojoloader_osmmap").forEach(function(n){
				console.debug(n);
				dojo.require("wpd.widget._BaseWidget");
				dojo.require("wpd.widget.OsmMapWidget");
				var map = new wpd.widget.OsmMapWidget({}, n);
				
				console.debug(map);
			});
			
} // end function wpdojoloader_addOnLoad ()

/**
 * 
 */
jQuery(document).ready(function() {
	try {
		if (!dojo)  //if dojo is not initialized this will throw an exception, probably there is a better way ?
			return;
		
		/*
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
		*/
		dojo.addOnLoad(wpdojoloader_addOnLoad);
		
	} catch (e) {
		//console.error(e);
	}
		
});


/**
 * remove tinyMce editor
 * @param {Object} sender
 */
function wpdojoloader_hideTinyMce() {
	
	if (tinyMCE.activeEditor) {
		
		//load the parent div.wpdojoloader
		var prnt = document.getElementById(tinyMCE.activeEditor.editorContainer);
		if (prnt) {
			var prnts = jQuery(prnt).parents('div.wpdojoloader');
			if (prnts.length > 0) {
				wpdojoloader_printTinyMceStatusMsg(prnts[0], "", "");	
			}
		}
		
		//hide the tinyMCE editor
		tinyMCE.get(tinyMCE.activeEditor.editorId).remove();
	}
	
} //end function wpdojoloader_hideTinyMce


/**
 * print a message in the wpdojoloader_tinymcestatus span element 
 * @param {Object} parentelement
 * @param {Object} message
 * @param {Object} style
 */
function wpdojoloader_printTinyMceStatusMsg(parentelement, message, style) {
	var lst1 = jQuery(".wpdojoloader_tinymcestatus", parentelement);
	var contentdiv = null;
	if (lst1.length > 0) {
		contentdiv = lst1[0];
		contentdiv.innerHTML = message;
		contentdiv.setAttribute("style",style);
	}
} // end function wpdojoloader_printTinyMceStatusMsg 


/**
 * 
 * @param {Object} sender
 */
function wpdojoloader_initTinyMce(sender) {
		
	try {
		if (tinyMCE.activeEditor) {
			return;
		}
		
		var prnts = jQuery(sender.domNode).parents('div.wpdojoloader'); //load the parent div.wpdojoloader from sender
		var entrydiv = null;
		if (prnts.length > 0) {
			entrydiv = prnts[0];
		}
	
		if (entrydiv) {
			//the contentdiv is a child div element from the entrydiv with the class wpdojoloader_dynamiccontent 
			var lst1 = jQuery(".wpdojoloader_dynamiccontent", entrydiv);
			var contentdiv = null;
			if (lst1.length > 0) {
				contentdiv = lst1[0];
			}
			if (contentdiv) {
				wpdojoloader_printTinyMceStatusMsg(entrydiv, "edit mode", "color: Black;");
				var elemid = contentdiv.getAttribute("id");
				//init tinyMCE
				tinyMCE.init({
					mode: "exact",
					//mode: "textareas",
					elements : elemid,
					theme: "advanced",
					theme_advanced_toolbar_location : "top",
    				theme_advanced_toolbar_align : "left",
					height: 480,
					oninit: function(){
						//alert('test');
					}
				});
			}			
		}
	} catch(e) {
		console.error(e);
	}
} // end function wpdojoloader_initTinyMce

/**
 * execute dojo.xhrPost to store the data
 * @param {Object} targetfile
 * @param {Object} params
 */
function wpdojoloader_doSavePost(targetfile, params) {
		
	try {
				
		dojo.xhrPost({ //
			// The following URL must match that used to test the server.
			url: targetfile,
			handleAs: "text",
			content: params,
			
			timeout: 5000, // Time in milliseconds
			// The LOAD function will be called on a successful response.
			load: function(response, ioArgs) {
							
						if (response.message == "Content updated.") {
							wpdojoloader_hideTinyMce();
						} else {
							alert("error: " + response.message);
						}
					},
			sync: true,
			// The ERROR function will be called in an error case.
			error: function(response, ioArgs){ //
				//alert(response);
				console.error("HTTP status code: ", ioArgs.xhr.status); //
				console.error(response);
				return response; //
			}
		});
	} 
	catch (e) {
		console.error(e);
	}
		
} //end function wpdojoloader_doSavePost

/**
 * 
 * @param {Object} sender
 */
function wpdojoloader_savePost(sender){
	try {
		if (! tinyMCE.activeEditor) {
			return;
		}
		
		//load entry div
		var prnts = jQuery(sender.domNode).parents('div.wpdojoloader'); //load the parent div.wpdojoloader from sender
		var entrydiv = null;
		if (prnts.length > 0) {
			entrydiv = prnts[0];
		}
		
		if (entrydiv) {
			//load content div
			var id1 = entrydiv.getAttribute("wpid");
			var lst1 = jQuery(".wpdojoloader_dynamiccontent", entrydiv);
			var contentdiv = null;
			var cntid = "";
			if (lst1.length > 0) {
				contentdiv = lst1[0];
				cntid = contentdiv.getAttribute("id");
			}
						
			contentdiv.innerHTML = tinyMCE.get(cntid).getContent();
			var newContent = "[dojocontent]<dynamic>" + contentdiv.innerHTML + "</dynamic>[/dojocontent]";
			wpdojoloader_printTinyMceStatusMsg(entrydiv, "please wait...", "color:Red;");
			wpdojoloader_doSavePost("wp-content/plugins/wpdojoloader/ajax-save.php", {
				id: id1,
				content: newContent
			});
			
		}
	} catch(e) {
		console.error(e);
	}	
} // end function wpdojoloader_savePost

function initHighlightner()
{
	dojo.query(".wpdojoloader_highlight").forEach(function(n){
	
	var code = n.innerHTML;
	
	if (n.getAttribute("initialized") == 'true')
		return;
	
	//replace the <p> tags -> added from wordpress
	//if you want to show <p> tags in the code you have to
	//comment out this line
	code = code.replace(/<p>/g,"");   
	code = code.replace(/<\/p>/g,"");
	code = code.replace(/<br>/g,"");
	code = code.replace(/<br\/>/g,"");
	code = code.replace(/<\/br>/g,"");
	console.debug(code);
	n.innerHTML = code;
	
	//below is the dojo highlightner
	//it's not needed because translation is in xsl
	/**/
	var cd1 = dojox.highlight.processString(code,n.getAttribute("lang"));
	n.innerHTML = cd1.result;
	n.setAttribute("initialized",'true');
	});
}

function initGridAfterLoad()
{
	//dojo.parser.parse();
	initDatagrid();
}

function initDatagrid()
{
	jQuery('.wpdojoloader_datagrid').each(function(){
		
		//init a store
		var storetype = jQuery(this).parent().attr('storetype');
		var uploaddir = jQuery(this).parent().attr('contentdir');
				
		var dataStore = null;
		switch (storetype) {
			case "csv": //create a csv store
				var storeurl = uploaddir + "/" + jQuery(this).parent().attr('filename');
				//console.debug(uploaddir);
				//var storeurl = "wp-content/" + jQuery(this).parent().attr('filename');
				dataStore = new dojox.data.CsvStore({
					url: storeurl,
					label: "Title"
				    //seperator: ";"  //supported by dojo 1.4 ?
				});
				break;
			case "xml": //create a xml datastore
				var storeurl = "wp-content/plugins/wpdojoloader/dojo_xmlstore_loadsave.php?filename=" +uploaddir + "/" + jQuery(this).parent().attr('filename'); 
				dataStore = new dojox.data.XmlStore({ 
					url: storeurl, 
					urlPreventCache: false,
					id:"teststore",
					jsId:"teststore"
					//query: "*",
					//sendQuery: false
				});  
				break;
		}
						
		if (!dataStore) 
			return;
		
		var editable = jQuery(this).parent().attr('editable');
		if (!editable) 
		{
			editable = "false";
		}
		
		//load the field definitions
		var id1 = jQuery(this).attr('id');
		var fields = jQuery(this).parent().attr('fieldnames'); //list of fieldnames, seperated with comma
		if (!fields)
		{
			fields = "name,link";
		}
		
		var layoutGrid = new Array();
		var lst1 = fields.split(","); //split the fieldlist
		if (lst1) {
			for (var i=0;i<lst1.length;i++) {
				var fo = {
					field: lst1[i],
					name: lst1[i],
					width: 'auto',
					editable: editable
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
			console.debug("onRowClick");
			console.debug(event);
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
		
		//onApplyCellEdit(inValue, inRowIndex, inFieldIndex);
		dijit.byId(id1).onApplyCellEdit = function(inValue, inRowIndex, inFieldIndex){
			console.debug("onApplyCellEdit");
			console.debug(inValue);
			console.debug(inRowIndex);
			console.debug(inFieldIndex);
		}
		
		dataStore._saveCustom = function(saveComplete, saveFailed) {
			alert("savecustom");
		}
		
		dataStore._getPostUrl = function(item) {
			console.debug("_getPostUrl");
			console.debug(item);
		}
		
		dataStore._getPutUrl = function(item) {
			console.debug("_getPutUrl");
			console.debug(item);
		}
		
		
		dataStore._getFetchUrl = function(item) {
			console.debug("_getFetchUrl");
			console.debug(item);
			if (item != null)
			{
				console.debug("hallo");
				return this.url;
			} else
			{
				return "wpdojoloader/#";
			}
		}
		
		/*
		console.debug(dataStore.fetchItemByIdentity);
		dataStore.fetchItemByIdentity = function(keywordArgs)
		{
			console.debug("fetchItemByIdentity");
			console.debug(keywordArgs);
			console.debug(this.data);
			
		}
		*/
		
		dijit.byId(id1).onApplyEdit = function(event){
			//log.error;
			console.debug("onApplyEdit")
			console.debug(event);
			//alert(event);
			//dataStore.save();
		}
	
		/* you can use this for custom row styleing
		dijit.byId(id1).onStyleRow = function(inrow){
		}
		*/
		
		dijit.byId(id1).setStructure(layoutGrid);	
		//dijit.byId(id1).setStore(dataStore, { query: "*" ,queryOptions: {ignoreCase: true}});
		dijit.byId(id1).setStore(dataStore);
		dijit.byId(id1).startup();
	}); //end datagrid
} //initDatagrid();

function initFishEye()
{
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
} //initFishEye 





