<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
<xsl:output method="html" indent="yes"/>

<!-- Шаблон -->
<xsl:param name="template" />
  
<!-- Розділ сайту -->
<xsl:param name="section" />

<!-- Розділ каталогу --> 
<xsl:param name="catalog" />
  
<xsl:template match="root">

  <xsl:choose>
    <xsl:when test="$template = 'head'">
      <xsl:call-template name="head" />
    </xsl:when>
    <xsl:when test="$template = 'footer'">
      <xsl:call-template name="footer" />
    </xsl:when>
    <xsl:when test="$template = 'catalog'">
      <xsl:call-template name="catalog" />
    </xsl:when>
  </xsl:choose>
  
</xsl:template>

<!-- Шапка сайту -->
<xsl:template name="head">

   <ul class="nav nav-pills">

      <xsl:for-each select="menu/item">
        <li class="nav-item">
          <a class="nav-link">
            <xsl:attribute name="href">
              <xsl:value-of select="href"/>
              <xsl:text>?section=</xsl:text>
              <xsl:value-of select="@key"/>
            </xsl:attribute>

            <xsl:if test="$section = @key">
              <xsl:attribute name="class">
                <xsl:text>nav-link active</xsl:text>
              </xsl:attribute>
            </xsl:if>
            
            <xsl:value-of select="title"/>
          </a>
        </li>
      </xsl:for-each>

    </ul>
  
</xsl:template>

<!-- Низ сайту -->
<xsl:template name="footer">
  
   <ul class="nav justify-content-center">

      <xsl:for-each select="menu/item">
        <li class="nav-item">
          <a class="nav-link">
            <xsl:attribute name="href">
              <xsl:value-of select="href"/>
              <xsl:text>?section=</xsl:text>
              <xsl:value-of select="@key"/>
            </xsl:attribute>

            <xsl:if test="$section = @key">
              <xsl:attribute name="class">
                <xsl:text>nav-link active</xsl:text>
              </xsl:attribute>
            </xsl:if>
            
            <xsl:value-of select="title"/>
          </a>
        </li>
      </xsl:for-each>

    </ul>

</xsl:template>

<!-- Каталог сайту -->
<xsl:template name="catalog">
  
    <xsl:variable name="catalog_item_class">
          <xsl:text>list-group-item list-group-item-action</xsl:text>
    </xsl:variable>
  
    <div class="list-group">
        <xsl:for-each select="catalog/block[@section_key = $section]/item">
             <a>
                <xsl:attribute name="href">
                  <xsl:value-of select="href"/>
                  <xsl:text>?section=</xsl:text>
                  <xsl:value-of select="$section"/>
                  <xsl:text>&amp;</xsl:text>
                  <xsl:text>catatalog=</xsl:text>
                  <xsl:value-of select="@key"/>
                </xsl:attribute>
               
                <xsl:attribute name="class">
                  <xsl:choose>
                      <xsl:when test="$catalog = @key">
                          <xsl:value-of select="$catalog_item_class"/>
                          <xsl:text> active</xsl:text>
                      </xsl:when>
                      <xsl:otherwise>
                          <xsl:value-of select="$catalog_item_class"/>
                     </xsl:otherwise>
                  </xsl:choose>
               </xsl:attribute>
                
                <xsl:value-of select="title"/>
              </a>
        </xsl:for-each>
    </div>

</xsl:template>

</xsl:stylesheet>