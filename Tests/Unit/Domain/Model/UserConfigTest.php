<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class UserConfigTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Domain\Model\UserConfig
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \ThomasWoehlke\Gtd\Domain\Model\UserConfig();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }



    /**
     * @test
     */
    public function getDefaultContextReturnsInitialValueForContext()
    {
        self::assertEquals(
            null,
            $this->subject->getDefaultContext()
        );

    }

    /**
     * @test
     */
    public function setDefaultContextForContextSetsDefaultContext()
    {
        $defaultContextFixture = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $this->subject->setDefaultContext($defaultContextFixture);

        self::assertAttributeEquals(
            $defaultContextFixture,
            'defaultContext',
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
