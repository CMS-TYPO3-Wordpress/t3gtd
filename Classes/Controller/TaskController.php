<?php
namespace ThomasWoehlke\T3gtd\Controller;

/***
 *
 * This file is part of the "Getting Things Done" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2016-2919 Thomas Woehlke <thomas.woehlke@rub.de>, Ruhr Universitaet Bochum
 *
 ***/
use ThomasWoehlke\T3gtd\Domain\Model\Project;
use \ThomasWoehlke\T3gtd\Domain\Model\Task;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TaskController
 *
 * @package ThomasWoehlke\T3gtd\Controller
 */
class TaskController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * action edit
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @ignorevalidation $task
     * @return void
     */
    public function editAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('task', $task);
        $this->getTaskEnergyAndTaskTime();
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    public function initializeEditAction()
    {
        $this->arguments['task']
            ->getPropertyMappingConfiguration()
            ->forProperty('dueDate')
            ->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'Y-m-d');
    }

    /**
     * action update
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function updateAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $persistentTask = $this->taskRepository->findByUid($task->getUid());
        $persistentTask->setTitle($task->getTitle());
        $persistentTask->setText($task->getText());
        $persistentTask->setTaskEnergy($task->getTaskEnergy());
        $persistentTask->setTaskTime($task->getTaskTime());
        $persistentTask->setDueDate($task->getDueDate());
        if($task->getDueDate() != NULL){
            $persistentTask->changeTaskState(Task::$TASK_STATES['scheduled']);
            $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
                $userObject,$currentContext,Task::$TASK_STATES['scheduled']);
            $persistentTask->setOrderIdTaskState($maxTaskStateOrderId);
        } else {
            if($persistentTask->getTaskState() == Task::$TASK_STATES['scheduled']){
                $persistentTask->changeTaskState(Task::$TASK_STATES['inbox']);
            }
            $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
                $userObject,$currentContext,$persistentTask->getTaskState());
            $persistentTask->setOrderIdTaskState($maxTaskStateOrderId);
        }
        if($this->request !== null) {
            if ($this->request->hasArgument('file')) {
                $persistentTask->setFiles(str_replace('uploads/tx_t3gtd/', '',
                    $this->request->getArgument('file')));
            }
        }
        $this->taskRepository->update($persistentTask);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.updated', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($persistentTask);
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

    public function initializeUpdateAction()
    {
        $this->arguments['task']
            ->getPropertyMappingConfiguration()
            ->forProperty('dueDate')
            ->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'Y-m-d');
    }

    /**
     * action transformTaskIntoProject
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function transformTaskIntoProjectAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        $parentProject = $task->getProject();
        $newProject = new Project();
        $newProject->setContext($task->getContext());
        $newProject->setUserAccount($task->getUserAccount());
        $newProject->setParent($parentProject);
        $newProject->setName($task->getTitle());
        $newProject->setDescription($task->getText());
        if($parentProject != null){
            $parentProject->addChild($newProject);
            $this->projectRepository->update($parentProject);
        }
        $this->projectRepository->add($newProject);
        $this->taskRepository->remove($task);
        $args = array("project" => $parentProject);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.task2project', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($newProject->getName()), $msg, FlashMessage::OK);
        $this->myRedirect('show',$args,"Project");
    }

    /**
     * action completeTask
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function completeTaskAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        $task->changeTaskState(Task::$TASK_STATES['completed']);
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['completed']);
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.completed', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action undoneTask
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function undoneTaskAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        $task->setToLastTaskState();
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,$task->getTaskState());
        $task->setOrderIdTaskState($maxTaskStateOrderId);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.notcompleted', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()), $msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action setFocus
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function setFocusAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        $task->setFocus(true);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.focus', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()),$msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    /**
     * action unsetFocus
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $task
     * @return void
     */
    public function unsetFocusAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $task)
    {
        $task->setFocus(false);
        $this->taskRepository->update($task);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.notfocus', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($task->getTitle()),$msg, FlashMessage::OK);
        $this->getRedirectFromTask($task);
    }

    private function getTaskEnergyAndTaskTime(){
        $taskEnergy = array();
        $taskTime = array();
        switch ($this->getLanguage()) {
            case 'de':
                $taskEnergy = array(
                    0 => 'nichts',
                    1 => 'niedrig',
                    2 => 'mittel',
                    3 => 'hoch'
                );
                $taskTime = array(
                    0 => 'nichts',
                    1 => '5 min',
                    2 => '10 min',
                    3 => '15 min',
                    4 => '30 min',
                    5 => '45 min',
                    6 => '1 Stunde',
                    7 => '2 Stunden',
                    8 => '3 Stunden',
                    9 => '4 Stunden',
                    10 => '6 Stunden',
                    11 => '8 Stunden',
                    12 => 'mehr'
                );
                break;
            case 'en':
            default:
                $taskEnergy = array(
                    0 => 'none',
                    1 => 'low',
                    2 => 'mid',
                    3 => 'high'
                );
                $taskTime = array(
                    0 => 'none',
                    1 => '5 min',
                    2 => '10 min',
                    3 => '15 min',
                    4 => '30 min',
                    5 => '45 min',
                    6 => '1 hour',
                    7 => '2 hours',
                    8 => '3 hours',
                    9 => '4 hours',
                    10 => '6 hours',
                    11 => '8 hours',
                    12 => 'more'
                );
                break;
        }
        $this->view->assign('taskEnergy',$taskEnergy);
        $this->view->assign('taskTime',$taskTime);
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
        $ctx = $this->contextService->getCurrentContext();
        $this->getTaskEnergyAndTaskTime();
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action create
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Task $newTask
     * @return void
     */
    public function createAction(\ThomasWoehlke\T3gtd\Domain\Model\Task $newTask)
    {
        /** @var $userObject \TYPO3\CMS\Extbase\Domain\Model\FrontendUser */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $newTask->setContext($currentContext);
        $newTask->setUserAccount($userObject);
        $newTask->setTaskState(Task::$TASK_STATES['inbox']);
        $projectOrderId = $this->taskRepository->getMaxProjectOrderId(null);
        $newTask->setOrderIdProject($projectOrderId);
        $maxTaskStateOrderId = $this->taskRepository->getMaxTaskStateOrderId(
            $userObject,$currentContext,Task::$TASK_STATES['inbox']);
        $newTask->setOrderIdTaskState($maxTaskStateOrderId);
        if($this->request !== null) {
            if ($this->request->hasArgument('file')) {
                $newTask->setFiles(str_replace('uploads/tx_t3gtd/', '',
                    $this->request->getArgument('file')));
            }
        }
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.task.new', $this->extName, null);
        $this->addFlashMessage(htmlspecialchars($newTask->getTitle()),$msg, FlashMessage::OK);
        if($newTask->getDueDate() != NULL){
            $newTask->setTaskState(Task::$TASK_STATES['scheduled']);
            $this->taskRepository->add($newTask);
            $this->myRedirect('scheduled', array(), "TaskState");
        } else {
            $newTask->setTaskState(Task::$TASK_STATES['inbox']);
            $this->taskRepository->add($newTask);
            $this->myRedirect('inbox', array(), "TaskState");
        }
    }

    public function initializeCreateAction()
    {
        $this->arguments['newTask']
            ->getPropertyMappingConfiguration()
            ->forProperty('dueDate')
            ->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'Y-m-d');
    }

    /**
     * action uploadFiles
     *
     * @return void
     */
    public function uploadFilesAction(){
        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $logger = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        $logger->debug($_FILES['upl']);
        $allowed = array(
            'png', 'jpg', 'gif','zip','doc', 'xls', 'csv', 'docx', 'xlsx', 'psd', 'rar', 'indd', 'ind', 'pdf'
        );
        if(isset($_FILES['upl']) && $_FILES['upl']['error'] == UPLOAD_ERR_OK){
            $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($extension), $allowed)){
                echo '{"status":"error"}';
                $msg = "File Type not allowed";
                $this->addFlashMessage($msg, '', FlashMessage::ERROR);
                exit;
            }
            $filePath = PATH_site . 'uploads/tx_t3gtd/';
            if(!file_exists($filePath)){
                GeneralUtility::mkdir($filePath);
            }
            $originalName = $_FILES['upl']['name'];
            $targetName = $this->getGoodFilemane($originalName);
            if(file_exists(($filePath . $_FILES['upl']['name']))){
                $timestamp = time();
                if(GeneralUtility::upload_copy_move($_FILES['upl']['tmp_name'], $filePath.$timestamp.'_'.$targetName)){
                    echo 'uploads/tx_t3gtd/'.$timestamp.'_'.$targetName;
                    $logger->debug('uploads/tx_t3gtd/'.$timestamp.'_'.$_FILES['upl']['name']);
                    exit;
                }
            } else {
                if(GeneralUtility::upload_copy_move($_FILES['upl']['tmp_name'], $filePath.$targetName)){
                    echo 'uploads/tx_t3gtd/'.$targetName;
                    $logger->debug('uploads/tx_t3gtd/'.$_FILES['upl']['name']);
                    exit;
                }
            }
        } else {
            if(isset($_FILES['upl'])){
                $msg = 'Failed Upload: '.$_FILES['upl']['name'].' ';
                switch ($_FILES['upl']['error']){
                    case UPLOAD_ERR_INI_SIZE:
                        $msg .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $msg .= 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified'
                        .' in the HTML form.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $msg .= 'The uploaded file was only partially uploaded.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $msg .= 'No file was uploaded.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $msg .= 'Missing a temporary folder.';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $msg .= 'Failed to write file to disk.';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $msg .= 'A PHP extension stopped the file upload.'
                        .'PHP does not provide a way to ascertain which extension caused the file upload to stop;'
                        .'examining the list of loaded extensions with phpinfo() may help.';
                        break;
                    default:
                        $msg .= 'Errorcode: '.$_FILES['upl']['error'];
                        break;
                }
                $logger->error($msg);
                $this->addFlashMessage($msg, '', FlashMessage::ERROR);
            } else {
                $logger->error('NOT isset($_FILES[\'upl\'])');
            }
            exit;
        }
    }

    /**
     * @param string $oldFilename
     * @return string
     */
    private function getGoodFilemane($oldFilename){
        $oldFilename = str_replace(' ','_',$oldFilename);
        return $oldFilename;
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
