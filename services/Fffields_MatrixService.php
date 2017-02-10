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

    // Public Methods
    // =========================================================================


    public function getConfig(BaseElementModel $element, FieldLayoutFieldModel $fieldLayoutField, $value, $namespace)
    {

        $field = $fieldLayoutField->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $namespace);
        $name = craft()->templates->namespaceInputName($field->handle, $namespace);
//        $settings = $field->getFieldType()->getSettings();

        $blocks = [];

        foreach ($value->find() as $block) {

            $fields = [];

            $blockTypeFieldLayout = $block->type->getFieldLayout();

            foreach ($blockTypeFieldLayout->getFields() as $blockFieldLayoutField) {

                $blockField = $blockFieldLayoutField->getField();

                $blockFieldValue = ($block ? $block->getFieldValue($blockField->handle) : null);

                // TODO: work out what each field’s value is
                $fields[] = [
                    'handle' => $blockField->handle,
                    'config' => craft()->fffields->getFieldConfig($element, $blockFieldLayoutField, $blockFieldValue, $namespace)
//                    'component'  => [
//                        'type' => craft()->fffields->getComponentType($blockField),
//                        'config' => craft()->fffields->getComponentConfig($element, $blockField, null, $namespace)
//                    ]
                ];
            }

            $blocks[] = [
                'name'   => $block->type->name,
                'fields' => $fields
            ];
        }

        $config = [
            'id'            => $id,
            'name'          => $name,
            'blocks'        => $blocks,
//            'blockTypes'    => $settings->getBlockTypes(),
        ];

//        Craft::dd($config);

        return $config;

    }

}