<?php
namespace ThomasWoehlke\T3gtd\Service;

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

use ThomasWoehlke\T3gtd\Domain\Model\Context;
use ThomasWoehlke\T3gtd\Domain\Model\UserConfig;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

class ContextService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * contextRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\ContextRepository
     * @inject
     */
    protected $contextRepository = null;

    /**
     * userAccountRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $userAccountRepository = null;

    /**
     * userConfigRepository
     *
     * @var \ThomasWoehlke\T3gtd\Domain\Repository\UserConfigRepository
     * @inject
     */
    protected $userConfigRepository = null;

    /**
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getContextList(){
        /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject */
        $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        $contextList = $this->contextRepository->findAllByUserAccount($userObject);
        if($contextList->count() == 0){
            $this->createDefaultContexts($userObject);
            $contextList = $this->contextRepository->findAllByUserAccount($userObject);
        }
        return $contextList;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    private function createDefaultContexts(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject)
    {
        $work = new Context();
        $private = new Context();
        $work->setNameDe("Arbeit");
        $work->setNameEn("Work");
        $work->setUserAccount($userObject);
        $private->setNameDe("Privat");
        $private->setNameEn("Private");
        $private->setUserAccount($userObject);
        $this->contextRepository->add($work);
        $this->contextRepository->add($private);
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $persistenceManager->persistAll();
    }

    /**
     * @return \ThomasWoehlke\T3gtd\Domain\Model\Context
     */
    public function getCurrentContext(){
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'tx_t3gtd_fesessiondata');
        $contextUid = $sessionData['contextUid'];
        if($contextUid == null){
            /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject */
            $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            /** @var \ThomasWoehlke\T3gtd\Domain\Model\UserConfig $userConfig */
            $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
            if($userConfig == null){
                $this->createUserConfig($userObject);
                $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
            }
            $defaultContext = $userConfig->getDefaultContext();
            $this->setCurrentContext($defaultContext);
            return $defaultContext;
        } else {
            /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject */
            $userObject = $this->userAccountRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            /** @var \ThomasWoehlke\T3gtd\Domain\Model\UserConfig $userConfig */
            $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
            if($userConfig == null){
                $this->createUserConfig($userObject);
                $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
            }
            /** @var \ThomasWoehlke\T3gtd\Domain\Model\Context $activeContext */
            $activeContext = $this->contextRepository->findByUid($contextUid);
            if($activeContext == null){
                $activeContext = $userConfig->getDefaultContext();
            }
            if($activeContext->getUserAccount()->getUid() == $userObject->getUid()){
                return $activeContext;
            } else {
                /** @var \ThomasWoehlke\T3gtd\Domain\Model\UserConfig $userConfig */
                $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
                if($userConfig == null){
                    $this->createUserConfig($userObject);
                    $userConfig = $this->userConfigRepository->findByUserAccount($userObject);
                }
                $activeContext = $userConfig->getDefaultContext();
                $this->setCurrentContext($activeContext);
                return $activeContext;
            }
        }
    }

    /**
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @return void
     */
    public function setCurrentContext(\ThomasWoehlke\T3gtd\Domain\Model\Context $context){
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'tx_t3gtd_fesessiondata');
        $sessionData['contextUid'] = $context->getUid();
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'tx_t3gtd_fesessiondata', $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject
     * @return void
     */
    private function createUserConfig(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject){
        $userConfig2 = new UserConfig();
        $userConfig2->setUserAccount($userObject);
        $contextList = $this->getContextList();
        $ctx = $contextList->getFirst();
        $userConfig2->setDefaultContext($ctx);
        $this->userConfigRepository->add($userConfig2);
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $persistenceManager = $objectManager->get(
            "TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $persistenceManager->persistAll();
    }

}
