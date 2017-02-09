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
     * Renders the fields for a given element.
     *
     * @param BaseElementModel $element
     *
     * @return bool
     */
    public function render(BaseElementModel $element)
    {
        return craft()->fffields->render($element);
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