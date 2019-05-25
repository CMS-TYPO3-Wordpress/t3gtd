<?php


namespace ThomasWoehlke\T3gtd\Controller;


use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TestController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * action createTestData
     * @return void
     */
    public function createTestDataAction()
    {
        /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $currentContext = $this->contextService->getCurrentContext();
        $this->projectService->createTestData($userObject,$currentContext);
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.testdata.created',
            $this->extName,
            null
        );
        $this->addFlashMessage($msg, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->myRedirect('inbox',array(),"Task");
    }

    /**
     * @param string $actionName
     * @param array $controllerArguments
     * @param string $controllerName
     */
    private function myRedirect(
        $actionName='inbox',$controllerArguments=array(),$controllerName = 'Test'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }
}