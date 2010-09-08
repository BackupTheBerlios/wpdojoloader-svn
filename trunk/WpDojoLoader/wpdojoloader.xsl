<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
<xsl:output method="html" version="4.01" omit-xml-declaration="yes" indent="no" encoding="UTF-8"/>
	
	<!-- the main template -->
	<!--<xsl:template match="wpcontentroot">  
		<div class="claro wpdojoloader">
      <xsl:apply-templates/>
		</div>
	</xsl:template>-->
	
  <!-- 
  	this template calls the different templates by name  	
   -->
  <xsl:template match="/">
    <xsl:variable name="templatename" select="name()"></xsl:variable>

    <div class="claro wpdojoloader"> 
      <xsl:apply-templates select="/root/data"/>
    </div>  
    <!--<xsl:call-template name="$templatename"></xsl:call-template>-->
  </xsl:template>
      
  <xsl:template match="script">
  	<xsl:if test="@type = 'script'">
  		<script type="text/javascript"><xsl:text disable-output-escaping="yes">var gl_ssid="</xsl:text><xsl:value-of select="//options/option[@name='ssid']"></xsl:value-of><xsl:text>"</xsl:text><xsl:value-of disable-output-escaping="yes" select="."></xsl:value-of></script>
  	</xsl:if>		
  </xsl:template> 

  <!-- 
  	template to call imported templates
   -->
  <xsl:template match="calltemplate">      
      <xsl:variable name="tplname" select="@name"></xsl:variable>
      <xsl:variable name="uniqueid">
      	<xsl:choose>
      		<xsl:when test="@uid"><xsl:value-of select="@uid"></xsl:value-of></xsl:when>
      		<xsl:otherwise><xsl:value-of select="/root/templates/template[@name = $tplname]/@uid"></xsl:value-of></xsl:otherwise>
      	</xsl:choose>
      </xsl:variable>
          
      <xsl:apply-templates select="/root/templates/template[@name = $tplname]">
      	<xsl:with-param name="uid">
      		<xsl:value-of select="$uniqueid"></xsl:value-of>
      	</xsl:with-param>
      	<xsl:with-param name="param1">
      		<xsl:value-of select="@param1"></xsl:value-of>
      	</xsl:with-param>
      	<xsl:with-param name="param2">
      		<xsl:value-of select="@param2"></xsl:value-of>
      	</xsl:with-param>
      	<xsl:with-param name="param3">
      		<xsl:value-of select="@param3"></xsl:value-of>
      	</xsl:with-param>
      </xsl:apply-templates>            
  </xsl:template>
  
	
	<!-- a dojo contentpane -->
	<xsl:template match="contentpane">
		<xsl:param name="uid"></xsl:param>
		
		<div dojoType="dijit.layout.ContentPane" class="claro wpdojoloader_contentpane">
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" >
				<xsl:with-param name="uid"><xsl:value-of select="$uid"></xsl:value-of></xsl:with-param>
			</xsl:call-template>
			
			<xsl:call-template name="textout">
     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
    		</xsl:call-template>
	    				
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo tabcontainer -->
	<xsl:template match="tabcontainer">
		<div dojoType="dijit.layout.TabContainer" class="claro wpdojoloader_tab">
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes">
				<xsl:with-param name="defaultstyle">width:100%;height:200px;</xsl:with-param> <!-- set the default style -->
			</xsl:call-template>  
		
			<xsl:call-template name="textout">
     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
    		</xsl:call-template>	
    		<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo datagrid -->
	<xsl:template match="datagrid">
		<div>
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" />
				
			<xsl:variable name="contentdir">  
            	<xsl:value-of select="//option[@name='datagridcontent']" />  
            </xsl:variable>
            
            <xsl:attribute name="contentdir"><xsl:value-of select="$contentdir"></xsl:value-of></xsl:attribute>
				
			<xsl:variable name="gridid">
				<xsl:value-of select="generate-id()"></xsl:value-of>
			</xsl:variable>				
			
			<div dojoType="dojox.grid.DataGrid" jsid="{$gridid}" id="{$gridid}" rowsPerPage="40" class="claro wpdojoloader_datagrid">
				
				<!-- add all attributes from xml to html -->
				<xsl:call-template name="allattributes">
					<xsl:with-param name="defaultstyle">width: 100%; height: 300px;</xsl:with-param> <!-- set the default style -->
				</xsl:call-template>  
			
				<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
				<xsl:apply-templates/>
			</div>
			<xsl:if test="//options/option[@name = 'ajaxload'] = 'true'">
				<script type="text/javascript">	
					<xsl:text disable-output-escaping="yes"><![CDATA[initGridAfterLoad();]]></xsl:text>
				</script>
			</xsl:if>
		</div>
	</xsl:template>
	
	
	<!-- a wordpress post -->
	<xsl:template match="post">
		<div class="wpdojoloader_post">
			
			<xsl:variable name="postid">   <!-- set a variable with the post id -->
            	<xsl:value-of select="@id"/>  
            </xsl:variable>
							
			<xsl:for-each select="//postcontent[@id=$postid]" > <!-- load the post content -->
			 	  <xsl:value-of select="." disable-output-escaping="yes" />	  
			</xsl:for-each>
		
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a wordpress page -->
	<xsl:template match="page">
		<div class="wpdojoloader_page">
			
			<xsl:variable name="pageid">   <!-- set a variable with the post id -->
            	<xsl:value-of select="@id"/>  
            </xsl:variable>
							
			<xsl:for-each select="//pagecontent[@id=$pageid]" > <!-- load the post content -->
			 	  <xsl:value-of select="." disable-output-escaping="yes" />	  
			</xsl:for-each>
		
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo fisheye -->
	<xsl:template match="fisheye">
		<div class="wpdojoloader_fisheyelite">
			<!-- <xsl:call-template name="addhtml" />  -->
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
		<xsl:if test="//options/option[@name = 'ajaxload'] = 'true'">
			<script type="text/javascript">	
				<xsl:text disable-output-escaping="yes"><![CDATA[initFishEye();]]></xsl:text>
			</script>
		</xsl:if>
	</xsl:template>
	
	
	<!-- a link -->
	<xsl:template match="link">
		<a>
			<xsl:variable name="wpurl">   <!-- set a variable with the post id -->
            	<xsl:value-of select="//option[@name='wpurl']" />  
            </xsl:variable>
			
			<xsl:choose>  
            	<xsl:when test="@type='cat'">
            		<xsl:attribute name="href"><xsl:value-of select="$wpurl" />/?cat=<xsl:value-of select="@id" /></xsl:attribute>	 		  	  
            	</xsl:when>
				<xsl:when test="@type='post'">
            		<xsl:attribute name="href"><xsl:value-of select="$wpurl" />/?p=<xsl:value-of select="@id" /></xsl:attribute>	 		  	  
            	</xsl:when>
				<xsl:when test="@type='page'">
            		<xsl:attribute name="href"><xsl:value-of select="$wpurl" />/?page_id=<xsl:value-of select="@id" /></xsl:attribute>	 		  	  
            	</xsl:when>
				<xsl:otherwise>
                	<xsl:attribute name="href"><xsl:value-of select="@id" /></xsl:attribute>
					<xsl:attribute name="target">_blank</xsl:attribute>
                </xsl:otherwise>   
			</xsl:choose>
		
			<xsl:value-of select="text()[1]" />
		</a>
	</xsl:template>
	
	
	<!-- a dojo scrollpane -->
	<xsl:template match="scrollpane">
		<div dojoType="dojox.layout.ScrollPane" >
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes">
				<xsl:with-param name="defaultstyle">width: 200px; height: 100px; border: solid 1px black;</xsl:with-param> <!-- set the default style -->
			</xsl:call-template>  
		
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo accordioncontainer  => the old one 
	
	<xsl:template match="accordioncontainer">
    	<div dojoType="dijit.layout.AccordionContainer">	
			
			<xsl:call-template name="allattributes">
				<xsl:with-param name="defaultstyle">width: 100%;height: 300px;</xsl:with-param> 
			</xsl:call-template>  
		
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	-->
	
	<!-- a dojo accordioncontainer (new) -->
	<xsl:template match="accordioncontainer">
    	<xsl:param name="uid"></xsl:param>
		
		<xsl:variable name="currentuid">
			<xsl:choose>
				<xsl:when test="$uid != ''"><xsl:value-of select="$uid"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@uid"/></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
    	
    	<xsl:variable name="accid">djacc_<xsl:value-of select="$currentuid" /><xsl:value-of select="generate-id(.)"></xsl:value-of></xsl:variable>    	
    	<div dojoType="dijit.layout.AccordionContainer">	
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes">
				<xsl:with-param name="defaultstyle">width: 100%;height: 300px;</xsl:with-param> <!-- set the default style -->
			</xsl:call-template>  
			
			<xsl:choose>
				<xsl:when test="@count">
					<xsl:call-template name="loop" >
						<xsl:with-param name="count"><xsl:value-of select="@count" /></xsl:with-param>
						<xsl:with-param name="templatename">accordionpane</xsl:with-param>
						<xsl:with-param name="prntid" select="$accid"></xsl:with-param>
						<xsl:with-param name="uid" select="$currentuid"></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="./accordionpane">
						<xsl:variable name="tid">
							<xsl:value-of select="$accid" /><xsl:value-of select="position()" />
						</xsl:variable>
				
						<xsl:call-template name="accordionpane" > 
							<xsl:with-param name="accid"><xsl:value-of select="$accid" /></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>	
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	<!-- 
		<xsl:template name="jqueryaccordion">
		<xsl:param name="prntid"></xsl:param>
		<xsl:param name="count"></xsl:param>
		<xsl:param name="uid"></xsl:param>
		
		<h3><a href="#">
				<xsl:choose>
					<xsl:when test="$count != ''">
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue">###title_<xsl:value-of select="$uid"/>_<xsl:value-of select="$count"/>###</xsl:with-param>        
			    		</xsl:call-template>	
			    		<xsl:apply-templates/>	
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</a></h3>
		<div>	
			<p>
				<xsl:choose>
					<xsl:when test="$count != ''">
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue">###text_<xsl:value-of select="$uid"/>_<xsl:value-of select="$count"/>###</xsl:with-param>        
			    		</xsl:call-template>	
			    		<xsl:apply-templates/>	
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
			    			</xsl:call-template>	
			    		<xsl:apply-templates/>
					</xsl:otherwise>	
				</xsl:choose>
				
    		</p>
		</div>
	</xsl:template>
	
	
	 -->
	
	
	<!-- a dojo accordionpane -->
	<xsl:template name="accordionpane" match="accordionpane">
		<xsl:param name="prntid"></xsl:param>
		<xsl:param name="count"></xsl:param>
		<xsl:param name="uid"></xsl:param>
	     
		<div dojoType="dijit.layout.AccordionPane">				
				<!-- add all attributes from xml to html -->
				<xsl:call-template name="allattributes" /> 
				
				<xsl:attribute name="title">
					<xsl:choose>
						<xsl:when test="$count != ''">
							<xsl:call-template name="textout">
				     	 		<xsl:with-param name="textvalue">###text_<xsl:value-of select="$uid"/>_<xsl:value-of select="$count"/>###</xsl:with-param>        
				    			<xsl:with-param name="istitle">true</xsl:with-param>
				    		</xsl:call-template>	
				    		<xsl:apply-templates/>	
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="textout">
				     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
				    			</xsl:call-template>	
				    		<xsl:apply-templates/>
						</xsl:otherwise>	
					</xsl:choose>
				</xsl:attribute>
				
			 	 <xsl:choose>
					<xsl:when test="$count != ''">
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue">###text_<xsl:value-of select="$uid"/>_<xsl:value-of select="$count"/>###</xsl:with-param>        
			    		</xsl:call-template>	
			    		<xsl:apply-templates/>	
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
			    			</xsl:call-template>	
			    		<xsl:apply-templates/>
					</xsl:otherwise>	
				</xsl:choose>
		</div>
	</xsl:template>
	
	
	<!-- a box -->
	<xsl:template match="box">
		<div>
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" /> <!--
				<xsl:with-param name="defaultstyle">width: 100%;height: 300px;</xsl:with-param>
			</xsl:call-template>  -->
			
			<xsl:if test="(@animation)">
				<xsl:attribute name="class">wpdojoloader_animation <xsl:value-of select="@class" /></xsl:attribute>
				<xsl:if test="@animation='fadein'">
					<xsl:attribute name="style">opacity:0; <xsl:value-of select="@style" /></xsl:attribute>	  	  
				</xsl:if>
			</xsl:if>
			
			<xsl:choose>  
            	<xsl:when test="@duration">
            		<xsl:attribute name="duration"><xsl:value-of select="@duration" /></xsl:attribute>	  	  
            	</xsl:when>  
                         
                <xsl:otherwise>
                	<xsl:attribute name="duration">3000</xsl:attribute>
                </xsl:otherwise>  
            </xsl:choose>  

			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    	</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo bordercontainer -->
	<xsl:template match="bordercontainer">
		<xsl:param name="uid"></xsl:param>
	
		<div dojoType="dijit.layout.BorderContainer" >	
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" > 
				<xsl:with-param name="defaultstyle">width:100%; heigth:200px;</xsl:with-param>
				<xsl:with-param name="uid" select="$uid"></xsl:with-param>
			</xsl:call-template>
		
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo titlepane -->
	<xsl:template match="titlepane">
		<div dojoType="dijit.TitlePane" >
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" > 
				<xsl:with-param name="defaultstyle">width:100%;</xsl:with-param>
			</xsl:call-template>
		
			<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    		</xsl:call-template>
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	<!-- 
		JQuery UI  
		XSL-Templates to create query-ui Widgets
	-->
	
	<!-- JQuery Tabs -->
	<xsl:template match="jquerytabcontainer">
		<xsl:param name="uid"></xsl:param>
		<xsl:variable name="tabid">jqtab_<xsl:value-of select="$uid" /><xsl:value-of select="generate-id(.)"></xsl:value-of></xsl:variable>
		
		<script type="text/javascript">
			<xsl:text disable-output-escaping="yes"><![CDATA[jQuery(document).ready(function(){jQuery("#]]></xsl:text><xsl:value-of select="$tabid"></xsl:value-of><xsl:text disable-output-escaping="yes"><![CDATA[").tabs();});]]></xsl:text>
		</script>
		
		<div id="{$tabid}">
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" > 
				<xsl:with-param name="defaultstyle">width:100%;</xsl:with-param>
			</xsl:call-template>
			
			<ul>
				<xsl:for-each select="./jquerytab">
					<li>
						<a>
							<xsl:attribute name="href">#<xsl:value-of select="$tabid" /><xsl:value-of select="position()"></xsl:value-of></xsl:attribute>
							<xsl:value-of select="@title"></xsl:value-of>
						</a>
					</li>
				</xsl:for-each>
			</ul>
			
			<xsl:for-each select="./jquerytab">
				<xsl:variable name="tid">
					<xsl:value-of select="$tabid" /><xsl:value-of select="position()" />
				</xsl:variable>
		
				<xsl:call-template name="jquerytab" > 
					<xsl:with-param name="tid"><xsl:value-of select="$tid" /></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
			
		</div>
		
		<xsl:apply-templates/>		
	</xsl:template>
	
	<xsl:template name="jquerytab">
		<xsl:param name="tid"></xsl:param>
		
		<!--   <xsl:variable name="tid">
			<xsl:value-of select="$tabid" /><xsl:value-of select="position()" />
		</xsl:variable>
		-->
		
		<div id="{$tid}">
			<p>
				<xsl:call-template name="textout">
	     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
	    			</xsl:call-template>	
	    		<xsl:apply-templates/>
    		</p>
		</div>
	</xsl:template>
	
	<!-- JQuery Accordion -->
	<xsl:template match="jqueryaccordioncontainer">
		<xsl:param name="uid"></xsl:param>
		<xsl:variable name="currentuid">
			<xsl:choose>
				<xsl:when test="$uid != ''"><xsl:value-of select="$uid"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@uid"/></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="accid">jqacc_<xsl:value-of select="$currentuid" /><xsl:value-of select="generate-id(.)"></xsl:value-of></xsl:variable>
		
		<script type="text/javascript">	
			<xsl:text disable-output-escaping="yes"><![CDATA[jQuery(document).ready(function(){jQuery("#]]></xsl:text><xsl:value-of select="$accid"></xsl:value-of><xsl:text disable-output-escaping="yes"><![CDATA[").accordion();});]]></xsl:text>
		</script>
		<div id="{$accid}">
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" > 
				<xsl:with-param name="defaultstyle">width:100%;</xsl:with-param>
			</xsl:call-template>
									
			<xsl:choose>
				<xsl:when test="@count">
					<xsl:call-template name="loop" >
						<xsl:with-param name="count"><xsl:value-of select="@count" /></xsl:with-param>
						<xsl:with-param name="templatename">jqueryaccordion</xsl:with-param>
						<xsl:with-param name="prntid" select="$accid"></xsl:with-param>
						<xsl:with-param name="uid" select="$currentuid"></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="./jqueryaccordion">
						<xsl:variable name="tid">
							<xsl:value-of select="$accid" /><xsl:value-of select="position()" />
						</xsl:variable>
				
						<xsl:call-template name="jqueryaccordion" > 
							<xsl:with-param name="accid"><xsl:value-of select="$accid" /></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>	
				</xsl:otherwise>
			</xsl:choose>
		</div>
		
		<xsl:apply-templates/>		
	</xsl:template>
	
	<!-- 
		adds a jquery ui accordion
		if count is given it will create placeholder ###title_{uid}_{count}###, ###
	 -->
	<xsl:template name="jqueryaccordion">
		<xsl:param name="prntid"></xsl:param>
		<xsl:param name="count"></xsl:param>
		<xsl:param name="uid"></xsl:param>
		
		<h3><a href="#">
				<xsl:choose>
					<xsl:when test="$count != ''">
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue">###title_<xsl:value-of select="$uid"/>_<xsl:value-of select="$count"/>###</xsl:with-param>        
			    		</xsl:call-template>	
			    		<xsl:apply-templates/>	
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</a></h3>
		<div>	
			<p>
				<xsl:choose>
					<xsl:when test="$count != ''">
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue">###text_<xsl:value-of select="$uid"/>_<xsl:value-of select="$count"/>###</xsl:with-param>        
			    		</xsl:call-template>	
			    		<xsl:apply-templates/>	
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="textout">
			     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
			    			</xsl:call-template>	
			    		<xsl:apply-templates/>
					</xsl:otherwise>	
				</xsl:choose>
				
    		</p>
		</div>
	</xsl:template>
	
	
	<!-- 
		calls a template with given templatename count times
	 -->
	<xsl:template name="loop"> 
      <xsl:param name="count" select="1"/>
      <xsl:param name="prntid"></xsl:param>
	  <xsl:param name="templatename"></xsl:param>
	  <xsl:param name="uid"></xsl:param>
	  
      <xsl:if test="$count > 0">
      	<xsl:call-template name="templatebyname">
      		<xsl:with-param name="templatename" select="$templatename"></xsl:with-param>
      		<xsl:with-param name="prntid" select="$prntid"></xsl:with-param>
      		<xsl:with-param name="count" select="$count"></xsl:with-param>
      		<xsl:with-param name="uid" select="$uid"></xsl:with-param>
      	</xsl:call-template>
      
        <xsl:call-template name="loop">
          <xsl:with-param name="count" select="$count - 1"/>
          <xsl:with-param name="prntid" select="$prntid"/>
          <xsl:with-param name="templatename" select="$templatename"/>
          <xsl:with-param name="uid" select="$uid"></xsl:with-param>
        </xsl:call-template>
      </xsl:if>
      
 	</xsl:template>
 	
 	<!-- 
 		dynamic template call by templatename
 	 -->
 	<xsl:template name="templatebyname">
	  	<xsl:param name="templatename"></xsl:param>
	  	<xsl:param name="prntid"></xsl:param>
	  	<xsl:param name="count"></xsl:param>
	  	<xsl:param name="uid"></xsl:param>

	  	<xsl:choose>
	    	<xsl:when test="$templatename = 'accordioncontainer'">
	    		<xsl:call-template name="accordioncontainer"></xsl:call-template>
	    	</xsl:when>
	    	<xsl:when test="$templatename = 'jqueryaccordion'">
	    		<xsl:call-template name="jqueryaccordion">
	    			<xsl:with-param name="prntid" select="$prntid"></xsl:with-param>
	    			<xsl:with-param name="count" select="$count"></xsl:with-param>
	    			<xsl:with-param name="uid" select="$uid"></xsl:with-param>
	    		</xsl:call-template>
	    	</xsl:when>
	    	<xsl:when test="$templatename = 'accordionpane'">
	    		<xsl:call-template name="accordionpane">
	    			<xsl:with-param name="prntid" select="$prntid"></xsl:with-param>
	    			<xsl:with-param name="count" select="$count"></xsl:with-param>
	    			<xsl:with-param name="uid" select="$uid"></xsl:with-param>
	    		</xsl:call-template>
	    	</xsl:when>
	    </xsl:choose>
  
  </xsl:template>
 	
		
	<!-- 
		 add all xml attributes as html attributes, if the style attribute does not exist
		 the defaultstyle param will be added as style
	-->
	<xsl:template name="allattributes">
		<xsl:param name="defaultstyle" />
		<xsl:param name="uid" />
		
		<xsl:for-each select="@*">
			<xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
			<!-- testing
			  <xsl:choose>
			  	<xsl:when test="name() = 'id'">
			  		
			  		<xsl:attribute name="{name()}">			  				
				  		<xsl:for-each select="ancestor::*">
							xxxx<xsl:value-of select="name()"></xsl:value-of>xxxx
						</xsl:for-each>						
			  		</xsl:attribute>
			  	</xsl:when>
			  	<xsl:otherwise>
			  		<xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
			  	</xsl:otherwise>
			  </xsl:choose>
			 -->	  
		</xsl:for-each>
		
		<xsl:if test="$uid != ''">
			<xsl:attribute name="uid"><xsl:value-of select="$uid"/></xsl:attribute>	
		</xsl:if>
		
		<xsl:if test="$defaultstyle != ''">
			<xsl:if test="not(@style)">
				<xsl:attribute name="style"><xsl:value-of select="$defaultstyle"/></xsl:attribute>
			</xsl:if>
		</xsl:if>
	</xsl:template>
  
  
  <!--
    output the given textvalue or if a content element with id textvalue is found
    the value from the content element is displayed
  -->
  <xsl:template name="textout">
    <xsl:param name="textvalue" />
    <xsl:param name="istitle" />
    <!-- -->	 
      <xsl:variable name="contentgroup">
    	<xsl:choose>
    		<xsl:when test="//options/option[@name = 'contentgroup'] != ''">
    			<xsl:value-of select="//options/option[@name = 'contentgroup']"></xsl:value-of>
    		</xsl:when>
    		<xsl:otherwise>
    			<xsl:value-of select="//calltemplate[@group != 'contentgroup']/@group"></xsl:value-of>
    			<!--   <xsl:value-of select="//calltemplate/[@group = 'sd']"></xsl:value-of>-->	
    		</xsl:otherwise>
    	</xsl:choose>   	 
      </xsl:variable>
      
      <xsl:variable name="parse"></xsl:variable>
    
	  <xsl:choose>	  
	  <xsl:when test="$contentgroup != ''">
	  	 <!--  <xsl:variable name="contentgroup" select="//options/option[@name = 'contentgroup']"></xsl:variable> -->
	  	 <xsl:choose>
		   	 <xsl:when test="$istitle = 'true'">
		   	 	<xsl:choose>
					<xsl:when test="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]/@title"><xsl:value-of select="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]/@title" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="$textvalue" disable-output-escaping="yes"></xsl:value-of></xsl:otherwise>
				</xsl:choose>
		   	 </xsl:when>
		   	 <xsl:otherwise>
			    <xsl:choose>
			 		<xsl:when test="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]/child::*">
						<xsl:for-each select="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]/child::*">
							<xsl:choose>
								<xsl:when test="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]/@parse = 'true'">
									<xsl:apply-templates select="."/>
								</xsl:when>
								<xsl:otherwise><xsl:copy-of select="."></xsl:copy-of></xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>		 
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>							
							<xsl:when test="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]">
								<xsl:choose>
									<xsl:when test="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]/@parse = 'true'">
										<xsl:apply-templates select="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]" />
									</xsl:when>
									<xsl:otherwise><xsl:copy-of select="/root/contentlist/content[@id = $textvalue and @group = $contentgroup]" /></xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise><xsl:value-of select="$textvalue" disable-output-escaping="yes"></xsl:value-of></xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
	     </xsl:choose>
	  </xsl:when>
	  <xsl:otherwise>
	  
		  <xsl:choose>
		   	 <xsl:when test="$istitle = 'true'">
		   	 	<xsl:choose>
					<xsl:when test="/root/contentlist/content[@id = $textvalue]/@title"><xsl:value-of select="/root/contentlist/content[@id = $textvalue]/@title" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="$textvalue" disable-output-escaping="yes"></xsl:value-of></xsl:otherwise>
				</xsl:choose>
		   	 </xsl:when>
		   	 <xsl:otherwise>
			    <xsl:choose>
			 		<xsl:when test="/root/contentlist/content[@id = $textvalue]/child::*">
						<xsl:for-each select="/root/contentlist/content[@id = $textvalue]/child::*">
							<xsl:choose>
								<xsl:when test="/root/contentlist/content[@id = $textvalue]/@parse = 'true'"><xsl:apply-templates select="." /></xsl:when>
								<xsl:otherwise><xsl:copy-of select="." /></xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>		 
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="/root/contentlist/content[@id = $textvalue]">
								<xsl:choose>
									<xsl:when test="/root/contentlist/content[@id = $textvalue]/@parse = 'true'">
										<xsl:apply-templates select="/root/contentlist/content[@id = $textvalue]" />
									</xsl:when>
									<xsl:otherwise><xsl:copy-of select="/root/contentlist/content[@id = $textvalue]" /></xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise><xsl:value-of select="$textvalue" disable-output-escaping="yes"></xsl:value-of></xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
	     </xsl:choose>
	 	</xsl:otherwise>
     </xsl:choose>
     <xsl:if test="/root/contentlist/content[@id = $textvalue]/@applytpl = 'true'">
     	<xsl:apply-templates select="/root/contentlist/content[@id = $textvalue]"></xsl:apply-templates>
     </xsl:if>   
  </xsl:template>
  
	
	<!--
		add html element from xml element
	-->
	<xsl:template name="addhtml">
		<xsl:variable name="elem">   
    		<xsl:value-of select="name()"/>  
    	</xsl:variable>
		
		<xsl:element name="{$elem}">	
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>	
		</xsl:element>
	</xsl:template>
	
	<!--
		add all child html element from xml element
	-->
	<xsl:template name="addall">
		<xsl:for-each select="./*"> <!-- all direct childs -->
			
			<xsl:variable name="elem">   
	    		<xsl:value-of select="name()"/>  
	    	</xsl:variable>
			
			<xsl:element name="{$elem}">
				<xsl:call-template name="allattributes" />					
				<xsl:value-of select="text()[1]" />	
				<xsl:call-template name="addall" /> <!-- recursiv call -->
			</xsl:element>
		</xsl:for-each>
	</xsl:template>
	
	<!-- Templates for some html elements -->
	<!-- 
	<xsl:template match="ul">
		<xsl:copy-of select="."></xsl:copy-of>	
	</xsl:template>
	
	<xsl:template match="li">
		<xsl:copy-of select="."></xsl:copy-of>	
	</xsl:template>
	
	<xsl:template match="img">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	
	<xsl:template match="b">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	
	<xsl:template match="strong">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	
	<xsl:template match="em">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	
	<xsl:template match="i">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	-->
	
	<xsl:template match="code">
		<div class="wpdojoloader_highlight" initialized="false">
			<!-- <xsl:call-template name="allattributes" />
			<xsl:call-template name="addall" /> -->
			<xsl:copy-of select="node()"></xsl:copy-of>
			<!-- 
			<xsl:call-template name="textout">
     	 		<xsl:with-param name="textvalue" select="text()[1]"></xsl:with-param>        
    		</xsl:call-template>
    		 -->
    		<!--<xsl:apply-templates/> -->
		</div>
		<script type="text/javascript">	
			<xsl:text disable-output-escaping="yes"><![CDATA[initHighlightner();]]></xsl:text>
		</script>		
	</xsl:template>
	
	<!-- 
		this is the default template for text nodes
		the text output is done in the templates, if no template exist => no text output
	-->
	<xsl:template match="@*|text()">
		 <!-- <xsl:value-of select="." /> -->
  	</xsl:template>		
	
</xsl:stylesheet>