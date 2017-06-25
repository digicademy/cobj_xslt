.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


ChangeLog
---------

+----------------+---------------------------------------------------------------+
| Version        | Changes                                                       |
+================+===============================================================+
| 1.8.0          | - Version compatibility set to 7.6.0-8.7.99                   |
|                |                                                               |
|                | - Usage of old standalone classname "cobj_xslt" via migration |
|                |   is now removed; please use the namespaced version of the    |
|                |   class from now on                                           |
|                |                                                               |
|                | - CKEditor plugin for <xslt> TypoTag in TYPO3 8.7+            |
|                |                                                               |
|                | - Update manual                                               |
|                |                                                               |
|                | - PSR refactoring and code compliance                         |
+----------------+---------------------------------------------------------------+
| 1.7.0          | - Version compatibility set to 6.2.0-7.9.99                   |
|                |                                                               |
|                | - Namespace and class refactoring                             |
+----------------+---------------------------------------------------------------+
| 1.6.0          | - Version compatibility set to 4.5.0-6.2.99                   |
|                |                                                               |
|                | - Set extension version to be in line with cobj_xpath again   |
|                |                                                               |
|                | - Minor modifications in manual                               |
+----------------+---------------------------------------------------------------+
| 1.3.0          | - Version compatibility set to 4.5.0-6.1.99                   |
|                |                                                               |
|                | - ReST based manual                                           |
|                |                                                               |
|                | - stdWrap for transformToURI property                         |
|                |                                                               |
|                | - new property suppressReturn to suppress output if a         |
|                |   transformation should only be written to file               |
+----------------+---------------------------------------------------------------+
| 1.2.0          | - New XSLT view helper for Fluid templates                    |
|                |                                                               |
|                | - New tutorial XSLT and FLUIDTEMPLATE                         |
|                |                                                               |
|                | - New tutorial about <xslt> TypoTag                           |
|                |                                                               |
|                | - New loading mechanism for XSL stylesheets that now supports |
|                |   all types (stdWrap,string,path,url)                         |
+----------------+---------------------------------------------------------------+
| 1.1.1          | - Loading XML files from a path could fail sometimes          |
+----------------+---------------------------------------------------------------+
| 1.1.0          | - TypoScript change: The former 'source.url' and              |
|                |   'stylesheet.url' properties are dropped and the             |
|                |   functionality is now fused into the parent property         |
+----------------+---------------------------------------------------------------+
| 1.0.0          | - First public version                                        |
+----------------+---------------------------------------------------------------+
