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
        $elements = $this->transformCriteria($criteria, [
            'name' => $name,
            'viewMode' => $settings->viewMode,
            'context' => 'field'
        ]);

        // Work out any weird selection criteria
        // TODO: Add `kind` from field settings
        $selectionCriteria = [
            'localeEnabled' => null,
            'locale' => craft()->getLanguage()
        ];

        // Override source if folder is set
        $assetsFolderId = false;
        if (isset($params['assetsFolderId']) && !is_null($params['assetsFolderId'])) {
            $assetsFolderId = $params['assetsFolderId'];
            $folder = craft()->assets->getFolderById($assetsFolderId);
            $selectionCriteria['sourceId'] = $folder->sourceId;
        }

        // Return the config
        $config = [
            'id'             => $id,
            'name'           => $name,
            'elements'       => $elements,
            'sources'        => $assetsFolderId ? ['folder:'.$assetsFolderId.':single'] : $settings->sources,
            'criteria'       => $selectionCriteria,
            'limit'          => $settings->limit,
            'viewMode'       => $settings->viewMode,
            'fieldId'        => $field->id,
            'selectionLabel' => ($settings->selectionLabel ? Craft::t($settings->selectionLabel) : Craft::t('Add an asset'))
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
        $size = ($params['viewMode'] === 'large' ? 200 : 50);

        $disabledElementIds = [];
        if (isset($params['disabledElementIds'])) {
            $disabledElementIds = $params['disabledElementIds'];
        }

        if ($criteria) {
            foreach ($criteria as $element) {
                if ($element->hasThumb())
                {
                    $thumbUrl = $element->getUrl([
                        'width' => $size,
                        'height' => $size,
                        'mode' => 'crop'
                    ]) . '?d=' . $element->dateModified->getTimestamp();
                }
                else
                {
                    $thumbUrl = UrlHelper::getResourceUrl('icons/'.$element->getExtension());
                }

                $elements[] = [
                    'id'           => $element->id,
                    'name'         => $params['name'] . '[]',
                    'context'      => $params['context'],
                    'label'        => HtmlHelper::encode($element),
                    'viewMode'     => $params['viewMode'],
                    'disabled'     => in_array($element->id, $disabledElementIds),
                    'thumbUrl'     => $thumbUrl,
                    'filename'     => $element->filename,
                    'size'         => craft()->formatter->formatSize($element->size),
                    'dateModified' => $element->dateModified->localeDate()
                ];
            }
        }

        return $elements;
    }

}