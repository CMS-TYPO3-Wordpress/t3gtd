<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class ContextControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Controller\ContextController
     */
    protected $subject = null;

    protected $langKey = 0;

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\Gtd\Controller\ContextController::class, ['redirect', 'forward', 'addFlashMessage','getLanguageId','getLanguage','myRedirect','redirectToUri'], [], '', false);

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
    public function newActionTest(){

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

        $view->expects(self::at(0))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->newAction();
    }

    /**
     * @test
     */
    public function createActionTest()
    {
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        $contextRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ContextRepository::class, ['add'], [], '', false);
        $contextRepository->expects(self::once())->method('add')->with($currentContext);
        $this->inject($this->subject, 'contextRepository', $contextRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->createAction($currentContext);
    }

    /**
     * @test
     */
    public function editActionTest()
    {
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
        $this->inject($this->subject, 'view', $view);

        $view->expects(self::at(0))->method('assign')->withConsecutive(['mycontext', $currentContext]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);

        $this->subject->editAction($currentContext);
    }


    /**
     * @test
     */
    public function updateActionTest()
    {
        $context = new \ThomasWoehlke\Gtd\Domain\Model\Context();

        $contextRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ContextRepository::class, ['update'], [], '', false);
        $contextRepository->expects(self::once())->method('update')->with($context);
        $this->inject($this->subject, 'contextRepository', $contextRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->updateAction($context);
    }

    /**
     * @test
     */
    public function deleteActionTest()
    {
        $context = new \ThomasWoehlke\Gtd\Domain\Model\Context();

        $contextRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ContextRepository::class, ['remove'], [], '', false);
        $contextRepository->expects(self::once())->method('remove')->with($context);
        $this->inject($this->subject, 'contextRepository', $contextRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['hasTasksForContext'], [$context], '', false);
        $taskRepository->expects(self::once())->method('hasTasksForContext')->with($context)->will(self::returnValue(false));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['hasProjectsForContext'], [$context], '', false);
        $projectRepository->expects(self::once())->method('hasProjectsForContext')->with($context)->will(self::returnValue(false));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->deleteAction($context);
    }
}
