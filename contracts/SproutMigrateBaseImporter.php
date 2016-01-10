<?php
namespace Craft;

/**
 * Class SproutMigrateBaseImporter
 *
 * @package Craft
 */
abstract class SproutMigrateBaseImporter
{
	protected $id;

	public $model;

	protected $valid;

	public function __construct($settings = array())
	{
		if (count($settings))
		{
			$model = $this->getModel();
			$this->model = $model;

			$this->populateModel($model, $settings);
			$this->validate();
		}
	}

	public function getErrors()
	{
		return $this->model->getErrors();
	}

	/**
	 * @param string $pluginHandle
	 */
	final public function setId($plugin, $importer)
	{
		$importerClass = str_replace('Craft\\', '', get_class($this));

		$this->id = $importerClass;
	}

	final public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	abstract public function getModel();

	/**
	 * @return string
	 */
	public function populateModel($model, $settings)
	{
		$model->setAttributes($settings);
		$this->model = $model;
	}

	/**
	 * @return bool
	 */
	public function validate()
	{
		$this->valid = $this->model->validate();
	}

	public function isValid()
	{
		return $this->valid;
	}

	/**
	 * @return string
	 */
	abstract public function save();

	abstract public function deleteById($id);

	//final public function run($model, $settings)
	//{
	//	$this->populateModel($model);
	//	$this->validate($model);
	//	$this->save($model);
	//}

	/**
	 * @return bool
	 */
	public function resolveRelatedSettings()
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public function resolveNestedSettings()
	{
		return true;
	}
}
