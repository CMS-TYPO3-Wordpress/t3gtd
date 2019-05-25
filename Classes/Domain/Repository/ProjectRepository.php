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
 * The repository for Projects
 */
class ProjectRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $currentContext
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function getRootProjects(\ThomasWoehlke\T3gtd\Domain\Model\Context $currentContext)
    {
        $query = $this->createQuery();
        #$query->getQuerySettings()->useQueryCache(FALSE);
        $query->matching(
            $query->logicalAnd(
                $query->equals('context', $currentContext),
                $query->logicalOr(
                    $query->equals('parent', 0),
                    $query->equals('parent', null)
                )
            )
        );
        return $query->execute();
    }

    /**
     * @param $context
     * @return bool
     */
    public function hasProjectsForContext($context)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('context',$context)
        );
        $list = $query->execute();
        return $list->count() > 0;
    }
}
