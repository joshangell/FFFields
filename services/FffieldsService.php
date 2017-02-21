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
class FffieldsService extends BaseApplicationComponent
{

    // Public Methods
    // =========================================================================

    /**
     * Renders the field layout for a given element and optional field layout.
     *
     * @param BaseElementModel      $element
     * @param FieldLayoutModel|null $fieldLayout
     *
     * @return bool|\Twig_Markup
     */
    public function render(BaseElementModel $element, FieldLayoutModel $fieldLayout = null)
    {

        if (!$fieldLayout) {

            $elementType = craft()->elements->getElementTypeById($element->id);

            if (!$elementType) {
                return false;
            }

            switch ($elementType) {
                case 'Entry' :
                    $fieldLayout = $element->getType()->getFieldLayout();
                    break;
            }

        }

        if (!$fieldLayout) {
            return false;
        }

        $oldPath = craft()->templates->getTemplatesPath();
        $newPath = craft()->path->getPluginsPath().'fffields/templates';
        craft()->templates->setTemplatesPath($newPath);

        $html = craft()->templates->render('fieldlayout', [
            'element' => $element,
            'fieldLayout' => $fieldLayout
        ]);

        craft()->templates->setTemplatesPath($oldPath);

        return TemplateHelper::getRaw($html);
    }

    /**
     * Renders the markup for a specific field.
     *
     * @param array $params
     *
     * @return bool|\Twig_Markup
     */
    public function renderField(array $params)
    {

        if (!isset($params['element'], $params['fieldLayoutField'], $params['namespace'])) {
            return false;
        }

        $element = $params['element'];
        $fieldLayoutField = $params['fieldLayoutField'];

        $field = $fieldLayoutField->getField();
        $params['value'] = ($element ? $element->getFieldValue($field->handle) : null);
        $fieldType = $field->getFieldType();

        if ($fieldType) {

            if ($element) {
                $fieldType->setElement($element);
            }

            $template = $this->getFieldTemplate($params);

            $html = craft()->templates->renderString($template);

        } else {

            $config = [
                'class' => 'ui error message visible',
                'message' =>  Craft::t("The fieldtype class “{class}” could not be found.", [ 'class' => $field->type ])
            ];

            $template = '<message v-bind:config="{{ config|json_encode() }}"></message>';

            $html = craft()->templates->renderString($template, [ 'config' => $config ]);

        }

        return TemplateHelper::getRaw($html);
    }

    /**
     * Renders a <matrix/> of Variants for use with Commerce.
     *
     * @param array $params
     */
    public function renderCommerceVariants(array $params)
    {

        if (!isset($params['product'])) {
            return false;
        }

        $name = 'variants';
        if (isset($params['name'])) {
            $name = $params['name'];
        }


        // TODO variant errors
        $errors = null;
//        $errors = ($params['product'] ? $params['product']->getErrors($field->handle) : null);

        $labelId = $name . '-label';
        $fieldId = $name . '-field';
        $label   = Craft::t($name);

        $fieldClass = [
            'field',
            ($errors ? 'error' : null)
        ];

        $fieldClass = implode(array_filter($fieldClass), ' ');

        $config = [
            'id' => $name,
            'class' => $fieldClass,
            'label' => Craft::t('Product Variants'),
            'labelId' => $labelId,
            'fieldId' => $fieldId,
            'instructions' => null,
            'field' => [
                'type' => 'matrix',
                'config' => craft()->fffields_matrix->getVariantsConfig([
                    'product' => $params['product'],
                    'name' => $name
                ])
            ]
        ];

        $template = '<field v-bind:config="{{ config|json_encode() }}"></field>';

        $html = craft()->templates->renderString($template, [ 'config' => $config ]);

        return TemplateHelper::getRaw($html);

    }

    /**
     * Returns the wrapping <field/> custom tag.
     *
     * @param $params
     *
     * @return string
     */
    public function getFieldTemplate($params)
    {
        $config = $this->getFieldConfig($params);

        $html = '<field v-bind:config="{{ config|json_encode() }}"></field>';

        return craft()->templates->renderString($html, [ 'config' => $config ]);
    }

    /**
     * Returns the required config for a <field/> custom tag.
     *
     * @param $params
     *
     * @return array
     */
    public function getFieldConfig($params)
    {
        $element = $params['element'];
        $fieldLayoutField = $params['fieldLayoutField'];

        $field = $fieldLayoutField->getField();
        $errors = ($element ? $element->getErrors($field->handle) : null);
        $instructions = ($field->instructions ? Craft::t($field->instructions) : null);
        $id = $field->handle;

        $labelId = $field->handle . '-label';
        $fieldId = $field->handle . '-field';
        $label   = Craft::t($field->name); // TODO: |e

        $fieldClass = [
            'field',
            ($errors ? 'error' : null),
            ($fieldLayoutField->required ? 'required' : null)
        ];

        $fieldClass = implode(array_filter($fieldClass), ' ');

        // TODO: this on instructions: |md|replace('/&amp;(\\w+);/', '&$1;')|raw

        // TODO: errors
        // {% include "_includes/forms/errorList" with { errors: errors } %}

        $config = [
            'id' => $id,
            'class' => $fieldClass,
            'label' => $label,
            'labelId' => $labelId,
            'fieldId' => $fieldId,
            'instructions' => $instructions,
            'field' => [
                'type' => $this->getComponentType($field),
                'config' => $this->getComponentConfig($params),
            ]
        ];

        return $config;

    }

    /**
     * Returns the type of custom tag a given field type should use.
     *
     * @param FieldModel $field
     *
     * @return string
     */
    public function getComponentType(FieldModel $field)
    {

        $settings = $field->getFieldType()->getSettings();

        switch ($field->type) {

            case 'PlainText' :
                if ($settings->multiline) {
                    return 'text-area';
                } else {
                    return 'text-input';
                }
                break;

            case 'Number' :
                return 'text-input';
                break;

            case 'Lightswitch' :
                return 'lightswitch';
                break;

            case 'Dropdown' :
                return 'dropdown';
                break;

            case 'Checkboxes' :
                return 'checkboxes';
                break;

            case 'RadioButtons' :
                return 'radio-buttons';
                break;

            case 'MultiSelect' :
                return 'multi-select';
                break;

            case 'RichText' :
                return 'rich-text';

            case 'Matrix' :
                return 'matrix';

            default :
                return 'message';
                break;

        }
    }

    /**
     * Returns the required config for a given field type’s custom tag.
     *
     * @param $params
     *
     * @return array
     */
    public function getComponentConfig($params)
    {
        $field = $params['fieldLayoutField']->getField();

        switch ($field->type) {

            case 'PlainText' :
                return craft()->fffields_basic->getPlainTextConfig($params);
                break;

            case 'Number' :
                return craft()->fffields_basic->getNumberConfig($params);
                break;

            case 'Lightswitch' :
                return craft()->fffields_basic->getLightswitchConfig($params);
                break;

            case 'Dropdown' :
            case 'Checkboxes' :
            case 'RadioButtons' :
            case 'MultiSelect' :
                return craft()->fffields_basic->getOptionsConfig($params);
                break;

            case 'RichText' :
                return craft()->fffields_richText->getConfig($params);

            case 'Matrix' :
                return craft()->fffields_matrix->getConfig($params);

            default :
                return [
                    'class' => 'ui warning message visible',
                    'message' =>  Craft::t("The fieldtype “{class}” is not yet supported.", ['class' => $field->type])
                ];
                break;

        }

    }

}