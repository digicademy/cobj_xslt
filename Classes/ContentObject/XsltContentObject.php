<?php

namespace Digicademy\CobjXslt\ContentObject;

/***************************************************************
 *  Copyright notice
 *
 *  Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DOMDocument;
use XSLTProcessor;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;


if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

class XsltContentObject extends AbstractContentObject
{

    /**
     * @var \DOMDocument $xsl Instance for loading a XSL stylesheet during a transformation run
     */
    protected $xsl;

    /**
     * @var \XSLTProcessor $xslt XSLT Processor instance during a transformation run
     */
    protected $xslt;

    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * Renders the XSLT content object
     *
     * @param array $conf TypoScript configuration of the cObj
     *
     * @return string The transformed XML string
     *
     */
    public function render($conf = [])
    {
        $content = '';

        // TimeTracker object is gone in TYPO3 8 but needed to set TS log messages; instantiate in versions >= 8.7
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= 8007000 && !is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = GeneralUtility::makeInstance(TimeTracker::class);
        }

        // Check if necessary XML extensions are loaded with PHP
        if (extension_loaded('SimpleXML') && extension_loaded('libxml') && extension_loaded('dom') && extension_loaded('xsl')) {

            // Fetch XML data
            if (isset($conf['source']) || is_array($conf['source.'])) {
                $xmlsource = $this->fetchXml($conf['source'], $conf['source.']);
            } else {
                $GLOBALS['TT']->setTSlogMessage('Source for XML is not configured.', 3);
            }
            // start XSLT transformation
            if (!empty($xmlsource)) {

                // Try to load a simpleXML object; this makes validation of XML and error handling easy
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($xmlsource);

                if ($xml instanceof \SimpleXMLElement) {

                    // Import the simpleXML object into a DOM object (necessary for XSLT support)
                    $domXML = dom_import_simplexml($xml);

                    // If it worked and transformations are configured
                    if ($domXML instanceof \DOMElement && count($conf['transformations.']) > 0) {

                        // Initialize transformation result
                        $result = '';

                        // Sort transformation configuration
                        ksort($conf['transformations.']);

                        foreach ($conf['transformations.'] as $index => $transformation) {

                            // Prepare new XSL for this run
                            $this->xsl = GeneralUtility::makeInstance(DOMDocument::class);

                            // get stylesheet for the transformation
                            $stylesheet = $this->fetchXml($transformation['stylesheet'],
                                $transformation['stylesheet.']);

                            // If loading of the stylesheet isn't successfull, skip this run
                            if (empty($stylesheet) || $this->xsl->loadXML($stylesheet) === false) {
                                $GLOBALS['TT']->setTSlogMessage('No valid XSL stylesheet set for transformation ' . $index . '', 3);
                                continue;
                            }

                            // Start XSLT processing
                            if ($this->xsl instanceof \DOMDocument) {

                                // Create a new XSLT processor and import the current stylesheet
                                $this->xslt = GeneralUtility::makeInstance(XSLTProcessor::class);
                                $this->xslt->importStylesheet($this->xsl);

                                // Possibility to register PHP functions for use within the XSL stylesheet
                                if (isset($transformation['registerPHPFunctions'])) {

                                    // If particular functions are configured, provide restricted registration
                                    if (is_array($transformation['registerPHPFunctions.'])) {

                                        // Test if the functions can be called
                                        $registeredFunctions = array();
                                        foreach ($transformation['registerPHPFunctions.'] as $key => $function) {
                                            if (strpos($function, '::')) {
                                                $objectAndMethod = GeneralUtility::trimExplode('::', $function);
                                                if (is_callable($objectAndMethod[0], $objectAndMethod[1])) {
                                                    $registeredFunctions[] = $function;
                                                }
                                            } elseif (is_callable($function)) {
                                                $registeredFunctions[] = $function;
                                            } else {
                                                $GLOBALS['TT']->setTSlogMessage('Tried to register a function ' . $function . ' that is not callable.', 3);
                                            }
                                        }

                                        // Now register all valid functions
                                        if (count($registeredFunctions) > 0) {
                                            $this->xslt->registerPHPFunctions($registeredFunctions);
                                        } else {
                                            $GLOBALS['TT']->setTSlogMessage('None of the functions specified in registerPHPFunctions were callable so nothing gets registered.', 3);
                                        }

                                        // If registerPHPFunctions was just set to 1, register all PHP functions without any restrictions
                                    } else {
                                        $this->xslt->registerPHPFunctions();
                                    }
                                }

                                // Set parameters for this stylesheet
                                if (is_array($transformation['setParameters.'])) {

                                    foreach ($transformation['setParameters.'] as $parameter => $value) {
                                        $paramNamespace = '';
                                        if (substr($parameter, -1) == '.' && is_array($value) === true) {
                                            $paramName = substr($parameter, 0, -1);
                                            $paramNamespace = $value['namespace'];
                                            $paramValue = $this->cObj->stdWrap($value['value'], $value['value.']);
                                            $this->xslt->setParameter($paramNamespace, $paramName, $paramValue);
                                        } else {
                                            $GLOBALS['TT']->setTSlogMessage('Setting the parameter ' . $parameter . ' failed due to misconfiguration', 3);
                                        }
                                    }
                                }

                                // Remove parameters from this stylesheet
                                if (is_array($transformation['removeParameters.'])) {

                                    foreach ($transformation['removeParameters.'] as $parameter => $value) {
                                        $paramNamespace = '';
                                        if (substr($parameter, -1) == '.' && is_array($value) === true) {
                                            $paramName = substr($parameter, 0, -1);
                                            if (isset($value['namespace'])) {
                                                $paramNamespace = $value['namespace'];
                                            }
                                            $this->xslt->removeParameter($paramNamespace, $paramName);
                                        } elseif (substr($parameter, -1) !== '.' && (int)$value > 0) {
                                            $paramName = $parameter;
                                            $this->xslt->removeParameter('', $paramName);
                                        }
                                    }
                                }

                                // Activate profiling if configured
                                if (isset($transformation['setProfiling'])) {
                                    $profilingTempFile = GeneralUtility::tempnam('xslt_profiler_');
                                    $this->xslt->setProfiling($profilingTempFile);
                                }

                                // If there is a result from a former transformation of the source
                                if ($result !== '') {

                                    // Load the transformed XML from the last run into a new DOM object
                                    $formerResult = GeneralUtility::makeInstance(DOMDocument::class);

                                    // If the XML is valid, apply the current XSL transformation
                                    if ($formerResult->loadXML($result) !== false) {
                                        $result = $this->xslt->transformToXML($formerResult);
                                    } else {
                                        $GLOBALS['TT']->setTSlogMessage('XSL transformation ' . $index . ' failed because the XML resulting from the former transformation is invalid.', 3);
                                    }

                                    // First run, process the loaded source
                                } else {
                                    $result = $this->xslt->transformToXML($domXML);
                                }

                                // Load the profiling result from temporary file into admin panel
                                if (isset($transformation['setProfiling'])) {
                                    $profilingInformation = str_replace(' ', ' ', GeneralUtility::getURL($profilingTempFile));
                                    $GLOBALS['TT']->setTSlogMessage('Profiling result for XSL transformation ' . $index . "\n" . $profilingInformation, 1);
                                    GeneralUtility::unlink_tempfile($profilingTempFile);
                                }

                                // stdWrap for this transformation
                                if ($transformation['stdWrap.']) {
                                    $result = $this->cObj->stdWrap($result, $transformation['stdWrap.']);
                                }

                                // If set write the result of this transformation to a file
                                // Use TYPO3 functions here (and not transformToURI) so that the stdWrap output can be included
                                if ($resultFile = GeneralUtility::getFileAbsFileName($this->cObj->stdWrap($transformation['transformToURI'],
                                    $transformation['transformToURI.']))
                                ) {
                                    GeneralUtility::writeFile($resultFile, $result);
                                }

                                // suppress transformation result if configured; makes sense in scenarios where the transformation output is written to a file
                                if ($resultFile && (int)$transformation['suppressReturn'] === 1) {
                                    $result = '';
                                }

                            } else {
                                $GLOBALS['TT']->setTSlogMessage('The stylesheet ' . $index . ' could not be loaded or contained errors.', 3);
                            }
                        }

                        // Set content to final result of all transformations
                        $content = $result;

                    } else {
                        $GLOBALS['TT']->setTSlogMessage('XML could not be converted to a DOM object or no transformations were configured.', 3);
                    }

                } else {
                    $errors = libxml_get_errors();
                    foreach ($errors as $error) {
                        $GLOBALS['TT']->setTSlogMessage('XML Problem: ' . $this->getXmlErrorCode($error), 3);
                    }
                    libxml_clear_errors();
                }

            } else {
                $GLOBALS['TT']->setTSlogMessage('The configured XML source did not return any data.', 3);
            }

        } else {
            $GLOBALS['TT']->setTSlogMessage('The PHP extensions SimpleXML, dom, xsl and libxml must be loaded.', 3);
        }

        return $this->cObj->stdWrap($content, $conf['stdWrap.']);
    }

    /**
     * Static wrapper function for calling TypoScript cObjects from XSL stylesheets, e.g. by doing
     * <xsl:value-of select="php:functionString('\Digicademy\CobjXslt\ContentObject\XsltContentObject::typoscriptObjectPath', 'lib.my.object', YOUR XPATH)"/>
     * registerPHPfunctions must be set in the configuration of the cObj for this to work
     *
     * @param string $key  The setup key to be applied from the global TypoScript scope
     * @param mixed  $data The matches of the XPATH expression from the XSL stylesheet
     *
     * @return string The rendered TypoScript object
     */
    public static function typoscriptObjectPath($key, $data)
    {

        // Set data to the current value - first possibility is an incoming array of DOMElements (if called with php:function in the XSL styleheet)
        if (is_array($data)) {
            $currentVal = '';
            // Accumulate all matches to a XML string and hand it over for TypoScript processing
            foreach ($data as $match) {
                $currentVal .= $match->C14N(0, 1);
                $GLOBALS['TSFE']->cObj->setCurrentVal($currentVal);
            }
            // Second possibility is an incoming string (if called with php:functionString in the XSL stylesheet)
        } else {
            $GLOBALS['TSFE']->cObj->setCurrentVal($data);
        }

        // Get TypoScript configuration from global scope
        $tsParser = GeneralUtility::makeInstance(TypoScriptParser::class);
        $configuration = $tsParser->getVal($key, $GLOBALS['TSFE']->tmpl->setup);

        // Process and return a TS object
        if (is_array($configuration)) {
            $out = $GLOBALS['TSFE']->cObj->cObjGetSingle($configuration[0], $configuration[1]);
        } else {
            $GLOBALS['TT']->setTSlogMessage('The TypoScript key ' . $key . ' referenced in the XSL stylesheet could not be found', 3);
            $out = '';
        }

        return $out;
    }

    /**
     * Tries to fetch XML with stdWrap, from a path or from an url
     *
     * @param string $source
     * @param string $configuration
     *
     * @return string
     */
    private function fetchXml($source, $configuration)
    {
        $xml = '';
        // First process the source string with stdWrap
        $xml = $this->cObj->stdWrap($source, $configuration);
        // Fetch by (possible) path
        $path = GeneralUtility::getFileAbsFileName($xml);
        if (@is_file($path) === true) {
            $xml = GeneralUtility::getURL($path, 0, false);
            // Fetch by (possible) URL
        } elseif (GeneralUtility::isValidUrl($xml) === true) {
            $xml = GeneralUtility::getURL($xml, 0, false);
        }

        return $xml;
    }

    /**
     * Returns XML error codes for the TSFE admin panel.
     * Function inspired by http://www.php.net/manual/en/function.libxml-get-errors.php
     *
     * @param \LibXMLError $error
     *
     * @return string
     */
    private function getXmlErrorCode(\LibXMLError $error)
    {
        $errormessage = '';

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $errormessage .= 'Warning ' . $error->code . ': ';
                break;
            case LIBXML_ERR_ERROR:
                $errormessage .= 'Error ' . $error->code . ': ';
                break;
            case LIBXML_ERR_FATAL:
                $errormessage .= 'Fatal error ' . $error->code . ': ';
                break;
        }

        $errormessage .= trim($error->message) . ' - Line: ' . $error->line . ', Column:' . $error->column;

        if ($error->file) {
            $errormessage .= ' - File: ' . $error->file;
        }

        return $errormessage;
    }

}
