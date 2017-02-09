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
     * Renders a <text-input/> or <text-area/> custom input
     *
     * @param FieldModel $field
     * @param            $value
     * @param            $namespace
     *
     * @return string
     */
    public function renderPlainText(FieldModel $field, $value, $namespace)
    {
        $config = $this->getPlainTextConfig($field, $value, $namespace);

        if ($config['multiline']) {
            $html = '<text-area v-bind:config="{{ config|json_encode() }}"></text-area>';
        } else {
            $html = '<text-input v-bind:config="{{ config|json_encode() }}"></text-input>';
        }

        return craft()->templates->renderString($html, [ 'config' => $config ]);
    }

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

    /**
     * Renders a <lightswitch/> custom input
     *
     * @param BaseElementModel $element
     * @param FieldModel       $field
     * @param                  $value
     * @param                  $namespace
     *
     * @return string
     */
    public function renderLightswitch(BaseElementModel $element, FieldModel $field, $value, $namespace)
    {

        $config = $this->getLightswitchConfig($element, $field, $value, $namespace);

        $html = '<lightswitch v-bind:config="{{ config|json_encode() }}"></lightswitch>';

        return craft()->templates->renderString($html, [ 'config' => $config ]);

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