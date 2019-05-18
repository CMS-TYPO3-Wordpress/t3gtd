<?php

namespace ThomasWoehlke\Gtd\Tests\Unit\Service;

/**
 * Created by PhpStorm.
 * User: tw
 * Date: 28.07.16
 * Time: 19:08
 */
class ContextServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \ThomasWoehlke\Gtd\Service\ContextService
     */
    protected $subject = null;

    protected $taskEnergy = null;
    protected $taskTime = null;
    protected $userLoggedIn = null;
    protected $userConfig = null;
    protected $currentContext = null;
    protected $contextList = null;

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['addFlashMessage'], [], '', false);

        $this->userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $this->currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $this->currentContext->setNameDe('Arbeit');
        $this->currentContext->setNameEn('Work');
        $this->currentContext->setUserAccount($this->userLoggedIn);
        $this->userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $this->userConfig->setUserAccount($this->userLoggedIn);
        $this->userConfig->setDefaultContext($this->currentContext);
        $this->contextList = [$this->currentContext];

        $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], 1, 0, true);

        $GLOBALS['TSFE']->fe_user = $this->getMock( \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class, ['getKey','setKey','storeSessionData'], [], '', false);
        $GLOBALS['TSFE']->fe_user->expects(self::any())->method('getKey')->with('ses', 'tx_t3gtd_fesessiondata')->will(self::returnValue(null));

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getContextListTest(){


        $myContextList = $this->getMock(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface::class, ['count','getQuery','getFirst','toArray','current','next','key','valid','rewind','offsetExists','offsetGet','offsetSet','offsetUnset'], [], '', false);
        $myContextList->expects(self::once())->method('count')->will(self::returnValue(1));

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        $contextRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ContextRepository::class, ['findAllByUserAccount'], [], '', false);
        $contextRepository->expects(self::once())->method('findAllByUserAccount')->with($this->userLoggedIn)->will(self::returnValue($myContextList));
        $this->inject($this->subject, 'contextRepository', $contextRepository);

        $this->subject->getContextList();
    }

    /**
     * @test
     */
    public function getCurrentContextTest(){

        $userConfig2 = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $userConfig2->setUserAccount($this->userLoggedIn);
        $userConfig2->setDefaultContext($this->currentContext);

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $userConfigRepository
        $userConfigRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\UserConfigRepository::class,
            ['findByUserAccount'], [$this->userLoggedIn], '', false);
        $userConfigRepository->expects(self::once())->method('findByUserAccount')->will(self::returnValue($userConfig2));
        $this->inject($this->subject, 'userConfigRepository', $userConfigRepository);

        $this->subject->getCurrentContext();
    }

    /**
     * @test
     */
    public function setCurrentContextTest(){

        $sessionData['contextUid'] = $this->currentContext->getUid();

        $GLOBALS['TSFE']->fe_user->expects(self::once())->method('setKey')->with('ses', 'tx_t3gtd_fesessiondata',$sessionData);
        $GLOBALS['TSFE']->fe_user->expects(self::once())->method('storeSessionData');

        $this->subject->setCurrentContext($this->currentContext);
    }
}
