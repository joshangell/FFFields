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
     * @param FieldModel $field
     * @param            $value
     *
     * @return mixed
     */
    public function getInputHtml(FieldModel $field, $value)
    {
        return craft()->fffields->getInputHtml($field, $value);
    }

}