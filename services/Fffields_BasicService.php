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
class Fffields_BasicService extends BaseApplicationComponent
{

    // Public Methods
    // =========================================================================

    /**
     * Returns the required config for a <plain-text/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getPlainTextConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        $config = [
            'id'            => $id,
            'name'          => $name,
            'type'          => 'text',
            'value'         => $params['value'],
            'maxlength'     => $settings->maxLength,
            'showCharsLeft' => $settings->maxLength ? true : false,
            'placeholder'   => Craft::t($settings->placeholder),
            'rows'          => $settings->initialRows,
            'multiline'     => $settings->multiline
        ];

        return $config;
    }

    /**
     * Returns the required config for a <number/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getNumberConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        $value = $params['value'];

        if ($params['element']->getHasFreshContent() && ($value < $settings->min || $value > $settings->max)) {
            $value = $settings->min;
        }

        $value = craft()->numberFormatter->formatDecimal($value, false);

        $config = [
            'id'    => $id,
            'name'  => $name,
            'type'  => 'number',
            'value' => $value,
        ];

        if ($settings->min || $settings->min === '0') {
            $config['min'] = $settings->min;
        }

        if ($settings->max || $settings->max === '0') {
            $config['max'] = $settings->max;
        }

        if ($settings->decimals) {
            $config['step'] = '0.' . str_repeat ('0', $settings->decimals - 1) . '1';
        }

        return $config;
    }

    /**
     * Returns the required config for a <lightswitch/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getLightswitchConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();
        $value = $params['value'];

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        if ($params['element']->getHasFreshContent()) {
            $value = $settings->default;
        }

        $config = [
            'id'    => $id,
            'name'  => $name,
            'value' => (bool) $value,
        ];

        return $config;
    }

    /**
     * Returns required config for a <date/> custom tag.
     *
     * @param array $params
     *
     * @return array
     */
    public function getDateConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        $value = null;
        if ($params['value']) {
            $value = [
                'date' => $settings->showDate ? $params['value']->localeDate() : null,
                'time' => $settings->showTime ? $params['value']->localeTime() : null,
            ];
        }

        $currentDateTime = DateTimeHelper::currentUTCDateTime();

        $config = [
            'id'              => $id,
            'name'            => $name,
            'value'           => $value,
            'localeDate'      => $currentDateTime->localeDate(),
            'localeTime'      => $currentDateTime->localeTime(),
            'placeholder'     => ($settings->showDate && $settings->showTime ? Craft::t('Date/Time') : ($settings->showTime ? Craft::t('Time') : Craft::t('Date'))),
            'showDate'        => $settings->showDate,
            'showTime'        => $settings->showTime,
            'minuteIncrement' => $settings->minuteIncrement
        ];

        return $config;
    }

    /**
     * Returns the required config for the following custom tags:
     *
     * <dropdown/>
     * <checkboxes/>
     *
     * @param array $params
     *
     * @return array
     */
    public function getOptionsConfig(array $params)
    {
        $field = $params['fieldLayoutField']->getField();
        $value = $params['value'];

        $id = craft()->templates->namespaceInputId($field->handle, $params['namespace']);
        $name = craft()->templates->namespaceInputName($field->handle, $params['namespace']);
        $settings = $field->getFieldType()->getSettings();

        // NOTE: Bit of a crap check but it’ll do for now
        $multi = false;
        if (in_array($field->type , ['Checkboxes', 'MultiSelect'])) {
            $multi = true;
        }

        // Sort out the value
        if ($params['element']->getHasFreshContent()) {

            if ($multi)
            {
                $defaultValues = [];
            }

            foreach ($settings->options as $option)
            {
                if (!empty($option['default']))
                {
                    if ($multi)
                    {
                        $defaultValues[] = $option['value'];
                    }
                    else
                    {
                        $value = $option['value'];
                    }
                }
            }

            if ($multi)
            {
                $value = $defaultValues;
            }

        } else {

            if ($multi) {
                $values = [];

                foreach ($value as $v) {
                    $values[] = $v->value;
                }

                $value = $values;
            } else {
                $value = $value->value;
            }

        }

        $config = [
            'id' => $id,
            'name' => $name,
            'value' => $value,
            'fluid' => false,
            'options' => $settings->options
        ];

        return $config;
    }

}