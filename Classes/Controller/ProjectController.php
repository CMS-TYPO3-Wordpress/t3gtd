<?php
namespace ThomasWoehlke\T3gtd\Controller;

use \ThomasWoehlke\T3gtd\Domain\Model\Project;
use \ThomasWoehlke\T3gtd\Domain\Model\Task;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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

/**
 * ProjectController
 */
class ProjectController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * projectRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ProjectRepository
     * @inject
     */
    protected $projectRepository = null;

    /**
     * projectService
     *
     * @var \ThomasWoehlke\T3gtd\Service\ProjectService
     * @inject
     */
    protected $projectService = null;

    /**
     * taskRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\TaskRepository
     * @inject
     */
    protected $taskRepository = null;

    /**
     * contextService
     *
     * @var \ThomasWoehlke\T3gtd\Service\ContextService
     * @inject
     */
    protected $contextService = null;

    /**
     * userAccountRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $userAccountRepository = null;

    private $extName = 't3gtd';

    /**
     * action show
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $project
     * @return void
     */
    public function showAction(\ThomasWoehlke\T3gtd\Domain\Model\Project $project=null)
    {
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('project', $project);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $tasks = null;
        $deleteable = false;
        if($project == null){
            $tasks = $this->taskRepository->findByRootProjectAndContext($ctx);
        } else {
            $tasks = $this->taskRepository->findByProject($project);
            if($tasks->count() == 0 && $project->getChildren()->count() == 0){
                $deleteable = true;
            }
        }
        $this->view->assign('tasks', $tasks);
        $this->view->assign('deleteable',$deleteable);
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action edit
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $project
     * @ignorevalidation $project
     * @return void
     */
    public function editAction(\ThomasWoehlke\T3gtd\Domain\Model\Project $project)
    {
        $this->view->assign('project', $project);
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action update
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $project
     * @return void
     */
    public function updateAction(\ThomasWoehlke\T3gtd\Domain\Model\Project $project)
    {
        $this->projectRepository->update($project);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.project.updated', $this->extName, null);
        $this->addFlashMessage($msg, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $args = array('project'=>$project);
        $this->myRedirect('show',$args);
    }

    /**
     * action delete
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $project
     * @return void
     */
    public function deleteAction(\ThomasWoehlke\T3gtd\Domain\Model\Project $project)
    {
        $parentProject = $project->getParent();
        $deleteable = false;
        $tasks = $this->taskRepository->findByProject($project);
        if($tasks->count() == 0 && $project->getChildren()->count() == 0){
            $deleteable = true;
        }
        if($deleteable) {
            $msg = LocalizationUtility::translate('tx_t3gtd_flash.project.deleted', $this->extName, null);
            $this->addFlashMessage(
                htmlspecialchars($project->getName()), $msg,
                \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING);
            $this->projectRepository->remove($project);
        }
        $args = array('project'=>$parentProject);
        $this->myRedirect('show',$args);
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $projects = $this->projectRepository->findAll();
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('projects', $projects);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action new
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $parentProject
     * @return void
     */
    public function newAction(\ThomasWoehlke\T3gtd\Domain\Model\Project $parentProject=null)
    {
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('parentProject', $parentProject);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action create
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $newProject
     * @return void
     */
    public function createAction(\ThomasWoehlke\T3gtd\Domain\Model\Project $newProject,
                                 \ThomasWoehlke\T3gtd\Domain\Model\Project $parentProject=null)
    {
        $context = $this->contextService->getCurrentContext();
        $newProject->setParent($parentProject);
        $newProject->setContext($context);
        $this->projectRepository->add($newProject);
        if($parentProject != null){
            $parentProject->addChild($newProject);
            $this->projectRepository->update($parentProject);
        }
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.project.created', $this->extName, null);
        $this->addFlashMessage(
            htmlspecialchars($newProject->getName()), $msg, \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $args = array('project'=>$parentProject);
        $this->myRedirect('show',$args);
    }

    /**
     * @return string
     */
    protected function getLanguage(){
        $settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        return $settings['config.']['language'];
    }

    protected function getLanguageId(){
        $id = 0;
        $settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        if(isset($settings['config.']['sys_language_uid']) &&
            ($settings['config.']['sys_language_uid'] !== null)){
            $id = $settings['config.']['sys_language_uid'];
        }
        return $id;
    }

    /**
     * @param string $actionName
     * @param array $controllerArguments
     * @param string $controllerName
     * @throws UnsupportedRequestTypeException If the request is not a web request
     * @throws StopActionException
     */
    protected function myRedirect(
        $actionName='inbox',$controllerArguments=array(),$controllerName = 'Context'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }

}
