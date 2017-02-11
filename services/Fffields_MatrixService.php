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
        $settings = $field->getFieldType()->getSettings();

        $blocks = [];
        $totalNewBlocks = 0;

        foreach ($value->find(['status'=>null]) as $block) {

            $blockId = $block->id;

            if (!$blockId) {
                $totalNewBlocks++;
                $blockId = 'new' . $totalNewBlocks;
            }

            $fields = [];

            $blockTypeFieldLayout = $block->type->getFieldLayout();

            $matrixNamespace = $name.'['.$blockId.'][fields]';

            foreach ($blockTypeFieldLayout->getFields() as $blockFieldLayoutField) {

                $blockField = $blockFieldLayoutField->getField();

                $blockFieldValue = ($block ? $block->getFieldValue($blockField->handle) : null);

                $fields[] = [
                    'handle' => $blockField->handle,
                    'config' => craft()->fffields->getFieldConfig($element, $blockFieldLayoutField, $blockFieldValue, $matrixNamespace)
                ];
            }

            $blocks[] = [
                'name' => $block->type->name,
                'type' => [
                    'name' => $name.'['.$blockId.'][type]',
                    'value' => $block->type->handle,
                ],
                'enabled' => [
                    'name' => $name.'['.$blockId.'][enabled]',
                    'value' => $block->enabled,
                ],
                'fields' => $fields
            ];
        }

        $blockTypes = [];

        foreach ($settings->getBlockTypes() as $blockType) {

            $fields = [];

            $blockTypeFieldLayout = $blockType->getFieldLayout();

            $matrixNamespace = $name.'[__block__][fields]';

            foreach ($blockTypeFieldLayout->getFields() as $blockFieldLayoutField) {

                $blockField = $blockFieldLayoutField->getField();

                $fields[] = [
                    'handle' => $blockField->handle,
                    'config' => craft()->fffields->getFieldConfig($element, $blockFieldLayoutField, null, $matrixNamespace)
                ];
            }

            $blockTypes[] = [
                'name' => $blockType->name,
                'type' => [
                    'name' => $name.'[__block__][type]',
                    'value' => $blockType->handle,
                ],
                'enabled' => [
                    'name' => $name.'[__block__][enabled]',
                    'value' => '1'
                ],
                'fields' => $fields
            ];
        }

        $config = [
            'id'         => $id,
            'name'       => $name,
            'blocks'     => $blocks,
            'blockTypes' => $blockTypes
        ];

        return $config;

    }

}