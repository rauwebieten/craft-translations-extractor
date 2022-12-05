<?php
/**
 * Translations Extractor plugin for Craft CMS 3.x
 *
 * Translations Extractor
 *
 * @link      https://github.com/rauwebieten
 * @copyright Copyright (c) 2022 rauwebieten
 */

namespace rauwebieten\translationsextractor;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use rauwebieten\translationsextractor\services\Extractor as ExtractorService;

/**
 * Class TranslationsExtractor
 *
 * @author    rauwebieten
 * @package   TranslationsExtractor
 * @since     1.0.0
 *
 * @property  ExtractorService $extractor
 */
class TranslationsExtractor extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var TranslationsExtractor
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'rauwebieten\translationsextractor\console\controllers';
        }

        Craft::info(
            Craft::t(
                'translations-extractor',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
}
