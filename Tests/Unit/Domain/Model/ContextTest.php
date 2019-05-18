<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class ContextTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\Gtd\Domain\Model\Context
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \ThomasWoehlke\Gtd\Domain\Model\Context();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }



    /**
     * @test
     */
    public function getNameDeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getNameDe()
        );

    }

    /**
     * @test
     */
    public function setNameDeForStringSetsNameDe()
    {
        $this->subject->setNameDe('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'nameDe',
            $this->subject
        );

    }

    /**
     * @test
     */
    public function getNameEnReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getNameEn()
        );

    }

    /**
     * @test
     */
    public function setNameEnForStringSetsNameEn()
    {
        $this->subject->setNameEn('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'nameEn',
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
