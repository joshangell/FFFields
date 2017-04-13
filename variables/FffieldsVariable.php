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
     * @param array $params
     *
     * @return mixed
     */
    public function renderField(array $params)
    {
        return craft()->fffields->renderField($params);
    }

    /**
     * Returns the current date in the locales format.
     *
     * @return string
     */
    public function getLocaleDate()
    {
        $date = DateTimeHelper::currentUTCDateTime();
        return $date->localeDate();
    }

    /**
     * Returns the current time in the locales format.
     *
     * @return string
     */
    public function getLocaleTime()
    {
        $date = DateTimeHelper::currentUTCDateTime();
        return $date->localeTime();
    }

}