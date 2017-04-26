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
class Fffields_CategoriesService extends BaseApplicationComponent
{

    // Public Methods
    // =========================================================================

    /**
     * Returns the required config for an <categories/> custom tag.
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
            $criteria = craft()->elements->getCriteria(ElementType::Category);
            $criteria->id = false;
        }

        $criteria->status = null;
        $criteria->localeEnabled = null;

        // Transform that criteria model to elements our js can understand
        $elements = $this->transformCriteria($criteria, [
            'name' => $name,
            'context' => 'field'
        ]);

        // Work out any weird selection criteria
        $selectionCriteria = [
            'localeEnabled' => null,
            'locale' => craft()->getLanguage()
        ];

        // Return the config
        $config = [
            'id'             => $id,
            'name'           => $name,
            'elements'       => $elements,
            'source'         => $settings->source,
            'criteria'       => $selectionCriteria,
            'limit'          => $settings->limit,
            'fieldId'        => $field->id,
            'selectionLabel' => ($settings->selectionLabel ? Craft::t($settings->selectionLabel) : Craft::t('Add a category'))
        ];

//        'elementType'        => new ElementTypeVariable($this->getElementType()),
//        'storageKey'         => 'field.'.$this->model->id,
//        'sourceElementId'    => (isset($this->element->id) ? $this->element->id : null),

        return $config;

    }

    /**
     * Transforms a criteria object into an array of elements.
     *
     * @param $criteria
     * @param $params
     *
     * @return array
     */
    public function transformCriteria($criteria, $params)
    {
        $elements = [];

        $disabledElementIds = [];
        if (isset($params['disabledElementIds'])) {
            $disabledElementIds = $params['disabledElementIds'];
        }

        if ($criteria) {
            foreach ($criteria as $element) {
                $elements[] = [
                    'id'           => $element->id,
                    'name'         => $params['name'] . '[]',
                    'context'      => $params['context'],
                    'label'        => HtmlHelper::encode($element),
                    'level'        => $element->level,
                    'disabled'     => in_array($element->id, $disabledElementIds),
                ];
            }
        }

        return $elements;
    }

}