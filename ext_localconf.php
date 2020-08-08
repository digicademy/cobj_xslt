<?php
if (!defined('TYPO3_MODE')) die('Not in Typo3');

// defines content object XSLT
$GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'], [
    'XSLT' =>  Digicademy\CobjXslt\ContentObject\XsltContentObject::class
]);

// define example RTE preset for XSLT TypoTag in TYPO3 8.7
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['cobj_xslt'] = 'EXT:cobj_xslt/Configuration/RTE/Default.yaml';
