dojo.require("dojo.parser");

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
			
			//init a animation
			jQuery('.wpdojoloader_animation').each(function(){
				var id1 = jQuery(this).attr('id');
				var animation = jQuery(this).attr('animation');
				var dojoanim = null;

				dojo.style(id1, "opacity", "0");
				switch (animation) {
					case "fadein":
						dojoanim = 	dojo.fadeIn({node: id1,duration: 1500});
						break;
					case "fadeout":
						dojoanim = 	dojo.fadeOut({node: id1,duration: 1500});
						break;
				}
				if (dojoanim) {
					dojoanim.play();
				}
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
			
} // end function wpdojoloader_addOnLoad ()

/**
 * 
 */
jQuery(document).ready(function() {
	try {
		if (!dojo)  //if dojo is not initialized this will throw an exception, probably there is a better way ?
			return;
			
		//load some dojo stuff
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
	
		//Load the XML language
		//dojo.require("dojox.highlight.languages.xml");
		//Load the HTML language
		//dojo.require("dojox.highlight.languages.html");
		dojo.addOnLoad(wpdojoloader_addOnLoad);
		
	} catch (e) {
		//console.error(e);
	}
		
});

/**
 * remove tinyMce editor
 * @param {Object} sender
 */
function wpdojoloader_hideTinyMce(sender) {
	var elem = sender;
	if (sender.domNode) //check if sender is a dojo widget 
	 elem = sender.domNode;
	
	var prnts = jQuery(elem).parents('div.entry'); //load the parent div.entry from sender
	var entrydiv = null;
	if (prnts.length > 0) {
		entrydiv = prnts[0];
	}
	
	if (entrydiv) {
		wpdojoloader_printTinyMceStatusMsg(entrydiv, "", "");
		var ta1 = document.getElementById("wpdojoloader_ta_tinymce");
		if (ta1) {
			tinyMCE.get("wpdojoloader_ta_tinymce").remove();	
			ta1.parentNode.removeChild(ta1);
		}
	}
}

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
}

/**
 * 
 * @param {Object} sender
 */
function wpdojoloader_initTinyMce(sender) {
		
	try {
		var prnts = jQuery(sender.domNode).parents('div.entry'); //load the parent div.entry from sender
		var entrydiv = null;
		if (prnts.length > 0) {
			entrydiv = prnts[0];
		}
		
		if (entrydiv) {
			var ta1 = document.getElementById("wpdojoloader_ta_tinymce");
			if (!ta1) {
				//create a new textarea for tinyMCE
				var ta1 = document.createElement("textarea");
				ta1.setAttribute("id", "wpdojoloader_ta_tinymce");
				ta1.setAttribute("class", "wpdojoloader_tinymce");
				ta1.setAttribute("className", "wpdojoloader_tinymce");
			} else {
				//console.debug(ta1);
				return;
			}
			
			//the contentdiv is a child div element from the entrydiv with the class wpdojoloader_dynamiccontent 
			var lst1 = jQuery(".wpdojoloader_dynamiccontent", entrydiv);
			var contentdiv = null;
			if (lst1.length > 0) {
				contentdiv = lst1[0];
			}
			
			if (contentdiv) {
				wpdojoloader_printTinyMceStatusMsg(entrydiv, "edit mode", "color: Black;");
				entrydiv.appendChild(ta1);
				ta1.value = contentdiv.innerHTML;
				
				//init tinyMCE
				tinyMCE.init({
					mode: "exact",
					//mode: "textareas",
					elements : "wpdojoloader_ta_tinymce",
					theme: "advanced",
					theme_advanced_toolbar_location : "top",
    				theme_advanced_toolbar_align : "left",
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
 * 
 * @param {Object} targetfile
 * @param {Object} params
 */
function wpdojoloader_doSavePost(targetfile, params) {
		
	try {
				
		dojo.xhrPost({ //
			// The following URL must match that used to test the server.
			url: targetfile,
			handleAs: "json",
			content: params,
			
			timeout: 5000, // Time in milliseconds
			// The LOAD function will be called on a successful response.
			load: function(response, ioArgs) {
								if (response.message == "Content updated.") {
									var ta1 = document.getElementById("wpdojoloader_ta_tinymce");
									if (ta1) {
										wpdojoloader_hideTinyMce(ta1);
									}
								} else {
									alert("error: " + response.message);
								}
								//alert(response.message);
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
		//load entry div
		var prnts = jQuery(sender.domNode).parents('div.entry'); //load the parent div.entry from sender
		var entrydiv = null;
		if (prnts.length > 0) {
			entrydiv = prnts[0];
		}
		
		//load content div
		var lst1 = jQuery(".wpdojoloader_dynamiccontent", entrydiv);
		var contentdiv = null;
		if (lst1.length > 0) {
			contentdiv = lst1[0];
		}
		
		//load editor
		var ta1 = document.getElementById("wpdojoloader_ta_tinymce");
		if (ta1) {
			contentdiv.innerHTML = tinyMCE.get("wpdojoloader_ta_tinymce").getContent();
			var newContent = "[dojocontent]<dynamic>" + contentdiv.innerHTML + "</dynamic>[/dojocontent]";
			wpdojoloader_printTinyMceStatusMsg(entrydiv, "please wait...", "color:Red;");
			wpdojoloader_doSavePost("wp-content/plugins/wpdojoloader/ajax-save.php",{id: "130", content: newContent});	
		}
		
	} catch(e) {
		console.error(e);
	}	
} // end function wpdojoloader_savePost

