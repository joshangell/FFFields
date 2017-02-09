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
    // Properties
    // =========================================================================

    /**
     * The original template path.
     *
     * @var string
     */
    protected $oldPath;

    /**
     * Our plugin template path.
     *
     * @var string
     */
    protected $newPath;

    // Public Methods
    // =========================================================================

    /**
     * Set the template path to our plugin one
     */
    public function __construct()
    {
        $this->oldPath = craft()->templates->getTemplatesPath();
        $this->newPath = craft()->path->getPluginsPath().'fffields/templates';
        craft()->templates->setTemplatesPath($this->newPath);
    }

    /**
     * Reset the parent path when the class gets killed
     */
    public function __destruct()
    {
        craft()->templates->setTemplatesPath($this->oldPath);
    }

    /**
     * Renders the field layout for a given element.
     *
     * @param BaseElementModel $element
     *
     * @return mixed
     */
    public function render(BaseElementModel $element)
    {

        $elementType = craft()->elements->getElementTypeById($element->id);

        if (!$elementType) {
            return false;
        }

        $fieldLayout = false;

        switch ($elementType) {
            case 'Entry' :
                $fieldLayout = $element->getType()->getFieldLayout();
                break;
        }

        if (!$fieldLayout) {
            return false;
        }

        $html = craft()->templates->render('fieldlayout', [
            'element' => $element,
            'fieldLayout' => $fieldLayout
        ]);

        return TemplateHelper::getRaw($html);
    }

    /**
     * Renders the markup for a specific field.
     *
     * TODO: convert params to object
     *
     * @param BaseElementModel      $element
     * @param FieldLayoutFieldModel $fieldLayoutField
     * @param                       $namespace
     *
     * @return \Twig_Markup
     */
    public function renderField(BaseElementModel $element, FieldLayoutFieldModel $fieldLayoutField, $namespace)
    {
        $field = $fieldLayoutField->getField();
        $value = ($element ? $element->getFieldValue($field->handle) : null);
        $fieldType = $field->getFieldType();

        if ($fieldType) {

            if ($element) {
                $fieldType->setElement($element);
            }

            $input = $this->getInputHtml($element, $fieldLayoutField, $value, $namespace);

        } else {

            $input = '<div class="field"><div class="ui error message visible">' . Craft::t("The fieldtype class “{class}” could not be found.", [ 'class' => $field->type ]) . '</div></div>';

        }

        $template = $this->getFieldTemplate($element, $fieldLayoutField, $namespace);


        $html = craft()->templates->renderString($template, [ 'input' => $input ]);

        return TemplateHelper::getRaw($html);
    }


    // TODO: convert params to object
    public function getFieldTemplate(BaseElementModel $element, FieldLayoutFieldModel $fieldLayoutField, $namespace)
    {
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

        $html = "<div class='{$fieldClass}' id='{$fieldId}'>";

        if ($label) {
            $html .= "<label id='{$labelId}' for='{$id}'>{$label}</label>";
        }

        if ($instructions) {
            $html .= "<div class='instructions'>{$instructions}</div>";
            // TODO: |md|replace('/&amp;(\\w+);/', '&$1;')|raw
        }


        $html .= "{{ input|raw }}";

        // TODO: errors
        // {% include "_includes/forms/errorList" with { errors: errors } %}

        $html .= "</div>";

        return $html;
    }

    /**
     * Gets the input html for a given field.
     *
     * TODO: convert params to object
     *
     * @param BaseElementModel      $element
     * @param FieldLayoutFieldModel $fieldLayoutField
     * @param                       $value
     * @param null                  $namespace
     *
     * @return string
     */
    public function getInputHtml(BaseElementModel $element, FieldLayoutFieldModel $fieldLayoutField, $value, $namespace = null)
    {

        $fieldType = $fieldLayoutField->getField()->type;

        switch ($fieldType) {

            case 'PlainText' :
                return craft()->fffields_basic->renderPlainText($fieldLayoutField, $value, $namespace);
                break;

            case 'Lightswitch' :
                return craft()->fffields_basic->renderLightswitch($element, $fieldLayoutField, $value, $namespace);
                break;

            case 'RichText' :
                return craft()->fffields_richText->render($fieldLayoutField, $value, $namespace);

            case 'Matrix' :
                return craft()->fffields_matrix->render($element, $fieldLayoutField, $value, $namespace);

            default :
                return '<div class="ui warning message visible">' . Craft::t("The fieldtype “{class}” is not yet supported.", ['class' => $fieldType]) . '</div>';
                break;

        }

    }

    // TODO: document these methods
    // TODO: convert params to object
    // =========================================================================

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

            case 'Lightswitch' :
                return 'lightswitch';
                break;

            case 'RichText' :
                return 'rich-text';

            default :
                return '';
                break;

        }
    }


    public function getComponentConfig(BaseElementModel $element, FieldModel $field, $value, $namespace = null)
    {

        switch ($field->type) {

            case 'PlainText' :
                return craft()->fffields_basic->getPlainTextConfig($field, $value, $namespace);
                break;

            case 'Lightswitch' :
                return craft()->fffields_basic->getLightswitchConfig($element, $field, $value, $namespace);
                break;

            case 'RichText' :
                return craft()->fffields_richText->getConfig($field, $value, $namespace);

            default :
                return '{}';
                break;

        }

    }

}