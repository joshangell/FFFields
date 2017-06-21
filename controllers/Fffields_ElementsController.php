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
class Fffields_ElementsController extends BaseController
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the application component.
     *
     * @throws HttpException
     * @return null
     */
    public function init()
    {
        // Only support JSON responses
        $this->requireAjaxRequest();
    }

    /**
     * TODO
     */
    public function actionGetAssets()
    {
        $sourceKeys = craft()->request->getParam('sources');
        $elementType = $this->getElementType();
        $context = craft()->request->getParam('context');
        $name = craft()->request->getParam('fieldName');
        $fieldId = craft()->request->getParam('fieldId');
        $disabledElementIds = craft()->request->getParam('disabledElementIds', array());

        if ($fieldId) {
            $field = craft()->fields->getFieldById($fieldId);
            $viewMode = $field->getFieldType()->getSettings()->viewMode;
        } else {
            $viewMode = 'large';
        }

        if (is_array($sourceKeys)) {
            $sources = [];

            foreach ($sourceKeys as $key) {
                $source = $elementType->getSource($key, 'field');

                if ($source) {
                    $sources[$key] = $source;
                }
            }
        } else {
            $sources = craft()->elementIndexes->getSources($elementType->getClassHandle(), $context);
        }

        $source = ArrayHelper::getFirstValue($sources);

//        'showSidebar' => (count($sources) > 1 || ($sources && !empty($source['nested']))),

        $criteria = craft()->elements->getCriteria($elementType->getClassHandle());


        // Does the source specify any criteria attributes?
        if (!empty($source['criteria']))
        {
            $criteria->setAttributes($source['criteria']);
        }

        $criteria->limit = null;

        $this->returnJson([
            'success' => true,
            'elements' => craft()->fffields_assets->transformCriteria($criteria, [
                'name' => $name,
                'viewMode' => $viewMode,
                'context' => $context,
                'disabledElementIds' => $disabledElementIds
            ])
        ]);

    }

    public function actionGetCategories()
    {
        $sourceKey = craft()->request->getParam('source');
        $elementType = $this->getElementType();
        $context = craft()->request->getParam('context');
        $name = craft()->request->getParam('fieldName');
        $fieldId = craft()->request->getParam('fieldId');
        $disabledElementIds = craft()->request->getParam('disabledElementIds', array());

        $field = craft()->fields->getFieldById($fieldId);

        $source = $elementType->getSource($sourceKey, 'field');

        $criteria = craft()->elements->getCriteria($elementType->getClassHandle());


        // Does the source specify any criteria attributes?
        if (!empty($source['criteria']))
        {
            $criteria->setAttributes($source['criteria']);
        }

        $criteria->limit = null;

        $this->returnJson([
            'success' => true,
            'elements' => craft()->fffields_categories->transformCriteria($criteria, [
                'name' => $name,
                'context' => $context,
                'disabledElementIds' => $disabledElementIds
            ])
        ]);

    }


    // Protected Methods
    // =========================================================================

    /**
     * Returns the element type based on the posted element type class.
     *
     * @throws Exception
     * @return BaseElementType
     */
    protected function getElementType()
    {
        $class = craft()->request->getRequiredParam('elementType');
        $elementType = craft()->elements->getElementType($class);

        if (!$elementType)
        {
            throw new Exception(Craft::t('No element type exists with the class “{class}”', array('class' => $class)));
        }

        return $elementType;
    }

}
