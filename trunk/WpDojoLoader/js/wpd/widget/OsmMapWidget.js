/**
    @license
    This file is part of osmdatamanager.

    osmdatamanager is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, only GPLv2.

    osmdatamanager is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with osmdatamanager.  If not, see <http://www.gnu.org/licenses/>.
	
*/

dojo.provide("wpd.widget.OsmMapWidget");
dojo.require("wpd.widget._BaseWidget");
dojo.require("dijit._Templated");
dojo.require("dojo.parser");
dojo.require("trm.Application");
dojo.require("trm.MarkerManager");

dojo.declare("wpd.widget.OsmMapWidget", [wpd.widget._BaseWidget,dijit._Templated], {
	templatePath:    dojo.moduleUrl('wpd.widget', 'OsmMapWidget.html'),
	templateString: '',
	osm_map: null,
	trm_application: null,
	trm_markermanager: null,
	
	postCreate: function() {
		this.inherited(arguments);
		this._initMap();
	},
	_dataOk: function() {
		return true;
	},
	
	_initMap: function() {
		
		//Main Map
		//"map"
		this.osm_map = new OpenLayers.Map(this.osmmap, {  
			controls: [
				//new OpenLayers.Control.KeyboardDefaults(),
				new OpenLayers.Control.MouseDefaults(),
				new OpenLayers.Control.LayerSwitcher(),
				new OpenLayers.Control.PanZoomBar()],
			maxExtent:
                new OpenLayers.Bounds(-20037508.34,-20037508.34,
                                       20037508.34, 20037508.34),
			numZoomLevels: 18,
            maxResolution: 156543,
            units: 'meters',
            projection: "EPSG:4326"} );
		
		//mapnik layer
		var layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
		this.osm_map.addLayer(layerMapnik);
		
		//cyclemap layer		
		var layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
		this.osm_map.addLayer(layerCycleMap);
		this.osm_map.addControl(new OpenLayers.Control.MousePosition());
		
		//markers layer		
		var gl_markers = new OpenLayers.Layer.Markers( "Markers",{projection: new OpenLayers.Projection("EPSG:4326")});
		this.osm_map.addLayer(gl_markers);
		
		this.trm_application = new trm.Application("osmdatamanager",this.osm_map,gl_markers);			
		this.trm_application.clientname = "Korsika2009";
		this.trm_application.targetprefix = "../../gpxtracer/";
		this.trm_application.displayGroupItemsByGroupName("Tour Karte");
									
		this.trm_markermanager = new trm.MarkerManager(this.osm_map);
		this.trm_application.markermanager = this.trm_markermanager;
			
		//if you set a clientname you don't have to log in with a username
		//the mapping between clientname and username is stored in the config.php file
		this.trm_application.clientname = "Demo1";
			
	},
	_setTranslations: function() {
		/*
		this.inherited(arguments);
		
		if (this.nls) {
			if (this.dlgAdmin_lblCrtUser)
				this.dlgAdmin_lblCrtUser.innerHTML = this.nls["createuser"];
				
			if (this.dlgAdmin_lblUsername) 
				this.dlgAdmin_lblUsername.innerHTML = this.nls["username"];
				
			if (this.dlgAdmin_lblPassword1) 
				this.dlgAdmin_lblPassword1.innerHTML = this.nls["password"];
			
			if (this.dlgAdmin_lblPassword2) 
				this.dlgAdmin_lblPassword2.innerHTML = this.nls["password"];
				
			if (this.dlgAdmin_btnCrtUser) 
				this.dlgAdmin_btnCrtUser.containerNode.innerHTML = this.nls["createuser"];			
		}
		*/
	},
	
	_updateOk: function(item) {
		/*
		if (this.application) {
			this.application._updateitem(item);
		}
		this.hide();
		*/
	},
	
	
	getData: function() {
		/*
		var username = document.getElementById(this.dlgAdmin_tbUsername.id).value.trim();
		var pwd1 = document.getElementById(this.dlgAdmin_tbPassword1.id).value.trim();
		var pwd2 = document.getElementById(this.dlgAdmin_tbPassword2.id).value.trim();
		
		var result = {
			"username": username,
			"password1":pwd1,
			"password2":pwd2
		}
	
		return result;
		*/
	},
	
	_okClick: function(e) {
		//this.inherited(arguments);
		/*
		var data = this.getData();
		if (this._dataOk()) {
			
		} else {
			if (this.nls) {
		 		alert(this.nls["entervaliddata"]);
		 	}
		}
		*/
	},
	
		
	show: function(update,root) {
		/*
		this.inherited(arguments);
		if (this.onlyshow) {
			this._setTag(this.dataitem.tagname);
			return;
		}
		
		if (update) {
			this.parentitem = null;
			this._loadData();
		} else {
			this._resetFields();
			this.dataitem = null;
			if (root)
				this.parentitem = null;
		}
		*/
	}
});