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
class Fffields_RichTextService extends BaseApplicationComponent
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    private static $_redactorLang = 'en';

    /**
     * @var
     */
    private $_field;

    // Public Methods
    // =========================================================================


    public function render(FieldModel $field, $value)
    {

        $this->_field = $field;

        $configJs = $this->_getConfigJson();
        $this->_includeFieldResources($configJs);

        $id = craft()->templates->formatInputId($this->_field->handle);
        $localeId = (isset($this->_field->element) ? $this->_field->element->locale : craft()->language);

        $settings = array(
            'id'              => craft()->templates->namespaceInputId($id),
//            'linkOptions'     => $this->_getLinkOptions(), // TODO support link options
//            'assetSources'    => $this->_getAssetSources(), // TODO support asset sources
//            'transforms'      => $this->_getTransforms(), // TODO support transforms
            'elementLocale'   => $localeId,
            'redactorConfig'  => JsonHelper::decode(JsonHelper::removeComments($configJs)),
            'redactorLang'    => static::$_redactorLang,
        );

        if (isset($this->_field->model) && $this->_field->model->translatable)
        {
            // Explicitly set the text direction
            $locale = craft()->i18n->getLocaleData($localeId);
            $settings['direction'] = $locale->getOrientation();
        }

//        craft()->templates->includeJs('new Craft.RichTextInput('.JsonHelper::encode($settings).');');

        if ($value instanceof RichTextData)
        {
            $value = $value->getRawContent();
        }

        if (strpos($value, '{') !== false)
        {
            // Preserve the ref tags with hashes {type:id:url} => {type:id:url}#type:id
            $value = preg_replace_callback('/(href=|src=)([\'"])(\{(\w+\:\d+\:'.HandleValidator::$handlePattern.')\})(#[^\'"#]+)?\2/', function($matches)
            {
                return $matches[1].$matches[2].$matches[3].(!empty($matches[5]) ? $matches[5] : '').'#'.$matches[4].$matches[2];
            }, $value);

            // Now parse 'em
            $value = craft()->elements->parseRefs($value);
        }

        // Swap any <!--pagebreak-->'s with <hr>'s
        $value = str_replace('<!--pagebreak-->', '<hr class="redactor_pagebreak" style="display:none" unselectable="on" contenteditable="false" />', $value);

        return '<textarea id="'.$id.'" name="'.$this->_field->handle.'" style="display: none">'.htmlentities($value, ENT_NOQUOTES, 'UTF-8').'</textarea>';

    }


    /**
     * Returns the Redactor config JSON used by this field.
     *
     * @return string
     */
    private function _getConfigJson()
    {
        if ($this->_field->getFieldType()->getSettings()->configFile)
        {
            $configPath = craft()->path->getConfigPath().'redactor/'.$this->_field->getFieldType()->getSettings()->configFile;
            $json = IOHelper::getFileContents($configPath);
        }

        if (empty($json))
        {
            $json = '{}';
        }

        return $json;
    }

    /**
     * Includes the input resources.
     *
     * @param string $configJs
     *
     * @return null
     */
    private function _includeFieldResources($configJs)
    {
        craft()->templates->includeCssResource('lib/redactor/redactor.min.css');

        // Gotta use the uncompressed Redactor JS until the compressed one gets our Live Preview menu fix
        craft()->templates->includeJsResource('lib/redactor/redactor.js');
        //craft()->templates->includeJsResource('lib/redactor/redactor'.(craft()->config->get('useCompressedJs') ? '.min' : '').'.js');

        $this->_maybeIncludeRedactorPlugin($configJs, 'fullscreen', false);
        $this->_maybeIncludeRedactorPlugin($configJs, 'source|html', false);
        $this->_maybeIncludeRedactorPlugin($configJs, 'table', false);
        $this->_maybeIncludeRedactorPlugin($configJs, 'video', false);
        $this->_maybeIncludeRedactorPlugin($configJs, 'pagebreak', true);

        craft()->templates->includeTranslations('Insert image', 'Insert URL', 'Choose image', 'Link', 'Link to an entry', 'Insert link', 'Unlink', 'Link to an asset', 'Link to a category');

//        craft()->templates->includeJsResource('js/RichTextInput.js');

        // Check to see if the Redactor has been translated into the current locale
        if (craft()->language != craft()->sourceLanguage)
        {
            // First try to include the actual target locale
            if (!$this->_includeRedactorLangFile(craft()->language))
            {
                // Otherwise try to load the language (without the territory half)
                $languageId = craft()->locale->getLanguageID(craft()->language);

                if (!$this->_includeRedactorLangFile($languageId))
                {
                    // If it's Norwegian Bokmål/Nynorsk, add plain ol' Norwegian as a fallback
                    if ($languageId === 'nb' || $languageId === 'nn')
                    {
                        $this->_includeRedactorLangFile('no');
                    }
                }
            }
        }

        $customTranslations = array(
            'fullscreen' => Craft::t('Fullscreen'),
            'insert-page-break' => Craft::t('Insert Page Break'),
            'table' => Craft::t('Table'),
            'insert-table' => Craft::t('Insert table'),
            'insert-row-above' => Craft::t('Insert row above'),
            'insert-row-below' => Craft::t('Insert row below'),
            'insert-column-left' => Craft::t('Insert column left'),
            'insert-column-right' => Craft::t('Insert column right'),
            'add-head' => Craft::t('Add head'),
            'delete-head' => Craft::t('Delete head'),
            'delete-column' => Craft::t('Delete column'),
            'delete-row' => Craft::t('Delete row'),
            'delete-table' => Craft::t('Delete table'),
            'video' => Craft::t('Video'),
            'video-html-code' => Craft::t('Video Embed Code or Youtube/Vimeo Link'),
        );

        craft()->templates->includeJs(
            '$.extend($.Redactor.opts.langs["'.static::$_redactorLang.'"], ' .
            JsonHelper::encode($customTranslations) .
            ');');
    }

    /**
     * Includes a plugin’s JS file, if it appears to be requested by the config file.
     *
     * @param string $configJs
     * @param string $plugin
     * @param bool $includeCss
     *
     * @return null
     */
    private function _maybeIncludeRedactorPlugin($configJs, $plugin, $includeCss)
    {
        if (preg_match('/([\'"])(?:'.$plugin.')\1/', $configJs))
        {
            if (($pipe = strpos($plugin, '|')) !== false)
            {
                $plugin = substr($plugin, 0, $pipe);
            }

            if ($includeCss)
            {
                craft()->templates->includeCssResource('lib/redactor/plugins/'.$plugin.'.css');
            }

            craft()->templates->includeJsResource('lib/redactor/plugins/'.$plugin.'.js');
        }
    }


    /**
     * Attempts to include a Redactor language file.
     *
     * @param string $lang
     *
     * @return bool
     */
    private function _includeRedactorLangFile($lang)
    {
        $path = 'lib/redactor/lang/'.$lang.'.js';

        if (IOHelper::fileExists(craft()->path->getResourcesPath().$path))
        {
            craft()->templates->includeJsResource($path);
            static::$_redactorLang = $lang;

            return true;
        }
        else
        {
            return false;
        }
    }

}