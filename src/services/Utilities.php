<?php

namespace barrelstrength\sproutimport\services;

use barrelstrength\sproutimport\SproutImport;
use Craft;
use craft\base\Component;
use craft\helpers\FileHelper;

class Utilities extends Component
{
    public $errors;

    /**
     * Call this method to get singleton
     *
     * @param bool $refresh
     *
     * @return Utilities|null|static
     */
    public static function Instance($refresh = false)
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Utilities();
        }

        return $inst;
    }

    /**
     * Make sure the Sprout Import temp folder is created
     *
     * @return string
     * @throws \yii\base\ErrorException
     */
    public function createTempFolder()
    {
        $folderPath = Craft::$app->getPath()->getTempAssetUploadsPath().'/sproutimport/';

        if (file_exists($folderPath)) {
            FileHelper::clearDirectory($folderPath);
        }

        FileHelper::createDirectory($folderPath);

        return $folderPath;
    }

    /**
     * @param string $key
     * @param mixed  $data
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValueByKey($key, $data, $default = null)
    {
        if (!is_array($data)) {
            SproutImport::error(Craft::t('sprout-import', 'getValueByKey() was passed in a non-array as data.'));

            return $default;
        }

        if (!is_string($key) || empty($key) || !count($data)) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                if (!array_key_exists($innerKey, $data)) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        return array_key_exists($key, $data) ? $data[$key] : $default;
    }
}