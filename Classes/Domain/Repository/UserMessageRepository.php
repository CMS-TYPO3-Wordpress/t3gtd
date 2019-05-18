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
 * The repository for UserMessages
 */
class UserMessageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    protected $defaultOrderings = array(
        'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $thisUser
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $otherUser
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllBetweenTwoUsers(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $thisUser,
        \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $otherUser)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr(
                $query->logicalAnd(
                    $query->equals('sender', $thisUser),
                    $query->equals('receiver', $otherUser)
                ),
                $query->logicalAnd(
                    $query->equals('sender', $otherUser),
                    $query->equals('receiver', $thisUser)
                )
            )
        );
        return $query->execute();
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $userAccount
     * @return int
     */
    public function getNewMessagesFor(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $sender,
        \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $receiver)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('sender', $sender),
                $query->equals('receiver', $receiver),
                $query->equals('readByReceiver', false)
            )
        );
        return $query->count();
    }
}
