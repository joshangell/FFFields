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
     * Renders the fields for a given element.
     *
     * @param BaseElementModel $element
     *
     * @return mixed
     */
    public function render(BaseElementModel $element)
    {

        $elementType = craft()->elements->getElementTypeById($element->id);

        if (!$elementType)
        {
            return false;
        }

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
     * @param FieldModel $field
     * @param            $value
     *
     * @return string
     */
    public function getInputHtml(FieldModel $field, $value, $namespace = null)
    {
        $fieldType = $field->getFieldType();

        switch ($field->type) {

            case 'PlainText' :
                return craft()->templates->render('_fieldtypes/PlainText/input', array(
                    'id'       => craft()->templates->namespaceInputId($field->handle, $namespace),
                    'name'     => craft()->templates->namespaceInputName($field->handle, $namespace),
                    'value'    => $value,
                    'settings' => $fieldType->getSettings()
                ));
                break;

            case 'RichText' :
                return craft()->fffields_richText->render($field, $value, $namespace);

//            case 'Matrix' :
//                return craft()->fffields_matrix->render($field, $value, $namespace);

            default :
                return '<div class="ui warning message visible">' . Craft::t("The fieldtype “{class}” is not yet supported.", ['class' => $field->type]) . '</div>';
                break;

        }

    }

}