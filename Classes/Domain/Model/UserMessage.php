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

use TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator;
use TYPO3\CMS\Extbase\Validation\Validator\TextValidator;

/**
 * UserMessage
 */
class UserMessage extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * messageText
     *
     * @var string
     * @validate NotEmpty,Text
     */
    protected $messageText = '';

    /**
     * readByReceiver
     *
     * @var bool
     */
    protected $readByReceiver = false;

    /**
     * sender
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $sender = null;

    /**
     * receiver
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $receiver = null;

    /**
     * Returns the messageText
     *
     * @return string $messageText
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * Sets the messageText
     *
     * @param string $messageText
     * @return void
     */
    public function setMessageText($messageText)
    {
        $this->messageText = $messageText;
    }

    /**
     * Returns the readByReceiver
     *
     * @return bool $readByReceiver
     */
    public function getReadByReceiver()
    {
        return $this->readByReceiver;
    }

    /**
     * Sets the readByReceiver
     *
     * @param bool $readByReceiver
     * @return void
     */
    public function setReadByReceiver($readByReceiver)
    {
        $this->readByReceiver = $readByReceiver;
    }

    /**
     * Returns the boolean state of readByReceiver
     *
     * @return bool
     */
    public function isReadByReceiver()
    {
        return $this->readByReceiver;
    }

    /**
     * Returns the sender
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Sets the sender
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $sender
     * @return void
     */
    public function setSender(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Returns the receiver
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Sets the receiver
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $receiver
     * @return void
     */
    public function setReceiver(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $receiver)
    {
        $this->receiver = $receiver;
    }
}
