/**
 * this file contains a custom plugin for tinymce
 * it allows you to insert templates for wpdojoloader into the editor
 */

(function() {
	tinymce.PluginManager.requireLangPack('wpdojoloader');

	tinymce.create('tinymce.plugins.WpDojoLoader', {
		plugin_url: "",
		createControl: function(n, cm) { //called when a control is created
			switch (n) {
				case 'wpdojoloader_plugin':  
	                var c = cm.createSplitButton('wpdojoloader_splitbutton', {
	                    title : 'wpdojoloader.dojowidgets',
	                    image : this.plugin_url + '/img/dojo_widgets.gif',
						onclick : function() { 
                        	//alert('Button was clicked.');
                   		}

	                });
	
	                c.onRenderMenu.add(function(c, m) {
	                    m.add({title : 'wpdojoloader.wpdojoloader', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
						
						//dojocontent template
	                    m.add({title : 'wpdojoloader.dojocontent', onclick : function() {
							tinyMCE.execCommand('mceInsertContent', false, "[dojocontent][/dojocontent]");
	                    }});
						
						m.add({title : 'wpdojoloader.dojowidgets', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
						
						//tabcontainer template
	                    m.add({title : 'wpdojoloader.tabcontainer', onclick : function() {
							tinyMCE.execCommand('mceInsertContent', false, "&lt;tabcontainer&gt;&lt;/tabcontainer&gt;");
	                    }});
	
						//contentpane template
	                    m.add({title : 'wpdojoloader.contentpane', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;contentpane title=\"A Contentpane\"&gt;Hello World&lt;/contentpane&gt;");
	                    }});
						
						//datagrid template
						m.add({title : 'wpdojoloader.datagrid', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;datagrid storetype=\"csv\" structurename=\"gridstructure_1\" filename=\"filename1.csv\"&gt;&lt;/datagrid&gt;");
	                    }});
						
						//accordion container template
						m.add({title : 'wpdojoloader.accordioncontainer', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;accordioncontainer&gt;&lt;/accordioncontainer&gt;");
	                    }});
						
						//accordionpane template
						m.add({title : 'wpdojoloader.accordionpane', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;accordionpane selected=\"true\" title=\"A AccordionPane\"&gt;Hello World&lt;/accordionpane&gt;");
	                    }});
						
						//fisheye template
						m.add({title : 'wpdojoloader.fisheye', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;fisheye&gt; <ul><li> Hello World</li></ul> &lt;/fisheye&gt;");
	                    }});
						
						//scrollpane template
						m.add({title : 'wpdojoloader.scrollpane', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;scrollpane orientation=\"vertical\" style=\"width:100%; height:150px;border:solid 1px black;\"&gt;Hello World&lt;/scrollpane&gt;");
	                    }});
						
						//box template
						m.add({title : 'wpdojoloader.box', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;box class=\"wpdojoloader_box\" style=\"width: 150px; height: 150px;\"&gt;Hello World&lt;/box&gt;");
	                    }});
						
						//border container
						m.add({title : 'wpdojoloader.bordercontainer', onclick : function() {
	                        tinyMCE.execCommand('mceInsertContent', false, "&lt;bordercontainer style=\"width:100%;height:300px;border: solid 1px black\" design=\"sidebar\" &gt;&lt;/bordercontainer&gt;");
	                    }});
						
	              });
	
	              // Return the new menubutton instance
	              return c;
	        }
	
	        return null;
		},
		
		//init the plugin	
		init : function(ed, url) {
				this.plugin_url = url;
		}
	});
	tinymce.PluginManager.add('wpdojoloader_plugin', tinymce.plugins.WpDojoLoader);
	
})();

