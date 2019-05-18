<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class ProjectTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Domain\Model\Project
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \ThomasWoehlke\Gtd\Domain\Model\Project();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }



    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );

    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );

    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
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

    /**
     * @test
     */
    public function getParentReturnsInitialValueForProject()
    {
        self::assertEquals(
            null,
            $this->subject->getParent()
        );

    }

    /**
     * @test
     */
    public function setParentForProjectSetsParent()
    {
        $parentFixture = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $this->subject->setParent($parentFixture);

        self::assertAttributeEquals(
            $parentFixture,
            'parent',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getChildrenReturnsInitialValueForProject()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getChildren()
        );

    }

    /**
     * @test
     */
    public function setChildrenForObjectStorageContainingProjectSetsChildren()
    {
        $child = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $objectStorageHoldingExactlyOneChildren = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneChildren->attach($child);
        $this->subject->setChildren($objectStorageHoldingExactlyOneChildren);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneChildren,
            'children',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function addChildToObjectStorageHoldingChildren()
    {
        $child = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $childrenObjectStorageMock = $this->getMock(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class, ['attach'], [], '', false);
        $childrenObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($child));
        $this->inject($this->subject, 'children', $childrenObjectStorageMock);

        $this->subject->addChild($child);
    }

    /**
     * @test
     */
    public function removeChildFromObjectStorageHoldingChildren()
    {
        $child = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $childrenObjectStorageMock = $this->getMock(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class, ['detach'], [], '', false);
        $childrenObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($child));
        $this->inject($this->subject, 'children', $childrenObjectStorageMock);

        $this->subject->removeChild($child);

    }
}
