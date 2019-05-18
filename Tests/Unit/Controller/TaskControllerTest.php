<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Controller;

use ThomasWoehlke\Gtd\Domain\Model\Project;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class TaskControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Controller\TaskController
     */
    protected $subject = null;

    protected $taskEnergy = null;
    protected $taskTime = null;
    protected $userLoggedIn = null;
    protected $userConfig = null;
    protected $currentContext = null;
    protected $contextList = null;
    protected $project1 = null;
    protected $project2 = null;
    protected $rootProjects = null;
    protected $task1 = null;
    protected $task2 = null;
    protected $taskList = null;

    protected $langKey = 0;

    protected $taskStates = array(
        'inbox' => 0, 'today' => 1, 'next' => 2, 'waiting' => 3, 'scheduled' => 4, 'someday' => 5, 'completed' => 6 , 'trash' => 7
    );

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\Gtd\Controller\TaskController::class, ['redirect', 'forward', 'addFlashMessage','getRedirectFromTask','getLanguageId','getLanguage','myRedirect','redirectToUri'], [], '', false);
        $this->taskEnergy = array(
            0 => 'none',
            1 => 'low',
            2 => 'mid',
            3 => 'high'
        );
        $this->taskTime = array(
            0 => 'none',
            1 => '5 min',
            2 => '10 min',
            3 => '15 min',
            4 => '30 min',
            5 => '45 min',
            6 => '1 hour',
            7 => '2 hours',
            8 => '3 hours',
            9 => '4 hours',
            10 => '6 hours',
            11 => '8 hours',
            12 => 'more'
        );
        $this->userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $this->userConfig = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
        $this->currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $this->currentContext->setNameDe('Arbeit');
        $this->currentContext->setNameEn('Work');
        $this->contextList = [$this->currentContext];
        $this->userConfig->setUserAccount($this->userLoggedIn);
        $this->userConfig->setDefaultContext($this->currentContext);
        $this->project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $this->project1->setName('p1');
        $this->project1->setDescription('d1');
        $this->project1->setContext($this->currentContext);
        $this->project1->setUserAccount($this->userLoggedIn);
        $this->project2 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $this->project2->setName('p2');
        $this->project2->setDescription('d2');
        $this->project2->setContext($this->currentContext);
        $this->project2->setUserAccount($this->userLoggedIn);
        $this->rootProjects = array($this->project1,$this->project2);

        $this->task1 = new \ThomasWoehlke\Gtd\Domain\Model\Task();
        $this->task1->setContext($this->currentContext);
        $this->task1->setUserAccount($this->userLoggedIn);
        $this->task1->setProject($this->project1);
        $this->task1->setText('Task Description');
        $this->task1->setTitle('Do something!');

        $this->task2 = new \ThomasWoehlke\Gtd\Domain\Model\Task();
        $this->task2->setContext($this->currentContext);
        $this->task2->setUserAccount($this->userLoggedIn);
        $this->task2->setProject($this->project1);
        $this->task2->setText('Task Description 2');
        $this->task2->setTitle('Do something 2!');

        $this->taskList = [$this->task1,$this->task2];

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
    public function editActionTest(){

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['task', $this->task1]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['taskEnergy', $this->taskEnergy]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['taskTime', $this->taskTime]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(4))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(5))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);
        $view->expects(self::at(6))->method('assign')->withConsecutive(['langKey', $this->langKey]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->editAction($this->task1);
    }

    /**
     * @test
     */
    public function updateActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['update','getMaxTaskStateOrderId','findByUid'], [$this->task1,null,1], '', false);
        $taskRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->task1));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(10));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->updateAction($this->task1);
    }

    /**
     * @test
     */
    public function inboxActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['inbox'])->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->inboxAction();
    }

    /**
     * @test
     */
    public function todayActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState','getScheduledTasksOfCurrentDay','getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['today'])->will(self::returnValue($this->taskList));
        $taskRepository->expects(self::once())->method('getScheduledTasksOfCurrentDay')->will(self::returnValue(array()));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->todayAction();
    }

    /**
     * @test
     */
    public function nextActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['next'])->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->nextAction();
    }

    /**
     * @test
     */
    public function waitingActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['waiting'])->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->waitingAction();
    }

    /**
     * @test
     */
    public function scheduledActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState','getScheduledTasksOfCurrentDay','getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['scheduled'])->will(self::returnValue($this->taskList));
        $taskRepository->expects(self::once())->method('getScheduledTasksOfCurrentDay')->will(self::returnValue(array()));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->scheduledAction();
    }

    /**
     * @test
     */
    public function somedayActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['someday'])->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->somedayAction();
    }

    /**
     * @test
     */
    public function completedActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['completed'])->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->completedAction();
    }

    /**
     * @test
     */
    public function trashActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndTaskState')->with($this->userLoggedIn,$this->currentContext,$this->taskStates['trash'])->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->trashAction();
    }

    /**
     * @test
     */
    public function focusActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndHasFocus'], [], '', false);
        $taskRepository->expects(self::once())->method('findByUserAccountAndHasFocus')->with($this->userLoggedIn,$this->currentContext)->will(self::returnValue($this->taskList));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['tasks', $this->taskList]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->focusAction();
    }

    /**
     * @test
     */
    public function emptyTrashActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState','remove'], [], '', false);
        $taskRepository->expects(self::at(0))->method('findByUserAccountAndTaskState')->withConsecutive([$this->userLoggedIn,$this->currentContext,$this->taskStates['trash']])->will(self::returnValue($this->taskList));
        $taskRepository->expects(self::at(1))->method('remove')->withConsecutive([$this->task1]);
        $taskRepository->expects(self::at(2))->method('remove')->withConsecutive([$this->task2]);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->emptyTrashAction();
    }

    /**
     * @test
     */
    public function transformTaskIntoProjectActionTest(){

        $parentProject = $this->task1->getProject();
        $newProject = new Project();
        $newProject->setContext($this->task1->getContext());
        $newProject->setUserAccount($this->task1->getUserAccount());
        $newProject->setParent($parentProject);
        $newProject->setName($this->task1->getTitle());
        $newProject->setDescription($this->task1->getText());
        if($parentProject != null){
            $parentProject->addChild($newProject);
        }

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['remove'], [], '', false);
        $taskRepository->expects(self::once())->method('remove')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['add','update'], [$newProject,$parentProject], '', false);
        $projectRepository->expects(self::once())->method('add')->with($newProject);
        $projectRepository->expects(self::once())->method('update')->with($parentProject);
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $this->subject->transformTaskIntoProjectAction($this->task1);
    }

    /**
     * @test
     */
    public function completeTaskActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::at(0))->method('getMaxTaskStateOrderId')->withConsecutive([$this->userLoggedIn,$this->currentContext,$this->taskStates['completed']])->will(self::returnValue(10));
        $taskRepository->expects(self::at(1))->method('update')->withConsecutive([$this->task1]);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->completeTaskAction($this->task1);
    }

    /**
     * @test
     */
    public function undoneTaskActionTest(){
        $this->task1->setLastTaskState($this->taskStates['inbox']);
        $this->task1->setTaskState($this->taskStates['completed']);

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::at(0))->method('getMaxTaskStateOrderId')->withConsecutive([$this->userLoggedIn,$this->currentContext,$this->taskStates['inbox']])->will(self::returnValue(10));
        $taskRepository->expects(self::at(1))->method('update')->withConsecutive([$this->task1]);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->undoneTaskAction($this->task1);
    }

    /**
     * @test
     */
    public function setFocusActionTest(){

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['update'], [], '', false);
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->setFocusAction($this->task1);
    }

    /**
     * @test
     */
    public function unsetFocusActionTest(){

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['update'], [], '', false);
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->unsetFocusAction($this->task1);
    }

    /**
     * @test
     */
    public function newActionTest(){
        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($this->contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$this->rootProjects], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($this->rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['taskEnergy', $this->taskEnergy]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['taskTime', $this->taskTime]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['contextList', $this->contextList]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['currentContext', $this->currentContext]);
        $view->expects(self::at(4))->method('assign')->withConsecutive(['rootProjects', $this->rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->newAction();
    }

    /**
     * @test
     */
    public function createActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxProjectOrderId','getMaxTaskStateOrderId','add'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxProjectOrderId')->will(self::returnValue(12));
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('add')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->createAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToInboxActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToInboxAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToTodayActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToTodayAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToNextActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToNextAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToWaitingActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToWaitingAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToSomedayActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToSomedayAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToCompletedActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToCompletedAction($this->task1);
    }

    /**
     * @test
     */
    public function moveToTrashActionTest(){

        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::once())->method('getMaxTaskStateOrderId')->will(self::returnValue(14));
        $taskRepository->expects(self::once())->method('update')->with($this->task1);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveToTrashAction($this->task1);
    }

    /**
     * @test
     */
    public function moveAllCompletedToTrashActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['findByUserAccountAndTaskState','getMaxTaskStateOrderId','update'], [], '', false);
        $taskRepository->expects(self::at(0))->method('findByUserAccountAndTaskState')->withConsecutive([$this->userLoggedIn,$this->currentContext,$this->taskStates['completed']])->will(self::returnValue($this->taskList));
        $taskRepository->expects(self::at(1))->method('getMaxTaskStateOrderId')->withConsecutive([$this->userLoggedIn,$this->currentContext,$this->taskStates['trash']])->will(self::returnValue(14));
        $taskRepository->expects(self::at(2))->method('update')->withConsecutive([$this->task1]);
        $taskRepository->expects(self::at(3))->method('update')->withConsecutive([$this->task2]);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveAllCompletedToTrashAction();
    }

    /**
     * @test
     */
    public function moveTaskOrderActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getTasksToReorderByOrderIdTaskState','update'], [], '', false);
        $taskRepository->expects(self::at(0))->method('getTasksToReorderByOrderIdTaskState')->withConsecutive([$this->userLoggedIn, $this->currentContext, $this->task1, $this->task2, $this->taskStates['inbox']])->will(self::returnValue($this->taskList));
        $taskRepository->expects(self::at(1))->method('update')->withConsecutive([$this->task1]);
        $taskRepository->expects(self::at(2))->method('update')->withConsecutive([$this->task2]);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveTaskOrderAction($this->task2,$this->task1);
    }

    /**
     * @test
     */
    public function moveTaskOrderInsideProjectActionTest(){
        // inject userAccountRepository
        $userAccountRepository = $this->getMock(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class, ['findByUid'], [1], '', false);
        $userAccountRepository->expects(self::once())->method('findByUid')->will(self::returnValue($this->userLoggedIn));
        $this->inject($this->subject, 'userAccountRepository', $userAccountRepository);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($this->currentContext));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $taskRepository
        $taskRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\TaskRepository::class, ['getTasksToReorderByOrderIdProject','update'], [], '', false);
        $taskRepository->expects(self::at(0))->method('getTasksToReorderByOrderIdProject')->withConsecutive([$this->userLoggedIn, $this->currentContext, $this->task1, $this->task2, $this->project1])->will(self::returnValue($this->taskList));
        $taskRepository->expects(self::at(1))->method('update')->withConsecutive([$this->task1]);
        $taskRepository->expects(self::at(2))->method('update')->withConsecutive([$this->task2]);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->moveTaskOrderInsideProjectAction($this->task2,$this->task1);
    }


}
