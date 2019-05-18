<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class ProjectControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Controller\ProjectController
     */
    protected $subject = null;

    protected $langKey = 0;

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\Gtd\Controller\ProjectController::class, ['redirect', 'forward', 'addFlashMessage','getLanguageId','getLanguage','myRedirect','redirectToUri'], [], '', false);

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
    public function showActionTest1(){

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

        $deleteable = false;

        $taskList = $this->getMock(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface::class, ['count','getQuery','getFirst','toArray','current','next','key','valid','rewind','offsetExists','offsetGet','offsetSet','offsetUnset'], [], '', false);
        $taskList->expects(self::once())->method('count')->will(self::returnValue(2));

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$currentContext], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByProject'], [$project1], '', false);
        $taskRepository->expects(self::once())->method('findByProject')->will(self::returnValue($taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['project', $project1]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);
        $view->expects(self::at(4))->method('assign')->withConsecutive(['tasks', $taskList]);
        $view->expects(self::at(5))->method('assign')->withConsecutive(['deleteable', $deleteable]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->showAction($project1);
    }

    /**
     * @test
     */
    public function showActionTest2(){
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

        $deleteable = false;

        $taskList = $this->getMock(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface::class, ['count','getQuery','getFirst','toArray','current','next','key','valid','rewind','offsetExists','offsetGet','offsetSet','offsetUnset'], [], '', false);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$currentContext], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByRootProjectAndContext'], [$currentContext], '', false);
        $taskRepository->expects(self::once())->method('findByRootProjectAndContext')->will(self::returnValue($taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['project', null]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);
        $view->expects(self::at(4))->method('assign')->withConsecutive(['tasks', $taskList]);
        $view->expects(self::at(5))->method('assign')->withConsecutive(['deleteable', $deleteable]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->showAction(null);
    }

    /**
     * @test
     */
    public function editActionTest(){
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
        $view->expects(self::at(0))->method('assign')->withConsecutive(['project', $project1]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->editAction($project1);
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
        $project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project1->setName('p1');
        $project1->setDescription('d1');
        $project1->setContext($currentContext);
        $project1->setUserAccount($userLoggedIn);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['update'], [$project1], '', false);
        $projectRepository->expects(self::once())->method('update')->with($project1);
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->updateAction($project1);
    }

    /**
     * @test
     */
    public function deleteActionTest1(){
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $userConfig->setUserAccount($userLoggedIn);
        $userConfig->setDefaultContext($currentContext);
        $project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project1->setName('p1');
        $project1->setDescription('d1');
        $project1->setContext($currentContext);
        $project1->setUserAccount($userLoggedIn);

        $taskList = $this->getMock(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface::class, ['count','getQuery','getFirst','toArray','current','next','key','valid','rewind','offsetExists','offsetGet','offsetSet','offsetUnset'], [], '', false);
        $taskList->expects(self::once())->method('count')->will(self::returnValue(0));

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['remove'], [$project1], '', false);
        $projectRepository->expects(self::once())->method('remove')->with($project1);
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByProject'], [$project1], '', false);
        $taskRepository->expects(self::once())->method('findByProject')->will(self::returnValue($taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->deleteAction($project1);
    }

    /**
     * @test
     */
    public function deleteActionTest2(){
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $userConfig->setUserAccount($userLoggedIn);
        $userConfig->setDefaultContext($currentContext);
        $project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project1->setName('p1');
        $project1->setDescription('d1');
        $project1->setContext($currentContext);
        $project1->setUserAccount($userLoggedIn);

        $taskList = $this->getMock(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface::class, ['count','getQuery','getFirst','toArray','current','next','key','valid','rewind','offsetExists','offsetGet','offsetSet','offsetUnset'], [], '', false);
        $taskList->expects(self::once())->method('count')->will(self::returnValue(2));

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['remove'], [$project1], '', false);
        $projectRepository->expects(self::never())->method('remove')->with($project1);
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByProject'], [$project1], '', false);
        $taskRepository->expects(self::once())->method('findByProject')->will(self::returnValue($taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->deleteAction($project1);
    }

    /**
     * @test
     */
    public function moveProjectActionTest(){
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $userConfig->setUserAccount($userLoggedIn);
        $userConfig->setDefaultContext($currentContext);
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
        //$rootProjects = array($project1,$project2);

        $task = new \ThomasWoehlke\Gtd\Domain\Model\Task();
        $task->setContext($currentContext);
        $task->setUserAccount($userLoggedIn);
        $task->setProject($project1);
        $task->setText('Task Description');
        $task->setTitle('Do something!');

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['update'], [$project1], '', false);
        $projectRepository->expects(self::at(0))->method('update')->withConsecutive([$project1]);
        $projectRepository->expects(self::at(1))->method('update')->withConsecutive([$project2]);
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->moveProjectAction($project1,$project2);
    }

    /**
     * @test
     */
    public function moveTaskActionTest(){
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $userConfig->setUserAccount($userLoggedIn);
        $userConfig->setDefaultContext($currentContext);
        $project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project1->setName('p1');
        $project1->setDescription('d1');
        $project1->setContext($currentContext);
        $project1->setUserAccount($userLoggedIn);

        $task = new \ThomasWoehlke\Gtd\Domain\Model\Task();
        $task->setContext($currentContext);
        $task->setUserAccount($userLoggedIn);
        $task->setProject($project1);
        $task->setText('Task Description');
        $task->setTitle('Do something!');

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['update','getMaxProjectOrderId'], [$task,null], '', false);
        $taskRepository->expects(self::once())->method('update')->with($task);
        $taskRepository->expects(self::once())->method('getMaxProjectOrderId')->will(self::returnValue(10));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->moveTaskAction($task,$project1);
    }

    /**
     * @test
     */
    public function listActionTest(){
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
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects','findAll'], [$currentContext,null], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($rootProjects));
        $projectRepository->expects(self::once())->method('findAll')->will(self::returnValue($rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['projects', $rootProjects]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
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
        $view->expects(self::at(0))->method('assign')->withConsecutive(['parentProject', $project1]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->newAction($project1);
    }

    /**
     * @test
     */
    public function createActionTest(){
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
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['update','add'], [$project1,$project2], '', false);
        $projectRepository->expects(self::once())->method('update')->will(self::returnValue($project1));
        $projectRepository->expects(self::once())->method('add')->will(self::returnValue($project2));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $dbresult = array();
        $dbresult['uid'] = 1;

        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, array(), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('exec_SELECTgetSingleRow')->will(self::returnValue($dbresult));
        $GLOBALS['TYPO3_DB']->expects(self::any())->method('fullQuoteStr')->will(self::returnValue('test'));

        $GLOBALS['TYPO3_LOADED_EXT'] = ['t3gtd'=>[]];

        $this->subject->createAction($project2,$project1);
    }

}
