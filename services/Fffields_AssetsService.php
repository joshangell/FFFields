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
class Fffields_AssetsService extends BaseApplicationComponent
{

    // Public Methods
    // =========================================================================

    /**
     * Returns the required config for an <assets/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getConfig(array $params)
    {

        $field = $params['fieldLayoutField']->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();


        // Sort out the element criteria object
        $criteria = $params['value'];

        if (!($criteria instanceof ElementCriteriaModel))
        {
            $criteria = craft()->elements->getCriteria(ElementType::Asset);
            $criteria->id = false;
        }

        $criteria->status = null;
        $criteria->localeEnabled = null;

        // Transform that criteria model to elements our js can understand
        $elements = [];

        if ($criteria) {
            foreach ($criteria as $element) {
                $elements[] = [
                    'id'       => $element->id,
                    'name'     => $name . '[]',
                    'label'    => HtmlHelper::encode($element),
                    'thumbUrl' => ($settings->viewMode === 'large' ? $element->getThumbUrl(100) : $element->getThumbUrl(50))
                ];
            }
        }

        // Work out any weird selection criteria
        $selectionCriteria = [
            'localeEnabled' => null,
            'locale' => craft()->getLanguage()
        ];






        // Return the config
        $config = [
            'id'       => $id,
            'name'     => $name,
            'elements' => $elements,
            'sources'  => $settings->sources,
            'criteria' => $selectionCriteria
        ];


//        'jsClass'            => $this->inputJsClass,
//        'elementType'        => new ElementTypeVariable($this->getElementType()),
//        'id'                 => craft()->templates->formatInputId($name),
//        'fieldId'            => $this->model->id,
//        'storageKey'         => 'field.'.$this->model->id,
//        'name'               => $name,
//        'elements'           => $criteria,
//        'sources'            => $this->getInputSources(),
//        'criteria'           => $selectionCriteria,
//        'sourceElementId'    => (isset($this->element->id) ? $this->element->id : null),
//        'limit'              => ($this->allowLimit ? $settings->limit : null),
//        'viewMode'           => $this->getViewMode(),
//        'selectionLabel'     => ($settings->selectionLabel ? Craft::t($settings->selectionLabel) : $this->getAddButtonLabel())

        return $config;

    }

}