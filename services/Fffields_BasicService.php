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

    /**
     * Returns the required config for a <plain-text/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getPlainTextConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        $config = [
            'id'            => $id,
            'name'          => $name,
            'value'         => $params['value'],
            'maxlength'     => $settings->maxLength,
            'showCharsLeft' => $settings->maxLength ? true : false,
            'placeholder'   => Craft::t($settings->placeholder),
            'rows'          => $settings->initialRows,
            'multiline'     => $settings->multiline
        ];

        return $config;

    }

    /**
     * Returns the required config for a <lightswitch/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getLightswitchConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();
        $value = $params['value'];

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        if ($params['element']->getHasFreshContent()) {
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