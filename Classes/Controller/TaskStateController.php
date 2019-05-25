<?php


namespace ThomasWoehlke\T3gtd\Controller;


use ThomasWoehlke\T3gtd\Domain\Model\Task;

class TaskStateController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * action inbox
     *
     * @return void
     */
    public function inboxAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['inbox']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action today
     *
     * @return void
     */
    public function todayAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $this->updateTodayAndScheduledTaskStates();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['today']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action next
     *
     * @return void
     */
    public function nextAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['next']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action waiting
     *
     * @return void
     */
    public function waitingAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['waiting']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action scheduled
     *
     * @return void
     */
    public function scheduledAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $this->updateTodayAndScheduledTaskStates();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['scheduled']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action someday
     *
     * @return void
     */
    public function somedayAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['someday']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action completed
     *
     * @return void
     */
    public function completedAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['completed']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action trash
     *
     * @return void
     */
    public function trashAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndTaskState(
            $userObject,$currentContext,Task::$TASK_STATES['trash']);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action focus
     *
     * @return void
     */
    public function focusAction()
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $tasks = $this->taskRepository->findByUserAccountAndHasFocus($userObject,$currentContext);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($currentContext));
        $this->view->assign('langKey',$this->getLanguageId());
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

    private function updateTodayAndScheduledTaskStates(){
        $tasks = $this->taskRepository->getScheduledTasksOfCurrentDay();
        foreach ($tasks as $task){
            $userAccount = $task->getUserAccount();
            $context = $task->getContext();
            $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
                $userAccount,$context,Task::$TASK_STATES['today']
            );
            $task->changeTaskState(Task::$TASK_STATES['today']);
            $task->setOrderIdTaskState($maxTaskStateOrderId);
            $this->taskRepository->update($task);
        }
    }

}