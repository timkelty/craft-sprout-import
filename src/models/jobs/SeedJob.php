<?php

namespace barrelstrength\sproutimport\models\jobs;

use craft\base\Model;
use sproutimport\enums\ImportType;
use barrelstrength\sproutbase\app\import\base\ElementImporter;

class SeedJob extends Model
{
    /**
     * The Element Type for which to generate Seeds
     *
     * @var string
     */
    public $elementType;

    /**
     * The Seed Type
     *
     * @var Seed $type
     */
    public $seedType;

    /**
     * The number of seeds that will be generated
     *
     * @var int
     */
    public $quantity;

    /**
     * Additional settings the Element Importer will use
     *
     * @see ElementImporter
     *
     * @var string
     */
    public $settings;

    /**
     * @var string
     */
    public $details;

    /**
     * @var \DateTime
     */
    public $dateCreated;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['elementType'], 'required'],
            [['settings'], 'validateSeedSettings']
        ];
    }

    /**
     * Allow Element Importers to validate Seed settings before generating Seeds
     */
    public function validateSeedSettings()
    {
        /**
         * @var $elementImporter ElementImporter
         */
        $elementImporter = new $this->elementType;

        if ($errors = $elementImporter->getSeedSettingsErrors($this->settings)) {
            $this->addError('settings', $errors);
        }
    }
}