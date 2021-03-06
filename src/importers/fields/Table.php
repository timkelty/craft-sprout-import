<?php

namespace barrelstrength\sproutimport\importers\fields;

use barrelstrength\sproutbase\app\import\base\FieldImporter;
use barrelstrength\sproutimport\SproutImport;
use craft\fields\Table as TableField;

class Table extends FieldImporter
{
    /**
     * @return string
     */
    public function getModelName()
    {
        return TableField::class;
    }

    /**
     * @return array|mixed|null
     */
    public function getMockData()
    {
        $settings = $this->model->settings;

        if (!isset($settings['columns'])) {
            return null;
        }

        $columns = $settings['columns'];

        $randomLength = random_int(2, 5);

        $values = [];

        for ($inc = 1; $inc <= $randomLength; $inc++) {
            $values[] = SproutImport::$app->fieldImporter->generateTableColumns($columns);
        }

        return $values;
    }

}
