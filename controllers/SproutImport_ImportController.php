<?php
namespace Craft;

class SproutImport_ImportController extends BaseController
{
	/**
	 * Import Content and Settings via JSON schema using the proper Craft Import Format
	 *
	 * @throws HttpException
	 */
	public function actionRunImport()
	{
		$this->requirePostRequest();

		$tasks = array();
		$files = UploadedFile::getInstancesByName('files');

		$folderPath = sproutImport()->createTempFolder();

		$count = 0;

		foreach ($files as $file)
		{
			if (!$file->getHasError() && $file->getType() == 'application/json'
				|| $file->getType() == 'application/octet-stream'
			)
			{
				$path = $folderPath . $file->getName();

				if (move_uploaded_file($file->getTempName(), $path))
				{
					$content = file_get_contents($path);

					$tasks[$count]['path']    = $path;
					$tasks[$count]['content'] = $content;
				}
			}

			$count++;
		}

		$pastedJson = craft()->request->getPost('pastedJson');

		if (!empty($pastedJson) && json_decode($pastedJson, true) == true && !json_last_error())
		{
			$tasks[$count]['path']    = 'pastedJson';
			$tasks[$count]['content'] = $pastedJson;
		}
		elseif (!empty($pastedJson))
		{
			$errorMsg = Craft::t('Unable to parse pasted JSON.');

			craft()->userSession->setError($errorMsg);

			$this->redirectToPostedUrl();
		}

		$seed = craft()->request->getPost('seed');

		try
		{
			sproutImport()->tasks->createImportTasks($tasks, $seed);

			craft()->userSession->setNotice(Craft::t('Files queued for import. Total: {tasks}', array(
				'tasks' => count($tasks)
			)));
		}
		catch (\Exception $e)
		{
			craft()->userSession->setError($e->getMessage());
		}

		$this->redirectToPostedUrl();
	}

	/**
	 * Import element content from a post request
	 *
	 * @todo - make this import method behave just like the standard actionRunImport method
	 */
	public function actionRunImportFromPost()
	{
		$elements = craft()->request->getPost('elements');

		sproutImport()->tasks->createImportTasksFromPost($elements);

		craft()->end();
	}

	/**
	 * @deprecated since 0.5.0. To be removed in 1.0.
	 */
	public function actionEnqueueTasksByPost()
	{
		craft()->deprecator->log('SproutImport_ImportController::actionEnqueueTasksByPost()', 'SproutImport_ImportController::actionEnqueueTasksByPost() has been deprecated. Use SproutImport_ImportController::actionRunImportFromPost() instead.');

		$this->actionRunImportFromPost();
	}
}
