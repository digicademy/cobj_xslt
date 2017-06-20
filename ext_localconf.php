<?php
if (!defined('TYPO3_MODE')) die('Not in Typo3');

// defines content object XSLT
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['cObjTypeAndClass'][] = array(
    0 => 'XSLT',
    1 => 'EXT:cobj_xslt/Classes/ContentObject/XsltContentObject.php:ADWLM\CobjXslt\ContentObject\XsltContentObject',
);
