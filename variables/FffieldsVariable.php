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

        return craft()->fffields->renderFromLayout($fieldLayout);

    }

}