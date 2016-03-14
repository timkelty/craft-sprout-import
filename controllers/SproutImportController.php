<?php
namespace Craft;

class SproutImportController extends BaseController
{
	public function actionImportElements()
	{
		$this->requirePostRequest();

		$tasks = array();
		$files = UploadedFile::getInstancesByName('files');

		foreach ($files as $file)
		{
			if (!$file->getHasError() && $file->getType() == 'application/json'
			|| $file->getType() == 'application/octet-stream')
			{
				$path = craft()->path->getStoragePath() . $file->getName();

				if (move_uploaded_file($file->getTempName(), $path))
				{
					$tasks[] = $path;
				}
			}
		}

		$seed = craft()->request->getPost('seed');

		try
		{
			sproutImport()->createImportTasks($tasks, $seed);

			craft()->userSession->setNotice(Craft::t('Files queued for import. Total: {tasks}', array(
				'tasks' => count($tasks)
			)));
		}
		catch(\Exception $e)
		{
			craft()->userSession->setError($e->getMessage());
		}

		$this->redirectToPostedUrl();
	}

	public function actionEnqueueTasksByPost()
	{
		$elements = craft()->request->getPost('elements');

		sproutImport()->setEnqueueTasksByPost($elements);
		craft()->end();
	}

	public function actionGenerateData()
	{
		$elementSelect = array();

		$elementSelect['Entry']    = 'Entries';
		$elementSelect['Category'] = 'Categories';
		$elementSelect['Tag']      = 'Tags';

		$this->renderTemplate('sproutimport/generatedata', array(
			'elements' => $elementSelect
		));
	}

	public function actionGenerateElements()
	{
		$this->requirePostRequest();

		$elementType = craft()->request->getRequiredPost('elementType');

		craft()->userSession->setNotice(Craft::t('Elements generated.' . $elementType));

		$this->redirectToPostedUrl();
	}
}
