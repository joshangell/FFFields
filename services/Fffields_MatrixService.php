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


        // Create a fake Variant model so the field types have a way to get at the owner element, if there is one
        $variant = new Commerce_VariantModel();
        $variant->setProduct($params['product']);

        list($meta, $fields) = $this->_getVariantFields($variant, '__BLOCK__', $name);

        // Store the meta and fields for the blockTypes array
        $blockTypes[] = [
            'name' => Craft::t('Variant'),
            'enabled' => [
                'name' => $name.'[__BLOCK__][enabled]',
                'value' => '1'
            ],
            'meta'   => $meta,
            'fields' => $fields
        ];


        // Now go over the existing variants and load up the meta and fields for them too
        foreach ($params['product']->getVariants() as $variant) {

            $variantId = $variant->id;

            if (!$variantId) {
                $totalNewVariants++;
                $variantId = 'new' . $totalNewVariants;
            }

            // Need to re-build the fields and variants so we get inline errors and values etc

//            $metaFields = json_decode(str_replace('__VARIANT__', $variantId, json_encode($meta)));
//            $variantFields = json_decode(str_replace('__VARIANT__', $variantId, json_encode($fields)));

            list($metaFields, $variantFields) = $this->_getVariantFields($variant, $variantId, $name);

            $variants[] = [
                'name' => Craft::t('Variant'),
                'enabled' => [
                    'name' => $name.'['.$variantId.'][enabled]',
                    'value' => $variant->enabled ? '1' : '0',
                ],
                'meta'   => $metaFields,
                'fields' => $variantFields
            ];
        }

        $config = [
            'id'             => $id,
            'name'           => $name,
            'blocks'         => $variants,
            'blockTypes'     => $blockTypes,
            'totalNewBlocks' => $totalNewVariants
        ];

        return $config;
    }

    // Private Methods
    // =========================================================================

    /**
     * Returns the field configs for both the meta and field layout for a variant.
     *
     * @param $variant
     * @param $variantId
     * @param $name
     *
     * @return array
     */
    private function _getVariantFields($variant, $variantId, $name)
    {
        $fields = [];
        $meta = [];

        // Sort out the custom fields for these variants
        $variantFieldLayout = $variant->getFieldLayout();

        $variantNamespace = $name.'['.$variantId.']';

        foreach ($variantFieldLayout->getFields() as $variantFieldLayoutField) {

            $variantField = $variantFieldLayoutField->getField();

            $fieldType = $variantField->getFieldType();

            if ($fieldType)
            {
                $fieldType->element = $variant;
                $fieldType->setIsFresh(true);
            }

            $variantFieldValue = ($variant ? $variant->getFieldValue($variantField->handle) : null);

            $fields[] = [
                'handle' => $variantField->handle,
                'config' => craft()->fffields->getFieldConfig([
                    'element' => $variant,
                    'fieldLayoutField' => $variantFieldLayoutField,
                    'value' => $variantFieldValue,
                    'namespace' => $variantNamespace.'[fields]'
                ])
            ];

            if ($fieldType)
            {
                $fieldType->setIsFresh(null);
            }

        }

        // Sort out the meta fields for these variants
        if ($variant->product->type->hasVariantTitleField) {
            $meta[] = [
                'handle' => 'title',
                'config' => $this->_getVariantMetaFieldConfig([
                    'name'        => 'title',
                    'label'       => Craft::t('Title'),
                    'placeholder' => Craft::t('Enter title'),
                    'required'    => true,
                    'variant'     => $variant,
                    'namespace'   => $variantNamespace
                ])
            ];
        }

        $meta[] = [
            'handle' => 'sku',
            'config' => $this->_getVariantMetaFieldConfig([
                'name'        => 'sku',
                'label'       => Craft::t('SKU'),
                'placeholder' => Craft::t('Enter SKU'),
                'required'    => true,
                'variant'     => $variant,
                'namespace'   => $variantNamespace
            ])
        ];

        $meta[] = [
            'handle' => 'price',
            'config' => $this->_getVariantMetaFieldConfig([
                'name'        => 'price',
                'label'       => Craft::t('Price'),
                'placeholder' => Craft::t('Enter price'),
                'required'    => true,
                'variant'     => $variant,
                'namespace'   => $variantNamespace
            ])
        ];

        $meta[] = [
            [
                'handle' => 'stock',
                'config' => $this->_getVariantMetaFieldConfig([
                    'name'        => 'stock',
                    'label'       => Craft::t('Stock'),
                    'placeholder' => null,
                    'required'    => true,
                    'variant'     => $variant,
                    'namespace'   => $variantNamespace
                ])
            ],
            [
                'handle' => 'unlimitedStock',
                'config' => $this->_getVariantMetaFieldConfig([
                    'name'        => 'unlimitedStock',
                    'label'       => Craft::t('Unlimited?'),
                    'placeholder' => null,
                    'variant'     => $variant,
                    'namespace'   => $variantNamespace
                ])
            ]
        ];

        $meta[] = [
            [
                'handle' => 'minQty',
                'config' => $this->_getVariantMetaFieldConfig([
                    'name'        => 'minQty',
                    'label'       => Craft::t('Min quantity'),
                    'placeholder' => Craft::t('Any'),
                    'variant'     => $variant,
                    'namespace'   => $variantNamespace
                ])
            ],
            [
                'handle' => 'maxQty',
                'config' => $this->_getVariantMetaFieldConfig([
                    'name'        => 'maxQty',
                    'label'       => Craft::t('Max quantity'),
                    'placeholder' => Craft::t('Any'),
                    'variant'     => $variant,
                    'namespace'   => $variantNamespace
                ])
            ]
        ];

        return [$meta, $fields];

    }

    /**
     * Returns the config for the simple meta fields needed on variant blocks.
     *
     * @param array $params
     *
     * @return array
     */
    private function _getVariantMetaFieldConfig(array $params)
    {
        $errors = ($params['variant'] ? $params['variant']->getErrors($params['name']) : null);
        $required = (isset($params['required']) && $params['required'] ? true : false);


        $labelId = $params['name'] . '-label';
        $fieldId = $params['name'] . '-field';

        $fieldClass = [
            'field',
            ($errors ? 'error' : null),
            ($required ? 'required' : null)
        ];

        $fieldClass = implode(array_filter($fieldClass), ' ');


        // TODO: errors
        //            $variant->getErrors('title')
        //        variant.getErrors('unlimitedStock')|merge(variant.getErrors('stock'))


        $id = craft()->templates->namespaceInputId($params['name'], $params['namespace']);
        $name = craft()->templates->namespaceInputName($params['name'], $params['namespace']);


        switch ($params['name']) {

            case 'price' :
                $value = craft()->numberFormatter->formatDecimal($params['variant']->price, false);

                if ($value === '0') {
                    $value = '';
                }

                $fieldConfig = [
                    'type' => 'text-input',
                    'config' => [
                        'id'          => $id,
                        'name'        => $name,
                        'type'        => 'number',
                        'value'       => $value,
                        'leftLabel'   => craft()->i18n->getLocaleData()->getCurrencySymbol(craft()->commerce_paymentCurrencies->getPrimaryPaymentCurrency()->iso),
                        'placeholder' => $params['placeholder'],
                        'min'         => 0,
                        'step'        => 0.01
                    ]
                ];
                break;


            case 'stock' :
                $value = ($params['variant']->unlimitedStock ? '' : $params['variant']->stock == '0' ? '0': ($params['variant']->stock ? $params['variant']->stock : ''));

                $fieldConfig = [
                    'type' => 'text-input',
                    'config' => [
                        'id'          => $id,
                        'name'        => $name,
                        'type'        => 'number',
                        'value'       => $value,
                        'placeholder' => $params['placeholder']
                    ]
                ];
                break;

            case 'unlimitedStock' :
                $fieldConfig = [
                    'type' => 'lightswitch',
                    'config' => [
                        'id'    => $id,
                        'name'  => $name,
                        'value' => (bool) $params['variant']->unlimitedStock,
                        'toggle' => [
                            'selector' => '#'.craft()->templates->namespaceInputId('stock', $params['namespace']),
                            'attribute' => 'disabled'
                        ]
                    ]
                ];
                break;

            case 'minQty' :
            case 'maxQty' :
                $value = craft()->numberFormatter->formatDecimal($params['variant'][$params['name']], false);

                if ($value === '0') {
                    $value = '';
                }

                $fieldConfig = [
                    'type' => 'text-input',
                    'config' => [
                        'id'          => $id,
                        'name'        => $name,
                        'type'        => 'number',
                        'value'       => $value,
                        'placeholder' => $params['placeholder'],
                    ]
                ];
                break;

            default;
                $fieldConfig = [
                    'type' => 'text-input',
                    'config' => [
                        'id'          => $id,
                        'name'        => $name,
                        'type'        => 'text',
                        'value'       => $params['variant'][$params['name']],
                        'placeholder' => $params['placeholder']
                    ]
                ];
                break;
        }

        return [
            'id'           => $params['name'],
            'class'        => $fieldClass,
            'label'        => $params['label'],
            'labelId'      => $labelId,
            'fieldId'      => $fieldId,
            'instructions' => null,
            'field'        => $fieldConfig
        ];
    }

}