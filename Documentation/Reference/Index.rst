.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Reference
---------

This section gives an overview on all TypoScript properties of the XSLT content object.

.. container:: table-row

   Property
         source

   Data type
         string\/stdWrap

   Description
         This fetches the XML data from a source. Can be a XML string, a field
         in the database containing XML, a file (path or via TypoScript FILE
         cObject) or an external resource.

         **Example (field):** ::

            page.10 = XSLT
            page.10 {
               source.data = page : my_xml_field
               [...]
            }

         Fetches the XML from the field 'my\_xml\_field' of the current page
         record.

         **Example (stdWrap / FILE):** ::

            page.10 = XSLT
            page.10 {
               source.cObject = FILE
               source.cObject.file = fileadmin/myfile.xml
               [...]
            }

         This fetches the XML from a file included with TypoScript's FILE
         content object. Please note: Due to FILE's internal settings, the data
         can't be larger than 1024kb. See TSref.

         **Example (external):** ::

            page.10 = XSLT
            page.10 {
               source = http://news.typo3.org/rss.xml
               [...]
            }

         This draws the XML from an external source. It can be an URL like
         above or an external file resource of any size.


.. container:: table-row

   Property
         transformations.[1,2,3...]

   Data type
         array

   Description
         This configuration array contains all transformations in [index].[settings]
         notation. During rendering, the content object pipes the XML
         data through all configured transformations in numeric order. See the
         subproperties for configuration details.

         **Example:** ::

            page.10 = XSLT
            page.10 {
               source.data = page : my_xml_field
               transformations {
                  1 {
                      stylesheet = fileadmin/my.xsl
                      setProfiling = 1
                      [...]
                  }
               }
            }


.. container:: table-row

   Property
         transformations.[i].stylesheet

   Data type
         string\/stdWrap

   Description
         This property sets the XSL stylesheet that will get applied to the current
         transformation. Stylesheets can be loaded from a string, a path, with
         stdWrap or from an external resource.

         **Example (string):** ::

            transformations.1 {
               stylesheet (
            <xsl:stylesheet version="1.0" 
            xmlns:xsl="http://www.w3.org/1999/XSL/Transform"      
            <xsl:output method="html" encoding="utf8" indent="yes"/>
            <xsl:template match="item">             
            <p><xsl:value-of select="description"/></p>
            </xsl:template>
            </xsl:stylesheet>
            )
               [...]
            }

         **Example (path):** ::

            transformations.1 {
               stylesheet = fileadmin/my.xsl
            }

         **Example (stdWrap):** ::

            transformations.1 {
               stylesheet.cObject = FILE
               stylesheet.cObject.file = fileadmin/my.xsl
               [...]
            }

         **Example (external):** ::

            transformations.1 {
               stylesheet = http://example.org/external.xsl
               [...]
            }


.. container:: table-row

   Property
         transformations.[i].transformToURI

   Data type
         path\/stdWrap

   Description
         If a valid filepath is set, the result of the current transformation
         is not only returned but also written to a file. This is very useful
         for debugging multi-transformation scenarios. Its also useful for providing
         generated XML resources that can then be picked up by following XSLT
         objects. If the result of a transformation should only be written to a file
         without returning the result, use the property [i].suppressReturn.

         **Example:** ::

            transformations.1 {
               transformToURI = fileadmin/transformation-1.xml
               [...]
            }


.. container:: table-row

   Property
         transformations.[i].suppressReturn

   Data type
         boolean

   Description
         If [i].transformToURI is used and the result should only be written to 
         the file, you can use this property to completely suppress the return 
         of the transformation.

         **Example:** ::

            transformations.1 {
               transformToURI = fileadmin/transformation-1.xml
               suppressReturn = 1
               [...]
            }


.. container:: table-row

   Property
         transformations.[i].registerPHPFunctions

   Data type
         Boolean \+ array of PHP function names

   Description
         The use of PHP functions within XSL stylesheets provides
         really powerful possibilities.
         If this property is set to 1, all available PHP functions in your
         environment can be called from your XSL stylesheets. This can be
         restricted by providing specific function names in a key => name
         notation below the property.

         .. important::

            You must declare the PHP namespace in your XSL stylesheet: xmlns:php="http://php.net/xsl"

         **Example:** ::

            transformations.1 {
                registerPHPFunctions = 1
                registerPHPFunctions {
                   1 = strtoupper
                }
            }

         This activates the PHP function registration and restricts the calling
         of functions to strtoupper() for the current stylesheet. In your XSL
         stylesheet you can then do:

         .. code-block:: xml

            <h1><xsl:value-of select="php:functionString('strtoupper', title)"/></h1>

         This will transform the content of the matched tags to uppercase.

         **typoscriptObjectPath:**

         In addition to calling standard PHP functions, the XSLT object
         provides the possibility to work with TypoScript cObjects from your
         XSL stylesheets. This functionality is quite similar to the
         <f:cObject> viewhelper in FLUID. For activation, you need to register
         the static typoscriptObjectPath function of this extension for the
         current stylesheet: ::

            transformations.1 {
                registerPHPFunctions = 1
                registerPHPFunctions {
                   1 = \ADWLM\CobjXslt\ContentObject\XsltContentObject::typoscriptObjectPath
                }
            }

         In your stylesheet, you can then do:

         .. code-block:: xslt

            <xsl:value-of select="php:functionString('\ADWLM\CobjXslt\ContentObject\XsltContentObject::typoscriptObjectPath', 'lib.my.cObject', title)"/>

         This will submit the matches found by the stylesheet to lib.my.cObject
         for further processing.


.. container:: table-row

   Property
         transformations.[i].setParameters

   Data type
         array \+ subproperties

   Description
         Makes it possible to set parameters for the current stylesheet from
         TypoScript. The syntax is: ::

            transformations.1 {
                setParameters {
                   your_parameter_name {
                       namespace = your_namespace
                       value = your_value
                   }
                }
            }

         The keys of the array are the parameter names. Below each parameter
         name a namespace (string) and a value can be set. The  **.value**
         subproperty has stdWrap capabilities.

         **Example:** ::

            transformations.1 {
                setParameters {
                   pagetitle.value.data = page:title
                }
            }

         And in your XSL stylesheet:

         .. code-block:: xslt

            <xsl:param name="pagetitle" select="default"/>
            <h1><xsl:value-of select="$pagetitle"/></h1>


.. container:: table-row

   Property
         transformations.[i].removeParameters

   Data type
         array \+ subproperties

   Description
         Remove formerly set parameters from the stylesheet. The syntax is: ::

            transformations.1 {
                removeParameters {
                   your_parameter_name = 1
                   your_parameter_name {
                       namespace = your_namespace
                   }
                }
            }

         The namespace property is optional. Parameters to remove must be set
         to 1.


.. container:: table-row

   Property
         transformations.[i].setProfiling

   Data type
         boolean

   Description
         This activates profiling for the current stylesheet. The profiling
         information is written to the TSFE admin panel.


.. container:: table-row

   Property
         transformations.[i].stdWrap

   Data type
         stdWrap

   Description
         stdWrap properties for the current transformation. Executed before the result is passed to the next
         transformation and/or written to a file.


.. container:: table-row

   Property
         stdWrap

   Data type
         stdWrap

   Description
         stdWrap properties for the whole XSLT cObject ::

            page.10 = XSLT
            page.10 {

               [...]

               stdWrap {
                  outerWrap = <code>|</code>
               }
            }

         Executed on the final result of all transformations just before the content is returned.

Next is an example for all TS configuration options with their according data types

::

   my.object = XSLT
   my.object {

      source = [URL / PATH / STRING / stdWrap]

      transformations {

         1 {
               stylesheet = [URL / PATH / STRING / stdWrap]

               transformToURI = [PATH]

               registerPHPFunctions = [BOOLEAN / ARRAY]
               registerPHPFunctions {
                  10 = [object name :: function name]
               }

               setParameters {
                  parametername {
                     namespace = [STRING]
                     value =  [STRING / stdWrap]
                  }
               }

               removeParameters {
                  parametername {
                     namespace = [STRING]
                  }
               }

               setProfiling = [BOOLEAN]

               stdWrap = [stdWrap to result of this transformation]
         }

         2 {
               [...]
         }
      }

      stdWrap [stdWrap to the whole object]
   }

