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

/**
 * Class UserConfigController
 *
 * @package ThomasWoehlke\T3gtd\Controller
 */
class UserConfigController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * userConfigRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\UserConfigRepository
     * @inject
     */
    protected $userConfigRepository = null;


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

    /**
     * projectRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ProjectRepository
     * @inject
     */
    protected $projectRepository = null;


    private $extName = 't3gtd';

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $userConfigs = $this->userConfigRepository->findAll();
        $this->view->assign('userConfigs', $userConfigs);
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction(){
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
        $currentContext = $this->contextService->getCurrentContext();
        $contextList = $this->contextService->getContextList();
        $rootProjects = $this->projectRepository->getRootProjects($currentContext);
        $this->view->assign('thisUser', $userObject);
        $this->view->assign('userConfig',$userConfig);
        $this->view->assign('contextList',$contextList);
        $this->view->assign('currentContext',$currentContext);
        $this->view->assign('rootProjects',$rootProjects);
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action update
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\UserConfig $userConfig
     * @return void
     */
    public function updateAction(\ThomasWoehlke\T3gtd\Domain\Model\UserConfig $userConfig){
        $persistentUserConfig = $this->userConfigRepository->findByUid($userConfig->getUid());
        $ctx = $userConfig->getDefaultContext();
        $persistentUserConfig->setDefaultContext($ctx);
        $this->userConfigRepository->update($persistentUserConfig);
        $this->contextService->setCurrentContext($ctx);
        $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
            'tx_t3gtd_flash.userconfig.updated', $this->extName, null);
        $this->addFlashMessage($msg, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
        $this->myRedirect('show');
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
     */
    private function myRedirect(
        $actionName='show',$controllerArguments=array(),$controllerName = 'UserConfig'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }
}
