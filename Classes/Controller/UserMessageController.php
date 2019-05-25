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
 * Class UserMessageController
 *
 * @package ThomasWoehlke\T3gtd\Controller
 */
class UserMessageController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * userMessageRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\UserMessageRepository
     * @inject
     */
    protected $userMessageRepository = null;

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
     * action list
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $thisUser
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $otherUser
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     *
     * @return void
     */
    public function listAction(
        \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $thisUser,
        \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $otherUser)
    {
        $this->view->assign('thisUser', $thisUser);
        $this->view->assign('otherUser', $otherUser);
        $userMessages = $this->userMessageRepository->findAllBetweenTwoUsers($thisUser,$otherUser);
        $this->view->assign('userMessages', $userMessages);
        foreach ($userMessages as $msg){
            if((!$msg->isReadByReceiver())&&($msg->getReceiver()->getUid() == $thisUser->getUid())){
                $msg->setReadByReceiver(true);
                $this->userMessageRepository->update($msg);
            }
        }
        $context = $this->contextService->getCurrentContext();
        $this->view->assign('contextList',$this->contextService->getContextList());
        $this->view->assign('currentContext',$context);
        $this->view->assign('rootProjects',$this->projectRepository->getRootProjects($context));
        $this->view->assign('langKey',$this->getLanguageId());
    }

    /**
     * action create
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\UserMessage $newUserMessage
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $sender
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $receiver
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     *
     * @return void
     */
    public function createAction(
        \ThomasWoehlke\T3gtd\Domain\Model\UserMessage $newUserMessage,
        \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $sender,
        \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $receiver)
    {
        $newUserMessage->setReadByReceiver(false);
        $newUserMessage->setSender($sender);
        $newUserMessage->setReceiver($receiver);
        $this->userMessageRepository->add($newUserMessage);
        $arguments = array('thisUser'=> $sender,'otherUser' => $receiver);
        $this->myRedirect('list',$arguments);
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
        $actionName='list',$controllerArguments=array(),$controllerName = 'UserMessage'){
        $langId=$this->getLanguageId();
        $pid = $this->uriBuilder->getTargetPageUid();
        $this->uriBuilder->reset()->setArguments(array('L' => $langId))->setTargetPageUid($pid);
        $uri = $this->uriBuilder->uriFor($actionName, $controllerArguments,$controllerName);
        $this->redirectToUri($uri);
    }

}
