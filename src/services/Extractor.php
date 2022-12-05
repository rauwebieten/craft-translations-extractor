<?php
/**
 * Translations Extractor plugin for Craft CMS 3.x
 *
 * Translations Extractor
 *
 * @link      https://github.com/rauwebieten
 * @copyright Copyright (c) 2022 rauwebieten
 */

namespace rauwebieten\translationsextractor\services;

use Craft;
use craft\base\Component;
use craft\helpers\FileHelper;
use LucLeroy\Regex\Regex;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;
use yii\base\Exception;

/**
 * @author    rauwebieten
 * @package   TranslationsExtractor
 * @since     1.0.0
 */
class Extractor extends Component
{
    /** @var string */
    private $twigRegex;

    public function init()
    {
        parent::init();

        $this->twigRegex = Regex::create()
            ->lazy()
            //->literal(' ')->before()
            ->chars('\'"')->capture('quote')
            ->start()
            ->anyChar()->anyTimes()
            ->group()->capture('message')
            ->ref('quote')
            ->alt([
                Regex::create()->literal('|t'),
                Regex::create()->literal('|translate')
            ])
            ->notChars('a..z')->after()
            //->literal(' ')->after()
            ->getRegex();
    }

    /**
     * @throws Exception
     */
    public function extract()
    {
        $namespace = 'site';
        $extractedMessages = $this->extractMessageFromTwigTemplates();

        $locales = $this->getAllLocales();
        foreach ($locales as $locale) {
            $filePath = Craft::getAlias("@translations/$locale/$namespace-extracted.php");
            if (file_exists($filePath)) {
                // load existing messages from translation file
                $messages = include($filePath);

                // remove messages not in use anymore: messages not found in extractedMessages
                $messages = array_intersect_key($messages, array_flip(array_keys($extractedMessages)));
            } else {
                $messages = [];
            }

            // merge messages and extractedMessages
            $messages = array_merge($extractedMessages, $messages);

            // sort by key
            ksort($messages);

            FileHelper::createDirectory(Craft::getAlias("@translations/$locale"), 0777);
            $fileContent = var_export($messages, 1);
            file_put_contents($filePath, "<?php return $fileContent;");
        }
    }

    private function extractMessageFromTwigTemplates(): array
    {
        $templatePath = Craft::getAlias('@templates');
        $messages = [];

        // create iterator to find all template files
        $iterator = new RecursiveDirectoryIterator($templatePath);
        $iterator = new RecursiveIteratorIterator($iterator);
        $iterator = new RegexIterator($iterator, '/^.+\.twig/i');

        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            // get template content, and extract translations strings
            $content = file_get_contents($fileInfo->getPathname());
            preg_match_all($this->twigRegex, $content, $matches, PREG_SET_ORDER);

            // replace escaped quote characters
            $strings = array_map(function($match) {
                $message = $match['message'];
                $quote = $match['quote'];
                return preg_replace("/".preg_quote("\\". $quote)."/", $quote, $message);
            }, $matches);

            // merge info the message array
            $messages = array_merge($messages, $strings);
        }

        // remove duplicated
        $messages = array_values(array_unique($messages));

        // make assoc with empty values, and return
        return array_fill_keys($messages, '');
    }

    private function getAllLocales(): array
    {
        $sites = Craft::$app->sites->getAllSites();
        $locales = [];
        foreach ($sites as $site) {
            $locales[] = $site->getLocale()->getLanguageID();
        }

        return array_values(array_unique($locales));
    }
}
