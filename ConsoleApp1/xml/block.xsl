<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" indent="yes"/>
  
  <xsl:template match="root">

    <xsl:for-each select="card">

      <div class="card">
        <div class="card-body">

          <h4 class="card-title">
            <xsl:value-of select="title"/>
          </h4>

          <xsl:for-each select="p">

            <p class="card-text">

              <img>
                <xsl:attribute name="src">
                  <xsl:text>/images/</xsl:text>
                  <xsl:value-of select="img/src"/>
                </xsl:attribute>
              </img>

              <xsl:if test="string-length(normalize-space(description)) > 0">
                <br/>
                <br/>
                <xsl:value-of select="normalize-space(description)"/>
              </xsl:if>

            </p>
              
          </xsl:for-each>
          
          <span>
            <xsl:value-of select="@date_add"/>
          </span>

          <!--
          <span> | </span>
          
          <a href="#" class="card-link">Card link</a>
          <a href="#" class="card-link">Another link</a>
          -->
        
        </div>
      </div>

      <br />
      
    </xsl:for-each>
      
  </xsl:template>
  
</xsl:stylesheet>

<!--

   <div class="card">
			<div class="card-body">

				<h4 class="card-title">Card title</h4>
				<p class="card-text">
					<img src="https://www.w3schools.com/bootstrap4/img_avatar3.png" style="width:100px">
				    Some example text. Some example text.
			    </p>
				<a href="#" class="card-link">Card link</a>
				<a href="#" class="card-link">Another link</a>
			</div>
		</div>

-->

<!--

    s<br/>
		<ul class="pagination justify-content-center">
			<li class="page-item"><a class="page-link" href="#">1</a></li>
			<li class="page-item"><a class="page-link" href="#">2</a></li>
			<li class="page-item"><a class="page-link" href="#">3</a></li>
			<li class="page-item"><a class="page-link" href="#">4</a></li>
			<li class="page-item"><a class="page-link" href="#">5</a></li>
			<li class="page-item"><a class="page-link" href="#">Наступна сторінка</a></li>
		</ul>

-->