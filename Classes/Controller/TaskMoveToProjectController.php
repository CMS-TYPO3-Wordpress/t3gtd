<?php


namespace ThomasWoehlke\T3gtd\Controller;


use ThomasWoehlke\T3gtd\Domain\Model\Project;
use ThomasWoehlke\T3gtd\Domain\Model\Task;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TaskMoveToProjectController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * taskRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\TaskRepository
     * @inject
     */
    protected $taskRepository = null;

    private $extName = 't3gtd';

    /**
     * action moveTaskToProject
     *
     * @param Task $srcTask
     * @param Project|null $targetProject
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function moveTaskToRootProjectAction(
        \ThomasWoehlke\T3gtd\Domain\Model\Task $task
    ){
        $task->setProject(null);
        $projectOrderId = $this->taskRepository->getMaxProjectOrderId(null);
        $task->setOrderIdProject($projectOrderId);
        $this->taskRepository->update($task);
        $arguments = array("project" => null);
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.task.moved2project', $this->extName, null);
        $this->addFlashMessage(
            htmlentities($task->getTitle()), $msg, \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->myRedirect('show', $arguments,"Project");
    }

    /**
     * action moveTaskToProject
     *
     * @param Task $srcTask
     * @param Project|null $targetProject
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function moveTaskToProjectAction(
        \ThomasWoehlke\T3gtd\Domain\Model\Task $task,
        \ThomasWoehlke\T3gtd\Domain\Model\Project $targetProject=null
    ){
        $task->setProject($targetProject);
        $projectOrderId = $this->taskRepository->getMaxProjectOrderId($targetProject);
        $task->setOrderIdProject($projectOrderId);
        $this->taskRepository->update($task);
        $arguments = array("project" => $targetProject);
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.task.moved2project', $this->extName, null);
        $this->addFlashMessage(
            htmlentities($task->getTitle()), $msg, \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->myRedirect('show', $arguments,"Project");
    }

    private function getLanguageId(){

        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        //$logger = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);

        $id = 0;

        $settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($settings);

        if(isset($settings['config.']['sys_language_uid']) &&
            ($settings['config.']['sys_language_uid'] !== null)){
            $id = $settings['config.']['sys_language_uid'];
            //$logger->error($id);
        }
        //$logger->error($id);
        return $id;
    }

    /**
     * @param string $actionName
     * @param array $controllerArguments
     * @param string $controllerName
     */
    private function myRedirect(
        $actionName='inbox',$controllerArguments=array(),$controllerName = 'TaskMove'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }
}