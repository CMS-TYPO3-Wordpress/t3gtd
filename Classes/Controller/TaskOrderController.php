<?php


namespace ThomasWoehlke\T3gtd\Controller;


use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TaskOrderController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * taskRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\TaskRepository
     * @inject
     */
    protected $taskRepository = null;

    /**
     * userAccountRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $userAccountRepository = null;

    /**
     * projectRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ProjectRepository
     * @inject
     */
    protected $projectRepository = null;

    /**
     * contextService
     *
     * @var \ThomasWoehlke\T3gtd\Service\ContextService
     * @inject
     */
    protected $contextService = null;

    private $extName = 't3gtd';

    /**
     * action moveTaskOrder
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $srcTask
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $targetTask
     * @return void
     */
    public function moveTaskOrderAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $srcTask,
                                        \ThomasWoehlke\T3gtd\Domain\Model\Task $targetTask){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $destinationTaskOrderId = $targetTask->getOrderIdTaskState();
        if($srcTask->getOrderIdTaskState()<$targetTask->getOrderIdTaskState()){
            $tasks = $this->taskRepository->getTasksToReorderByOrderIdTaskState(
                $userObject, $currentContext, $srcTask, $targetTask, $srcTask->getTaskState());
            foreach ($tasks as $task){
                $task->setOrderIdTaskState($task->getOrderIdTaskState()-1);
                $this->taskRepository->update($task);
            }
            $targetTask->setOrderIdTaskState($targetTask->getOrderIdTaskState()-1);
            $this->taskRepository->update($targetTask);
            $srcTask->setOrderIdTaskState($destinationTaskOrderId);
            $this->taskRepository->update($srcTask);
        } else {
            $tasks = $this->taskRepository->getTasksToReorderByOrderIdTaskState(
                $userObject, $currentContext, $targetTask, $srcTask, $srcTask->getTaskState());
            foreach ($tasks as $task){
                $task->setOrderIdTaskState($task->getOrderIdTaskState()+1);
                $this->taskRepository->update($task);
            }
            $srcTask->setOrderIdTaskState($destinationTaskOrderId+1);
            $this->taskRepository->update($srcTask);
        }
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.ordering', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($srcTask->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($srcTask);
    }

    /**
     * action moveTaskOrderInsideProject
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $srcTask
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $targetTask
     * @return void
     */
    public function moveTaskOrderInsideProjectAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $srcTask,
                                                     \ThomasWoehlke\T3gtd\Domain\Model\Task $targetTask){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $project = $srcTask->getProject();
        $destinationProjectOrderId = $targetTask->getOrderIdProject();
        if($srcTask->getOrderIdProject()<$targetTask->getOrderIdProject()){
            $tasks = $this->taskRepository->getTasksToReorderByOrderIdProject(
                $userObject, $currentContext, $srcTask, $targetTask, $project
            );
            foreach ($tasks as $task){
                $task->setOrderIdProject($task->getOrderIdProject()-1);
                $this->taskRepository->update($task);
            }
            $targetTask->setOrderIdProject($targetTask->getOrderIdProject()-1);
            $this->taskRepository->update($targetTask);
            $srcTask->setOrderIdProject($destinationProjectOrderId);
            $this->taskRepository->update($srcTask);
        } else {
            $tasks = $this->taskRepository->getTasksToReorderByOrderIdProject(
                $userObject, $currentContext, $targetTask, $srcTask, $project
            );
            foreach ($tasks as $task){
                $task->setOrderIdProject($task->getOrderIdProject()+1);
                $this->taskRepository->update($task);
            }
            $srcTask->setOrderIdProject($destinationProjectOrderId+1);
            $this->taskRepository->update($srcTask);
        }
        $args = array('project'=>$project);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.ordering', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($srcTask->getTitle()), $msg, FlashMessage::OK);
        $this->myRedirect('show',$args,'Project');
    }

    /**
     * @return string
     */
    private function getLanguage(){

        $settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        return $settings['config.']['language'];
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
    private function myRedirect($actionName='inbox',$controllerArguments=array(),$controllerName = 'Task'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }
}