.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Known problems
--------------

Currently two TYPO3 core bugs slightly influence some parts of the extensions functionality.
Both of them have a patch and should make it in future TYPO3 versions.

**1) Missing messages checkbox in RTE Admin Panel**

`Bug report on TYPO3 Forge <https://forge.typo3.org/issues/81609>`_

This is just a minor inconvenience and only affects the admin panel in TYPO3 version 8.7 and above.
You can always work around this situations by setting "admPanel.tsdebug.displayMessages = 1" in
your TSConfig.

**2) ContentObjectRenderer not fully initialized in Fluids <f:format.html> ViewHelper**

`Bug report on TYPO3 Forge <https://forge.typo3.org/issues/81624>`_

This affects the functionality of the <xslt> TypoTag in TYPO3 versions 7.6 and higher. Since
fluid_styled_content passes the content through lib.parseFunc (which has the bug) the rendering
of the TypoTag is not triggered even if it is correctly registered with TypoScript. Unless
the patch makes it into the core you have to patch your TYPO3 source manually.

Otherwise there are no know problems at the moment.

If you find any bugs, please report them on `GitHub <https://github.com/digicademy/cobj_xslt/issues>`_ .
