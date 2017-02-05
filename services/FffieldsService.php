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

}