<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
<xsl:output method="html" version="4.01" encoding="UTF-8"/>
	
	<!-- the main template -->
	<xsl:template match="wpcontentroot">  
		<div class="tundra wpdojoloader">
			 <xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo contentpane -->
	<xsl:template match="contentpane">
		<div dojoType="dijit.layout.ContentPane" class="tundra wpdojoloader_contentpane">
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" />
			
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo tabcontainer -->
	<xsl:template match="tabcontainer">
		<div dojoType="dijit.layout.TabContainer" class="tundra wpdojoloader_tab">
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes">
				<xsl:with-param name="defaultstyle">width:100%;height:200px;</xsl:with-param> <!-- set the default style -->
			</xsl:call-template>  
		
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo datagrid -->
	<xsl:template match="datagrid">
		<div>
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" />
							
			<div dojoType="dojox.grid.DataGrid" rowsPerPage="40" class="tundra wpdojoloader_datagrid">
				
				<!-- add all attributes from xml to html -->
				<xsl:call-template name="allattributes">
					<xsl:with-param name="defaultstyle">width: 100%; height: 300px;</xsl:with-param> <!-- set the default style -->
				</xsl:call-template>  
			
				<xsl:value-of select="text()[1]" />
				<xsl:apply-templates/>
			</div>
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
		
			<xsl:value-of select="text()[1]" />
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
		
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo fisheye -->
	<xsl:template match="fisheye">
		<div class="wpdojoloader_fisheyelite">
			<xsl:call-template name="addhtml" />
		</div>
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
		
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo scrollpane -->
	<xsl:template match="accordioncontainer">
		<div dojoType="dijit.layout.AccordionContainer">
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes">
				<xsl:with-param name="defaultstyle">width: 100%;height: 300px;</xsl:with-param> <!-- set the default style -->
			</xsl:call-template>  
		
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo accordionpane -->
	<xsl:template match="accordionpane">
		<div dojoType="dijit.layout.AccordionPane">
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" /> <!--
				<xsl:with-param name="defaultstyle">width: 100%;height: 300px;</xsl:with-param>
			</xsl:call-template>  -->
		
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
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
			
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- a dojo accordionpane -->
	<xsl:template match="bordercontainer">
		<div dojoType="dijit.layout.BorderContainer" >
			
			<!-- add all attributes from xml to html -->
			<xsl:call-template name="allattributes" > 
				<xsl:with-param name="defaultstyle">width:100%; heigth:200px;</xsl:with-param>
			</xsl:call-template>
		
			<xsl:value-of select="text()[1]" />
			<xsl:apply-templates/>
		</div>
	</xsl:template>
	
	
	<!-- 
		 add all xml attributes as html attributes, if the style attribute does not exist
		 the defaultstyle param will be added as style
	-->
	<xsl:template name="allattributes">
		<xsl:param name="defaultstyle" />
		<xsl:for-each select="@*">
			  <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>	  
		</xsl:for-each>
		
		<xsl:if test="$defaultstyle != ''">
			<xsl:if test="not(@style)">
				<xsl:attribute name="style"><xsl:value-of select="$defaultstyle"/></xsl:attribute>
			</xsl:if>
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
	
	
	<!-- Templates for some html elements -->
	
	<xsl:template match="ul">
		<xsl:call-template name="addhtml" />	
	</xsl:template>
	
	<xsl:template match="li">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	
	<xsl:template match="img">
		<xsl:call-template name="addhtml" />
	</xsl:template>
	
	
	<!-- 
		this is the default template for text nodes
		the text output is done in the templates, if no template exist => no text output
	-->
	<xsl:template match="@*|text()">
		 <!-- <xsl:value-of select="." /> -->
  	</xsl:template>		
	
</xsl:stylesheet>