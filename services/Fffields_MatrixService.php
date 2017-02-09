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
class Fffields_MatrixService extends BaseApplicationComponent
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
     * Renders a <matrix/> custom element, with <block/> elements inside
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

        $id = craft()->templates->namespaceInputId($this->_field->handle, $namespace);
        $name = craft()->templates->namespaceInputName($this->_field->handle, $namespace);
        $settings = $this->_field->getFieldType()->getSettings();

        $config = [
            'id'            => $id,
            'name'          => $name,
            'value'         => $value,
        ];

        $html = '<matrix v-bind:config="{{ config|json_encode() }}"></matrix>';

        return craft()->templates->renderString($html, [ 'config' => $config ]);
    }

}