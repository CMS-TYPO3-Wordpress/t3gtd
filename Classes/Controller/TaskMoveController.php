<?php


namespace ThomasWoehlke\T3gtd\Controller;


use ThomasWoehlke\T3gtd\Domain\Model\Project;
use ThomasWoehlke\T3gtd\Domain\Model\Task;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TaskMoveController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * contextService
     *
     * @var \ThomasWoehlke\T3gtd\Service\ContextService
     * @inject
     */
    protected $contextService = null;

    private $extName = 't3gtd';

    /**
     * action moveToInbox
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function moveToInboxAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['inbox']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['inbox']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_inbox', $this->extName, null);
        $this->addFlashMessage(
            htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveToToday
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function moveToTodayAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['today']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['today']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_today', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveToNext
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function moveToNextAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext, Task::$TASK_STATES['next']
        );
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['next']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_next', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()),$msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveToWaiting
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     * @throws Exception\UnknownObjectException
     * @throws Exception\IllegalObjectTypeException
     */
    public function moveToWaitingAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['waiting']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['waiting']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_waiting', $this->extName, null);
        $this->addFlashMessage(
            htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveToSomeday
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @throws Exception\UnknownObjectException
     * @throws Exception\IllegalObjectTypeException
     * @return void
     */
    public function moveToSomedayAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['someday']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['someday']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_someday', $this->extName, null);
        $this->addFlashMessage(
            htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveToCompleted
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @throws Exception\UnknownObjectException
     * @throws Exception\IllegalObjectTypeException
     * @return void
     */
    public function moveToCompletedAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['completed']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['completed']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_completed', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveToTrash
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     * @throws Exception\UnknownObjectException
     * @throws Exception\IllegalObjectTypeException
     */
    public function moveToTrashAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['trash']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $task->changeTaskState(Task::$TASK_STATES['trash']);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_trash', $this->extName, null);
        $this->addFlashMessage(
            htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action moveAllCompletedToTrash
     *
     * @throws Exception\UnknownObjectException
     * @throws Exception\IllegalObjectTypeException
     * @return void
     */
    public function moveAllCompletedToTrashAction(){
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext, Task::$TASK_STATES['completed']);
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['trash']);
        $title = "";
        foreach($tasks as $task){
            $title = $task->getTitle();
            $task->changeTaskState(Task::$TASK_STATES['trash']);
            $task->setOrderIdTaskState($maxTaskStateOrderId);
            $this->taskRepository->update($task);
            $maxTaskStateOrderId++;
        }
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.moved_completed2trash', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($title), $msg, FlashMessage::OK);
        $this->myRedirect('trash',array(), 'TaskState');
    }

    /**
     * action emptyTrash
     *
     * @return void
     */
    public function emptyTrashAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['trash']);
        foreach($tasks as $task){
            $this->taskRepository->remove($task);
        }
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.trash_emptied', $this->extName, null);
        $this->addFlashMessage($msg, '', FlashMessage::OK);
        $this->myRedirect('trash', array(),"TaskState");
    }

    /**
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     */
    private function getRedirectFromTask(\ThomasWoehlke\T3gtd\Domain\Model\Task $task){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor('inbox', array(), 'TaskState');
        switch($task->getTaskState()){
            case Task::$TASK_STATES['inbox']:
                break;
            case Task::$TASK_STATES['today']:
                $uri = $this->uriBuilder->uriFor('today', array(), 'TaskState');
                break;
            case Task::$TASK_STATES['next']:
                $uri = $this->uriBuilder->uriFor('next', array(), 'TaskState');
                break;
            case Task::$TASK_STATES['waiting']:
                $uri = $this->uriBuilder->uriFor('waiting', array(), 'TaskState');
                break;
            case Task::$TASK_STATES['scheduled']:
                $uri = $this->uriBuilder->uriFor('scheduled', array(), 'TaskState');
                break;
            case Task::$TASK_STATES['someday']:
                $uri = $this->uriBuilder->uriFor('someday', array(), 'TaskState');
                break;
            case Task::$TASK_STATES['completed']:
                $uri = $this->uriBuilder->uriFor('completed', array(), 'TaskState');
                break;
            case Task::$TASK_STATES['trash']:
                $uri = $this->uriBuilder->uriFor('trash', array(), 'TaskState');
                break;
            default:
                break;
        }
        $this->redirectToUri($uri);
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