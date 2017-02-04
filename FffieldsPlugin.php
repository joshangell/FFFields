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
class FffieldsPlugin extends BasePlugin
{
    // Public Methods
    // =========================================================================

    /**
     * The plugin name.
     *
     * @return string
     */
    public function getName()
    {
        return 'FFFields';
    }

    /**
     * The plugin description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return 'Craft CMS fields for the front-end.';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Josh Angell';
    }

    /**
     * Developer URL.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://angell.io';
    }

    /**
     * @return string
     */
    public function getPluginUrl()
    {
        return 'https://angell.io/plugins/fffields';
    }

    /**
     * Documentation URL.
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://angell.io/plugins/fffields/docs';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://angell.io/plugins/fffields/releases.json';
    }

    /**
     * Version.

     * @return string
     */
    public function getVersion()
    {
        return '0.0.1';
    }

    /**
     * Schema version.
     *
     * @return string|null
     */
    public function getSchemaVersion()
    {
        return '0.0.1';
    }

}
