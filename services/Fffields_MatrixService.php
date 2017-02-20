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

    /**
     * Returns the required config for a <matrix/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getConfig(array $params)
    {

        $field = $params['fieldLayoutField']->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        $blocks = [];
        $totalNewBlocks = 0;

        foreach ($params['value']->find(['status'=>null]) as $block) {

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
                    'config' => craft()->fffields->getFieldConfig([
                        'element' => $params['element'],
                        'fieldLayoutField' => $blockFieldLayoutField,
                        'value' => $blockFieldValue,
                        'namespace' => $matrixNamespace

                    ])
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

            $matrixNamespace = $name.'[__BLOCK__][fields]';

            foreach ($blockTypeFieldLayout->getFields() as $blockFieldLayoutField) {

                $blockField = $blockFieldLayoutField->getField();

                $fields[] = [
                    'handle' => $blockField->handle,
                    'config' => craft()->fffields->getFieldConfig([
                        'element' => $params['element'],
                        'fieldLayoutField' => $blockFieldLayoutField,
                        'value' => null,
                        'namespace' => $matrixNamespace
                    ])
                ];
            }

            $blockTypes[] = [
                'name' => $blockType->name,
                'type' => [
                    'name' => $name.'[__BLOCK__][type]',
                    'value' => $blockType->handle,
                ],
                'enabled' => [
                    'name' => $name.'[__BLOCK__][enabled]',
                    'value' => '1'
                ],
                'fields' => $fields
            ];
        }

        $config = [
            'id'             => $id,
            'name'           => $name,
            'blocks'         => $blocks,
            'blockTypes'     => $blockTypes,
            'totalNewBlocks' => $totalNewBlocks
        ];

        return $config;

    }

    /**
     * Returns the required config for a <matrix/> custom tag as used
     * for Commerce Variants
     *
     * @param array $params
     *
     * @return array
     */
    public function getVariantsConfig(array $params)
    {
        $id = craft()->templates->namespaceInputId($params['name']);
        $name = craft()->templates->namespaceInputName($params['name']);

        $variants = [];
        $totalNewVariants = 0;

        foreach ($params['product']->getVariants() as $variant) {

            $variantId = $variant->id;

            if (!$variantId) {
                $totalNewVariants++;
                $variantId = 'new' . $totalNewVariants;
            }

            $fields = [];

//            $blockTypeFieldLayout = $block->type->getFieldLayout();

            $matrixNamespace = $name.'['.$variantId.']';
//
//            foreach ($blockTypeFieldLayout->getFields() as $blockFieldLayoutField) {
//
//                $blockField = $blockFieldLayoutField->getField();
//
//                $blockFieldValue = ($block ? $block->getFieldValue($blockField->handle) : null);
//
//                $fields[] = [
//                    'handle' => $blockField->handle,
//                    'config' => craft()->fffields->getFieldConfig([
//                        'element' => $params['element'],
//                        'fieldLayoutField' => $blockFieldLayoutField,
//                        'value' => $blockFieldValue,
//                        'namespace' => $matrixNamespace
//
//                    ])
//                ];
//            }

            $variants[] = [
                'name' => 'Variant',
//                'type' => [
//                    'name' => $name.'['.$variantId.'][type]',
//                    'value' => null,
//                ],
                'enabled' => [
                    'name' => $name.'['.$variantId.'][enabled]',
                    'value' => $variant->enabled,
                ],
                'fields' => $fields
            ];
        }


        $blockTypes[] = [
            'name' => 'variant',
//            'type' => [
//                'name' => $name.'[__BLOCK__][type]',
//                'value' => null,
//            ],
            'enabled' => [
                'name' => $name.'[__BLOCK__][enabled]',
                'value' => '1'
            ],
            'fields' => $fields
        ];


        $config = [
            'id'             => $id,
            'name'           => $name,
            'blocks'         => $variants,
            'blockTypes'     => $blockTypes,
            'totalNewBlocks' => $totalNewVariants
        ];

        return $config;
    }

}