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

use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
/**
 * Class ContextController
 *
 * @package ThomasWoehlke\T3gtd\Controller
 */
class ContextController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * contextRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ContextRepository
     * @inject
     */
    protected $contextRepository = null;

    /**
     * contextService
     *
     * @var \ThomasWoehlke\T3gtd\Service\ContextService
     * @inject
     */
    protected $contextService = null;

    /**
     * projectRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ProjectRepository
     * @inject
     */
    protected $projectRepository = null;

    /**
     * userAccountRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $userAccountRepository = null;

    /**
     * taskRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\TaskRepository
     * @inject
     */
    protected $taskRepository = null;

    private $extName = 't3gtd';

    /**
     * action switchContext
     *
     * @return void
     */
    public function switchContextAction(\ThomasWoehlke\T3gtd\Domain\Model\Context $context)
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'tx_t3gtd_fesessiondata');
        $sessionData['contextUid'] = $context->getUid();
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'tx_t3gtd_fesessiondata', $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        $this->myRedirect('inbox',array(),"TaskState");
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action create
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $newContext
     * @return void
     */
    public function createAction(\ThomasWoehlke\T3gtd\Domain\Model\Context $newContext)
    {
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $newContext->setUserAccount($userObject);
        $this->contextRepository->add($newContext);
        $msg = LocalizationUtility::translate(
            'tx_t3gtd_flash.context.created', $this->extName, null);
        $this->addFlashMessage(
            $msg, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->myRedirect('show',array(),'UserConfig');
    }

    /**
     * action edit
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @ignorevalidation $context
     * @return void
     */
    public function editAction(\ThomasWoehlke\T3gtd\Domain\Model\Context $context)
    {
        $ctx = $this->contextService->getCurrentContext();
        $this->view->assign('mycontext', $context);
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$ctx);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($ctx));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action update
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @return void
     */
    public function updateAction(\ThomasWoehlke\T3gtd\Domain\Model\Context $ctx)
    {
        $this->contextRepository->update($ctx);
        $msg = LocalizationUtility::translate('tx_t3gtd_flash.context.updated', $this->extName, null);
        $this->addFlashMessage($msg, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->myRedirect('show',array(),'UserConfig');
    }

    /**
     * action delete
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @return void
     */
    public function deleteAction(\ThomasWoehlke\T3gtd\Domain\Model\Context $context)
    {
        $hasTasks = $this->taskRepository->hasTasksForContext($context);
        $hasProjects = $this->projectRepository->hasProjectsForContext($context);
        if($hasTasks || $hasProjects){
            $msg = LocalizationUtility::translate(
                'tx_t3gtd_flash.context.cannot_delete', $this->extName, null);
            $this->addFlashMessage($msg, $context->getNameDe(), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
        } else {
            $this->contextRepository->remove($context);
            $msg = LocalizationUtility::translate(
                'tx_t3gtd_flash.context.deleted', $this->extName, null);
            $this->addFlashMessage($msg, $context->getNameDe(), \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING);
        }
        $this->myRedirect('show',array(),'UserConfig');
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
