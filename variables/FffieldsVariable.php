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
class FffieldsVariable
{

    /**
     * Renders the fields for a given element and optional field layout.
     *
     * @param BaseElementModel      $element
     * @param FieldLayoutModel|null $fieldLayout
     *
     * @return mixed
     */
    public function render(BaseElementModel $element, FieldLayoutModel $fieldLayout = null)
    {
        return craft()->fffields->render($element, $fieldLayout);
    }

    /**
     * Renders field html.
     *
     * @param BaseElementModel      $element
     * @param FieldLayoutFieldModel $fieldLayoutField
     * @param null                  $namespace
     *
     * @return mixed
     */
    public function renderField(BaseElementModel $element, FieldLayoutFieldModel $fieldLayoutField, $namespace = null)
    {
        return craft()->fffields->renderField($element, $fieldLayoutField, $namespace);
    }

}