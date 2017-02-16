<?php
namespace Craft;

/**
 * FFFields
 *
 * @author    Josh Angell <josh@angell.io>
 * @copyright Copyright (c) 2017, Josh Angell Ltd
 * @see       https://angell.io/plugins/fffields
 * @since     1.0
 */
class Fffields_RichTextService extends BaseApplicationComponent
{

    // Public Methods
    // =========================================================================


    // TODO: document these methods
    // TODO: convert params to object
    // =========================================================================

    public function getConfig(array $params)
    {

        $field = $params['fieldLayoutField']->getField();

//        // TODO implement redactor config from field
//        $this->_includeFieldResources($configJs);
//        $configJs = $this->_getConfigJson();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);

        $config = array(
            'id'    => $id,
            'name'  => $name,
            'value' => $params['value'],
//            'linkOptions'     => $this->_getLinkOptions(), // TODO support link options
//            'assetSources'    => $this->_getAssetSources(), // TODO support asset sources
//            'transforms'      => $this->_getTransforms(), // TODO support transforms
        );

        if ($config['value'] instanceof RichTextData)
        {
            $config['value'] = $config['value']->getRawContent();
        }

        if (strpos($config['value'], '{') !== false)
        {
            // Preserve the ref tags with hashes {type:id:url} => {type:id:url}#type:id
            $config['value'] = preg_replace_callback('/(href=|src=)([\'"])(\{(\w+\:\d+\:'.HandleValidator::$handlePattern.')\})(#[^\'"#]+)?\2/', function($matches)
            {
                return $matches[1].$matches[2].$matches[3].(!empty($matches[5]) ? $matches[5] : '').'#'.$matches[4].$matches[2];
            }, $config['value']);

            // Now parse 'em
            $config['value'] = craft()->elements->parseRefs($config['value']);
        }

        return $config;

    }

//    /**
//     * Returns the Redactor config JSON used by this field.
//     *
//     * TODO: convert to Trumbowyg config object
//     *
//     * @return string
//     */
//    private function _getConfigJson()
//    {
//        if ($this->_field->getFieldType()->getSettings()->configFile)
//        {
//            $configPath = craft()->path->getConfigPath().'redactor/'.$this->_field->getFieldType()->getSettings()->configFile;
//            $json = IOHelper::getFileContents($configPath);
//        }
//
//        if (empty($json))
//        {
//            $json = '{}';
//        }
//
//        return $json;
//    }

}