<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0"    
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    exclude-result-prefixes="xsl">

<xsl:output 
		omit-xml-declaration="yes"
		indent="yes" />
		
		
<!-- xml tempate -->
<xsl:template match="/">
	
	<xsl:apply-templates select="XML"/>
</xsl:template>

<xsl:template match="XML">
	<xsl:apply-templates select="*"/>
</xsl:template>

<xsl:template match="title">
	<h1><xsl:value-of select="."/><xsl:text> </xsl:text></h1>
</xsl:template>

<xsl:template match="cblocks">
	<div class="cblocks">
		<xsl:apply-templates select="cblock"/>
	</div>
</xsl:template>

<xsl:template match="cblock">
	<div class="cblock">
		<xsl:apply-templates select="text"/>
	</div>
</xsl:template>

<xsl:template match="text()" name="insertBreaks">
   <xsl:param name="pText" select="."/>

   <xsl:choose>
     <xsl:when test="not(contains($pText, '&#xA;'))">
       <xsl:copy-of select="$pText"/>
     </xsl:when>
     <xsl:otherwise>
       <xsl:value-of select="substring-before($pText, '&#xA;')"/>
       <br />
       <xsl:call-template name="insertBreaks">
         <xsl:with-param name="pText" select=
           "substring-after($pText, '&#xA;')"/>
       </xsl:call-template>
     </xsl:otherwise>
   </xsl:choose>
 </xsl:template>

		
</xsl:stylesheet>