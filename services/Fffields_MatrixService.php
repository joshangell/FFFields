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
     * TODO: convert params to object
     *
     * @param BaseElementModel $element
     * @param FieldModel       $field
     * @param                  $value
     * @param                  $namespace
     *
     * @return string
     */
    public function render(BaseElementModel $element, FieldModel $field, $value, $namespace)
    {

        $this->_field = $field;

        $id = craft()->templates->namespaceInputId($this->_field->handle, $namespace);
        $name = craft()->templates->namespaceInputName($this->_field->handle, $namespace);
//        $settings = $this->_field->getFieldType()->getSettings();

        $blocks = [];

        foreach ($value->find() as $block) {

            $fields = [];

            foreach ($block->type->getFields() as $blockField) {
                // TODO: work out what each field’s value is
                $fields[] = [
                    'handle' => $blockField->handle,
                    'component'  => [
                        'type' => craft()->fffields->getComponentType($blockField),
                        'config' => craft()->fffields->getComponentConfig($element, $blockField, null, $namespace),
                    ]
                ];
            }

            $blocks[] = [
                'name'   => $block->type->name,
                'type'   => $block->type->handle,
                'fields' => $fields
            ];
        }

        $config = [
            'id'            => $id,
            'name'          => $name,
            'blocks'        => $blocks,
//            'blockTypes'    => $settings->getBlockTypes(),
        ];

        $html = '<matrix v-bind:config="{{ config|json_encode() }}"></matrix>';

        return craft()->templates->renderString($html, [ 'config' => $config ]);
    }

}