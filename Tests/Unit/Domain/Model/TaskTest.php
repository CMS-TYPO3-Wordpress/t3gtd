<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class TaskTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Domain\Model\Task
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \ThomasWoehlke\Gtd\Domain\Model\Task();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }



    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );

    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getTextReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getText()
        );

    }

    /**
     * @test
     */
    public function setTextForStringSetsText()
    {
        $this->subject->setText('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'text',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getFocusReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getFocus()
        );

    }

    /**
     * @test
     */
    public function setFocusForBoolSetsFocus()
    {
        $this->subject->setFocus(true);

        self::assertAttributeEquals(
            true,
            'focus',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getTaskStateReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setTaskStateForIntSetsTaskState()
    {
    }

    /**
     * @test
     */
    public function getLastTaskStateReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setLastTaskStateForIntSetsLastTaskState()
    {
    }

    /**
     * @test
     */
    public function getTaskEnergyReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setTaskEnergyForIntSetsTaskEnergy()
    {
    }

    /**
     * @test
     */
    public function getTaskTimeReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setTaskTimeForIntSetsTaskTime()
    {
    }

    /**
     * @test
     */
    public function getDueDateReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getDueDate()
        );

    }

    /**
     * @test
     */
    public function setDueDateForDateTimeSetsDueDate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDueDate($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'dueDate',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getOrderIdProjectReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setOrderIdProjectForIntSetsOrderIdProject()
    {
    }

    /**
     * @test
     */
    public function getOrderIdTaskStateReturnsInitialValueForInt()
    {
    }

    /**
     * @test
     */
    public function setOrderIdTaskStateForIntSetsOrderIdTaskState()
    {
    }

    /**
     * @test
     */
    public function getProjectReturnsInitialValueForProject()
    {
        self::assertEquals(
            null,
            $this->subject->getProject()
        );

    }

    /**
     * @test
     */
    public function setProjectForProjectSetsProject()
    {
        $projectFixture = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $this->subject->setProject($projectFixture);

        self::assertAttributeEquals(
            $projectFixture,
            'project',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getContextReturnsInitialValueForContext()
    {
        self::assertEquals(
            null,
            $this->subject->getContext()
        );

    }

    /**
     * @test
     */
    public function setContextForContextSetsContext()
    {
        $contextFixture = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $this->subject->setContext($contextFixture);

        self::assertAttributeEquals(
            $contextFixture,
            'context',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getUserAccountReturnsInitialValueForUserAccount()
    {
        self::assertEquals(
            null,
            $this->subject->getUserAccount()
        );

    }

    /**
     * @test
     */
    public function setUserAccountForUserAccountSetsUserAccount()
    {
        $userAccountFixture = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser();
        $this->subject->setUserAccount($userAccountFixture);

        self::assertAttributeEquals(
            $userAccountFixture,
            'userAccount',
            $this->subject
        );

    }
}
