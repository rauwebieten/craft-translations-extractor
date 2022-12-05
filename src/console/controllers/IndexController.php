<?php

namespace rauwebieten\translationsextractor\console\controllers;

use rauwebieten\translationsextractor\TranslationsExtractor;
use yii\base\Exception;
use yii\console\Controller;
use yii\console\ExitCode;

class IndexController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionIndex(): int
    {
        TranslationsExtractor::getInstance()->extractor->extract();
        return ExitCode::OK;
    }
}
