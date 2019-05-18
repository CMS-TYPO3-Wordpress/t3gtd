<?php
namespace ThomasWoehlke\T3gtd\Domain\Model;

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

/**
 * UserConfig
 */
class UserConfig extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * defaultContext
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \ThomasWoehlke\T3gtd\Domain\Model\Context
     */
    protected $defaultContext = null;

    /**
     * userAccount
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $userAccount = null;

    /**
     * Returns the defaultContext
     *
     * @return \ThomasWoehlke\T3gtd\Domain\Model\Context $defaultContext
     */
    public function getDefaultContext()
    {
        return $this->defaultContext;
    }

    /**
     * Sets the defaultContext
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $defaultContext
     * @return void
     */
    public function setDefaultContext(\ThomasWoehlke\T3gtd\Domain\Model\Context $defaultContext)
    {
        $this->defaultContext = $defaultContext;
    }

    /**
     * Returns the userAccount
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userAccount
     */
    public function getUserAccount()
    {
        return $this->userAccount;
    }

    /**
     * Sets the userAccount
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userAccount
     * @return void
     */
    public function setUserAccount(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userAccount)
    {
        $this->userAccount = $userAccount;
    }
}
