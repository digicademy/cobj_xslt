.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


XSLT and FLUIDTEMPLATE
^^^^^^^^^^^^^^^^^^^^^^

The topic of the first tutorial was to demonstrate a pure TypoScript
XSLT transformation on some simple XML file. Having the XSLT content
object as a TypoScript implementation is what makes it so very
flexible. But what about the Extbase/Fluid way of doing things? It
might be that you want to handle or transform XML data in your
FLUIDTEMPLATEs for example...

This can always be achieved by using Fluid's cObject view
helper to invoke a TypoScript XSLT content object. From version 1.2.0
of cobj\_xslt it is even easier. The newer versions of cobj_xslt ship with
a special <xslt:transform> view helper. So let's try the
first tutorial again, but this time with Fluid.

First we set up a basic FLUIDTEMPLATE:

::

   page.10 = FLUIDTEMPLATE
   page.10.file = fileadmin/xslt/Collection.html

And that's really it. The rest is done in Fluid :) We open up our
FLUIDTEMPLATE and first load the view helper into an according
namespace:

::

   {namespace xslt=ADWLM\CobjXslt\ViewHelpers}

We can now call the view helper with <xslt:transform>. It takes
two required attributes: "source" and "transformations". Within "source" you
can set a path to a XML resource. If you don't set this, the view
helper takes the content of the tag as XML source. "Transformations"
in turn expects an array of configurations with array keys in
concordance with the property names of the XSLT cObj. Example Fluid
code:

::

   <f:format.raw>
   <xslt:transform transformations="{0: {stylesheet: 'fileadmin/xslt/collection.xsl'}">
           <collection>
                   <cd>
                           <title>Fight for your mind</title>
                           <artist>Ben Harper</artist>
                           <year>1995</year>
                   </cd>
                   <cd>
                           <title>Electric Ladyland</title>
                           <artist>Jimi Hendrix</artist>
                           <year>1997</year>
                   </cd>
           </collection>
   </xslt:transform>
   </f:format.raw>

As you can see we pass on a numbered array that reflects the number of
transformations we would like the data to go through. Just as in
TypoScript, the second level of this array is associative. The keys
reflect the according TypoScript properties.

   .. attention::

      From TYPO3 version 8.7 onwards you must wrap the view helper output in <f:format.raw> tags.

You can only use the above five properties in the XSLT view helper.

+----------------------+---------------------------------------------------------------+
| property             | description                                                   |
+======================+===============================================================+
| stylesheet           | Sets the XSL stylesheet for the current transformation        |
+----------------------+---------------------------------------------------------------+
| setProfiling         | Outputs profiling information in the TSFE admin panel         |
+----------------------+---------------------------------------------------------------+
| registerPHPFunctions | Allows the use of PHP functions in XSL stylesheets. Notice:   |
|                      | Within the view helper context the limitation to certain      |
|                      | functions is not implemented, you always allow all callable   |
|                      | PHP functions for the stylesheet)                             |
+----------------------+---------------------------------------------------------------+
| transformToURI       | File to write the result of the current transformation to     |
+----------------------+---------------------------------------------------------------+
| suppressReturn       | Only write to file and suppress returning the result          |
+----------------------+---------------------------------------------------------------+

The above code produces precisely the same result as in the first
tutorial. Next is a Fluid solution to how you could have solved the second
tutorial about the RSS feed:

::

   <f:format.raw>
      <xslt:transform source="http://news.typo3.org/rss.xml" transformations="{0: {stylesheet: 'fileadmin/xslt/rss.xsl', registerPHPFunctions: 1, setProfiling: 1}, 1: {stylesheet: 'fileadmin/xslt/rm.namespaces.xsl', setProfiling: 1}}" />
   </f:format.raw>

That's it. Enjoy!