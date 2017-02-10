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
class Fffields_BasicService extends BaseApplicationComponent
{

    // Public Methods
    // =========================================================================

    // TODO: document these methods
    // TODO: convert params to object
    // =========================================================================

    public function getPlainTextConfig(FieldModel $field, $value, $namespace)
    {

        $id = craft()->templates->namespaceInputId($field->handle, $namespace);
        $name = craft()->templates->namespaceInputName($field->handle, $namespace);
        $settings = $field->getFieldType()->getSettings();

        $config = [
            'id'            => $id,
            'name'          => $name,
            'value'         => $value,
            'maxlength'     => $settings->maxLength,
            'showCharsLeft' => $settings->maxLength ? true : false,
            'placeholder'   => Craft::t($settings->placeholder),
            'rows'          => $settings->initialRows,
            'multiline'     => $settings->multiline
        ];

        return $config;

    }


    public function getLightswitchConfig(BaseElementModel $element, FieldModel $field, $value, $namespace)
    {
        $id = craft()->templates->namespaceInputId($field->handle, $namespace);
        $name = craft()->templates->namespaceInputName($field->handle, $namespace);
        $settings = $field->getFieldType()->getSettings();

        if ($element->getHasFreshContent()) {
            $value = $settings->default;
        }

        $config = [
            'id'    => $id,
            'name'  => $name,
            'value' => (bool) $value,
        ];

        return $config;
    }

}