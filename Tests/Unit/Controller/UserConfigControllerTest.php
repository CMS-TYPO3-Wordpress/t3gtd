<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class UserConfigControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Controller\UserConfigController
     */
    protected $subject = null;

    protected $langKey = 0;

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\Gtd\Controller\UserConfigController::class, ['redirect', 'forward', 'addFlashMessage','getLanguageId','getLanguage','myRedirect','redirectToUri'], [], '', false);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        $this->inject($this->subject, 'objectManager', $objectManager);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
        $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');

        $this->inject($this->subject, 'configurationManager', $configurationManager);

        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $this->getMock(\TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class,array(), array(), '', false);

        $uriBuilder->expects(self::any())->method('reset')->will(self::returnValue($uriBuilder));
        $uriBuilder->expects(self::any())->method('setArguments')->will(self::returnValue($uriBuilder));
        $uriBuilder->expects(self::any())->method('setTargetPageUid')->will(self::returnValue($uriBuilder));
        $uriBuilder->expects(self::any())->method('uriFor');

        $this->inject($this->subject, 'uriBuilder', $uriBuilder);

        $this->subject->expects(self::any())->method('redirectToUri');
    }

    protected function tearDown()
    {
        parent::tearDown();
    }



    /**
     * @test
     */
    public function listActionTest()
    {
        $allUserConfigs = $this->getMock(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class, [], [], '', false);

        $userConfigRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\UserConfigRepository::class, ['findAll'], [], '', false);
        $userConfigRepository->expects(self::once())->method('findAll')->will(self::returnValue($allUserConfigs));
        $this->inject($this->subject, 'userConfigRepository', $userConfigRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::once())->method('assign')->with('userConfigs', $allUserConfigs);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function  showActionTest(){
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $userConfig->setUserAccount($userLoggedIn);
        $userConfig->setDefaultContext($currentContext);
        $contextList = [$currentContext];
        $project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project1->setName('p1');
        $project1->setDescription('d1');
        $project1->setContext($currentContext);
        $project1->setUserAccount($userLoggedIn);
        $project2 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project2->setName('p2');
        $project2->setDescription('d2');
        $project2->setContext($currentContext);
        $project2->setUserAccount($userLoggedIn);
        $rootProjects = array($project1,$project2);

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $userConfigRepository
        $userConfigRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\UserConfigRepository::class, ['findByUserAccount'], [$userLoggedIn], '', false);
        $userConfigRepository->expects(self::once())->method('findByUserAccount')->will(self::returnValue($userConfig));
        $this->inject($this->subject, 'userConfigRepository', $userConfigRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$currentContext], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['thisUser', $userLoggedIn]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['userConfig', $userConfig]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(4))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->showAction();
    }

    /**
     * @test
     */
    public function updateActionTest(){
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $userConfig->setUserAccount($userLoggedIn);
        $userConfig->setDefaultContext($currentContext);

        $dbresult = array();
        $dbresult['uid'] = 1;


        //inject $userConfigRepository
        $userConfigRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\UserConfigRepository::class, ['findByUid','update'], [$userLoggedIn,$userConfig], '', false);
        $userConfigRepository->expects(self::once())->method('findByUid')->will(self::returnValue($userConfig));
        $userConfigRepository->expects(self::once())->method('update')->with($userConfig);
        $this->inject($this->subject, 'userConfigRepository', $userConfigRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['setCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('setCurrentContext')->with($currentContext);
        $this->inject($this->subject, 'contextService', $contextService);


        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);

        $this->inject($this->subject, 'view', $view);

        $this->subject->updateAction($userConfig);
    }
}
