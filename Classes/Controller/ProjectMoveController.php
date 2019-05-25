<?php


namespace ThomasWoehlke\T3gtd\Controller;


use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ProjectMoveController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * action moveProjectToProject
     *
     * @return void
     */
    public function moveProjectToRootProjectAction(
        \ThomasWoehlke\T3gtd\Domain\Model\Project $srcProject)
    {
        $context = $this->contextService->getCurrentContext();
        $this->projectService->moveProjectToRootProject($srcProject,$context);
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.project.moved',
            $this->extName,
            null
        );
        $this->addFlashMessage(
            htmlspecialchars($srcProject->getName()),
            $msg,
            \TYPO3\CMS\Core\Messaging\FlashMessage::OK
        );
        $arguments = array("project" => $srcProject);
        $this->myRedirect('show', $arguments,"Project");
    }

    /**
     * action moveProjectToProject
     *
     * @return void
     */
    public function moveProjectToProjectAction(
        \ThomasWoehlke\T3gtd\Domain\Model\Project $srcProject,
        \ThomasWoehlke\T3gtd\Domain\Model\Project $targetProject)
    {
        $context = $this->contextService->getCurrentContext();
        $this->projectService->moveProjectToProject($srcProject,$targetProject,$context);
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.project.moved',
            $this->extName,
            null
        );
        $this->addFlashMessage(
            htmlspecialchars($srcProject->getName()),
            $msg,
            \TYPO3\CMS\Core\Messaging\FlashMessage::OK
        );
        $arguments = array("project" => $srcProject);
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
    private function myRedirect($actionName='show', $controllerArguments=array(), $controllerName = 'ProjectMove'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }
}