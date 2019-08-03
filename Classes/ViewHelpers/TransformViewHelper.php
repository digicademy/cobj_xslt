<?php

namespace Digicademy\CobjXslt\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Usage:
 *
 * <xslt:transform transformations="{0: {stylesheet: 'path/to/stylesheet.xsl', setProfiling: 1, registerPHPFunctions: 1, transformToURI: 'path/to/file.xml', suppressReturn: 1}}" />
 *
 * The transformations property of the view helper expects an array of configurations, just as you would normally do it from TypoScript.
 * Only the four configuration keys from above are allowed; no setParameter/removeParameter; you are in a Fluid context, this should not be necessary
 *
 */

class TransformViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
        $this->configurationManager = $configurationManager;
        $this->contentObject = $this->configurationManager->getContentObject();
        $this->contentObject->start(array(), '');
    }

    /**
     * Initialize ViewHelper arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('source', 'null', 'source', false, null);
        $this->registerArgument('transformations', 'array', 'transformations', false, []);
    }

    /**
     * Fluid view helper wrapper for the XSLT content object. Calls the content object class directly.
     *
     * @return mixed
     */
    public function render()
    {

        $content = '';
        $source = $this->arguments['source'];
        $transformations = $this->arguments['transformations'];

        if ($source === null) {
            $source = $this->renderChildren();
        }

        if (count($transformations) > 0 && array_key_exists('stylesheet', $transformations[0])) {
            $configuration = array();
            foreach ($transformations as $key => $transformation) {
                $i = $key + 1;
                $configuration['transformations.'][$i]['stylesheet'] = $transformation['stylesheet'];
                if (array_key_exists('setProfiling', $transformation)) {
                    $configuration['transformations.'][$i]['setProfiling'] = (int)$transformation['setProfiling'];
                }
                if (array_key_exists('registerPHPFunctions', $transformation)) {
                    $configuration['transformations.'][$i]['registerPHPFunctions'] = (int)$transformation['registerPHPFunctions'];
                }
                if (array_key_exists('transformToURI', $transformation)) {
                    $configuration['transformations.'][$i]['transformToURI'] = $transformation['transformToURI'];
                }
                if (array_key_exists('suppressReturn', $transformation)) {
                    $configuration['transformations.'][$i]['suppressReturn'] = $transformation['suppressReturn'];
                }
            }
            $configuration['source'] = trim($source);
            $content = $this->contentObject->cObjGetSingle('XSLT', $configuration, '');

        }

        return $content;
    }
}
