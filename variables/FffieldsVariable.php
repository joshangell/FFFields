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
     * Renders the input html for a given set of parameters.
     *
     * @param BaseElementModel $element
     * @param FieldModel       $field
     * @param                  $value
     * @param null             $namespace
     *
     * @return mixed
     */
    public function getInputHtml(BaseElementModel $element, FieldModel $field, $value, $namespace = null)
    {
        return craft()->fffields->getInputHtml($element, $field, $value, $namespace);
    }

}