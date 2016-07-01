<!-- DEMO stylesheet for displaying the newsfeed http://news.typo3.org/rss.xml with XSLT -->

<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">

    <!-- set output format -->
    <xsl:output method="html" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />
    
    <!-- suppress any non matched text/atts -->
    <xsl:template match="text()|@*"/>
    
    <!-- root node -->
    <xsl:template match="/">
        <xsl:apply-templates />
    </xsl:template>
    
    <!-- processing of news items -->
    <xsl:template match="item">
        
        <div class="item">
        
        <!-- transform title to uppercase -->
        <h2><xsl:value-of select="php:functionString('strtoupper', title)"/></h2>
        
        <!-- get author if there and format date with PHP to DD.MM.YYYY -->
        <p>
            <xsl:if test="author">
                by <em><xsl:value-of select="author"/></em>
                <xsl:text disable-output-escaping="yes"> </xsl:text>
            </xsl:if>
            <span class="date">[<xsl:value-of select="php:functionString('strftime', '%d.%m.%Y' ,php:functionString('strtotime', pubDate))"/>]</span>
        </p>
        
        <!-- collect categories into one paragraph and separate them with commas -->
        <p>
            Tags: 
            <xsl:for-each select=".//category">
                <span><xsl:value-of select="."/></span>
                <xsl:if test="position() != last()">
                    <xsl:text>, </xsl:text>
                </xsl:if>
            </xsl:for-each>
        </p>
        
        <p><xsl:value-of select="description"/></p>
        
        <!-- hand link generation over to TypoScript -->
        <xsl:value-of select="php:functionString('\ADWLM\CobjXslt\ContentObject\XsltContentObject::typoscriptObjectPath', 'lib.link', link)" disable-output-escaping="yes"/>
            
        </div>
        
    </xsl:template>
    
</xsl:stylesheet>