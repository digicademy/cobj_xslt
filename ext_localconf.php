<?php
if (!defined('TYPO3_MODE')) die('Not in Typo3');

// defines content object XSLT
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['cObjTypeAndClass'][] = array(
    0 => 'XSLT',
    1 => 'Digicademy\CobjXslt\ContentObject\XsltContentObject',
);

// define example RTE preset for XSLT TypoTag in TYPO3 8.7
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['cobj_xslt'] = 'EXT:cobj_xslt/Configuration/RTE/Default.yaml';
