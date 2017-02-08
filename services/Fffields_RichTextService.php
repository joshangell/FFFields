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
    // Properties
    // =========================================================================

    /**
     * @var
     */
    private $_field;

    // Public Methods
    // =========================================================================


    /**
     * Renders a <rich-text /> custom input, which uses the Trumbowyg editor
     *
     * @param FieldModel $field
     * @param            $value
     * @param            $namespace
     *
     * @return string
     */
    public function render(FieldModel $field, $value, $namespace)
    {

        $this->_field = $field;

//        // TODO implement redactor config from field
//        $configJs = $this->_getConfigJson();
//        $this->_includeFieldResources($configJs);

        $id = craft()->templates->namespaceInputId($this->_field->handle, $namespace);
        $name = craft()->templates->namespaceInputName($this->_field->handle, $namespace);

        $settings = array(
            'id'    => $id,
            'name'  => $name,
            'value' => $value,
//            'linkOptions'     => $this->_getLinkOptions(), // TODO support link options
//            'assetSources'    => $this->_getAssetSources(), // TODO support asset sources
//            'transforms'      => $this->_getTransforms(), // TODO support transforms
        );

        if ($settings['value'] instanceof RichTextData)
        {
            $settings['value'] = $settings['value']->getRawContent();
        }

        if (strpos($settings['value'], '{') !== false)
        {
            // Preserve the ref tags with hashes {type:id:url} => {type:id:url}#type:id
            $settings['value'] = preg_replace_callback('/(href=|src=)([\'"])(\{(\w+\:\d+\:'.HandleValidator::$handlePattern.')\})(#[^\'"#]+)?\2/', function($matches)
            {
                return $matches[1].$matches[2].$matches[3].(!empty($matches[5]) ? $matches[5] : '').'#'.$matches[4].$matches[2];
            }, $settings['value']);

            // Now parse 'em
            $settings['value'] = craft()->elements->parseRefs($settings['value']);
        }

        $html = '<rich-text v-bind:config="{{ config|json_encode() }}"></rich-text>';

        return craft()->templates->renderString($html, [ 'config' => $settings ]);
    }

    /**
     * Returns the Redactor config JSON used by this field.
     *
     * TODO: convert to Trumbowyg config object
     *
     * @return string
     */
    private function _getConfigJson()
    {
        if ($this->_field->getFieldType()->getSettings()->configFile)
        {
            $configPath = craft()->path->getConfigPath().'redactor/'.$this->_field->getFieldType()->getSettings()->configFile;
            $json = IOHelper::getFileContents($configPath);
        }

        if (empty($json))
        {
            $json = '{}';
        }

        return $json;
    }

}