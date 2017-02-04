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
     * Renders the fields for a given field layout.
     *
     * @param FieldLayoutModel $fieldLayout
     *
     * @return mixed
     */
    public function renderFromLayout(FieldLayoutModel $fieldLayout)
    {

        if (!$fieldLayout) {
            return false;
        }

        return 'HI!';

    }

}