<?xml version="1.0" encoding="UTF-8"?>
<!--<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01//EN"
                doctype-system="http://www.w3.org/TR/html4/strict.dtd" indent="yes"/>

    <xsl:template match="/">
        <xsl:variable name="distinct-work"
                      select="descendant::msItem[@n][not(@n = preceding::msItem/@n)]"> 
        </xsl:variable>
        <xsl:variable name="all-msDesc" select="descendant::msDesc"/>
         
        <div id="manuscriptList">
            <div class="dataList hasTable">

                <h1>Manuscripts list</h1>
                <table class="table table-browser" id="manuscript">
                    <thead>
                        <tr>
                            <th scope="col">Settlement</th>
                            <th scope="col">Institution</th>
                            <th scope="col">Identifier</th>
                            <th scope="col">Works</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="descendant::msDesc">
                            <tr>
                                <xsl:apply-templates select="."/>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </div>                    
            <!-- ____________________________________________ Work ____________________________________ -->
            <div class="dataList hasTable">
                <h1>Work list</h1>

                <table class="table table-browser">
                    <thead>
                        <th scope="row">Id</th>
                        <th scope="row">Work</th>
                        <th scope="row">Author</th>
                        <th scope="row">Ms.</th>
                    </thead>
                    <tbody>
                        <xsl:for-each select="$distinct-work">
                            <tr>
                                <xsl:variable name="work-id" select="./@n"/>
                                <td>
                                    <xsl:value-of select="$work-id"/>
                                </td>
                                <td>
                                    <span>
                                        <xsl:attribute name="id" select="concat('work', ./@n)"/>
                                        <xsl:value-of select="./title"/>
                                    </span>
                                </td>
                                <td>
                                    <xsl:variable name="author" select="./author"/>
                                    <xsl:value-of select="$author"/>
                                    <xsl:if test="not($author)">Anonymous</xsl:if>
                                </td>
                                <td>
                                    <ul>
                                        <xsl:for-each select="$all-msDesc[descendant::msItem[@n = $work-id]]">
                                            <li>
                                                <a>
                                                    <xsl:attribute name="href" select="concat('#ms', ./msIdentifier/idno)"/>
                                                    <xsl:value-of select="descendant::institution"/>|<xsl:apply-templates select="./msIdentifier"/>
                                                </a>
                                            </li>
                                        </xsl:for-each>
                                    </ul>
                                </td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>

                 
            </div>
        </div>
    </xsl:template>


    <xsl:template match="msDesc">
        <xsl:variable name="idno" select="./msIdentifier/idno"/>
        <td>
            <xsl:value-of select="descendant::settlement"/>
        </td>
        <td>
            <xsl:value-of select="descendant::institution"/>
        </td>
        <td>
            <a>
                <xsl:attribute name="href" select="concat('#collapse', $idno)"/>
                <xsl:attribute name="aria-controls" select="concat('collapse', $idno)"/>
                <xsl:attribute name="id" select="concat('ms', $idno)"/>
                <xsl:apply-templates select="msIdentifier"/>
            </a>
        </td>
        <td>
            <ul>
                <xsl:for-each select="descendant::msItem">
                    <li>
                        <xsl:apply-templates select="."/>
                    </li>
                </xsl:for-each>
            </ul>
        </td>

    </xsl:template>

    <xsl:template match="msIdentifier">
        <xsl:value-of select="repository"/>&#8194;
        <xsl:value-of select="idno"/>
    </xsl:template>

    <xsl:template match="msItem">
        <a>
            <xsl:attribute name="href" select="concat('#work', ./@n)"> </xsl:attribute>
            <xsl:value-of select="title"/>
        </a>
    </xsl:template>


</xsl:stylesheet>
