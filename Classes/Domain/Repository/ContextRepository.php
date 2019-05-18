<?php
namespace ThomasWoehlke\T3gtd\Domain\Repository;

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
 * The repository for Contexts
 */
class ContextRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllByUserAccount(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userObject)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('userAccount', $userObject)
        );
        return $query->execute();
    }
}
