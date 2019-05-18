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
use TYPO3\CMS\Extbase\Validation\Validator\NumberRangeValidator;
use TYPO3\CMS\Extbase\Validation\Validator\DateTimeValidator;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Task
 */
class Task extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var array|int
     */
    public static $TASK_STATES = array(
        'inbox' => 0,
        'today' => 1,
        'next' => 2,
        'waiting' => 3,
        'scheduled' => 4,
        'someday' => 5,
        'completed' => 6 ,
        'trash' => 7
    );

    /**
     * title
     *
     * @var string
     * @validate NotEmpty
     * @validate Text
     */
    protected $title = '';

    /**
     * text
     *
     * @var string
     * @validate NotEmpty
     * @validate Text
     */
    protected $text = '';

    /**
     * focus
     *
     * @var bool
     */
    protected $focus = false;

    /**
     * taskState
     *
     * @var int
     * @validate NumberRange(minimum = 0, maximum = 7)
     */
    protected $taskState = 0;

    /**
     * lastTaskState
     *
     * @var int
     * @validate NumberRange(minimum = 0, maximum = 7)
     */
    protected $lastTaskState = 0;

    /**
     * taskEnergy
     *
     * @var int
     * @validate NumberRange(minimum = 0, maximum = 3)
     */
    protected $taskEnergy = 0;

    /**
     * taskTime
     *
     * @var int
     * @validate NumberRange(minimum = 0, maximum = 12)
     */
    protected $taskTime = 0;

    /**
     * dueDate
     *
     * @var \DateTime
     * @validate DateTime
     */
    protected $dueDate = null;

    /**
     * orderIdProject
     *
     * @var int
     */
    protected $orderIdProject = 0;

    /**
     * orderIdTaskState
     *
     * @var int
     */
    protected $orderIdTaskState = 0;

    /**
     * project
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \ThomasWoehlke\T3gtd\Domain\Model\Project
     */
    protected $project = null;

    /**
     * context
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \ThomasWoehlke\T3gtd\Domain\Model\Context
     */
    protected $context = null;

    /**
     * userAccount
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $userAccount = null;

    /**
     * files
     *
     * @var string
     */
    protected $files = null;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns the focus
     *
     * @return bool $focus
     */
    public function getFocus()
    {
        return $this->focus;
    }

    /**
     * Sets the focus
     *
     * @param bool $focus
     * @return void
     */
    public function setFocus($focus)
    {
        $this->focus = $focus;
    }

    /**
     * Returns the boolean state of focus
     *
     * @return bool
     */
    public function isFocus()
    {
        return $this->focus;
    }

    /**
     * Returns the taskState
     *
     * @return int $taskState
     */
    public function getTaskState()
    {
        return $this->taskState;
    }


    public function setToLastTaskState()
    {
        $helper = $this->taskState;
        $this->taskState = $this->lastTaskState;
        $this->lastTaskState = $helper;
    }

    /**
     * Sets the taskState
     *
     * @param int $taskState
     * @return void
     */
    public function changeTaskState($taskState)
    {
        $this->lastTaskState = $this->taskState;
        $this->taskState = $taskState;
    }

    /**
     * Sets the taskState
     *
     * @param int $taskState
     * @return void
     */
    public function setTaskState($taskState)
    {
        $this->taskState = $taskState;
    }

    /**
     * Returns the lastTaskState
     *
     * @return int $lastTaskState
     */
    public function getLastTaskState()
    {
        return $this->lastTaskState;
    }

    /**
     * Sets the lastTaskState
     *
     * @param int $lastTaskState
     * @return void
     */
    public function setLastTaskState($lastTaskState)
    {
        $this->lastTaskState = $lastTaskState;
    }

    /**
     * Returns the taskEnergy
     *
     * @return int $taskEnergy
     */
    public function getTaskEnergy()
    {
        return $this->taskEnergy;
    }

    /**
     * Sets the taskEnergy
     *
     * @param int $taskEnergy
     * @return void
     */
    public function setTaskEnergy($taskEnergy)
    {
        $this->taskEnergy = $taskEnergy;
    }

    /**
     * Returns the taskTime
     *
     * @return int $taskTime
     */
    public function getTaskTime()
    {
        return $this->taskTime;
    }

    /**
     * Sets the taskTime
     *
     * @param int $taskTime
     * @return void
     */
    public function setTaskTime($taskTime)
    {
        $this->taskTime = $taskTime;
    }

    /**
     * Returns the dueDate
     *
     * @return \DateTime $dueDate
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Sets the dueDate
     *
     * @param \DateTime $dueDate
     * @return void
     */
    public function setDueDate(\DateTime $dueDate = NULL)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * Returns the orderIdProject
     *
     * @return int $orderIdProject
     */
    public function getOrderIdProject()
    {
        return $this->orderIdProject;
    }

    /**
     * Sets the orderIdProject
     *
     * @param int $orderIdProject
     * @return void
     */
    public function setOrderIdProject($orderIdProject)
    {
        $this->orderIdProject = $orderIdProject;
    }

    /**
     * Returns the orderIdTaskState
     *
     * @return int $orderIdTaskState
     */
    public function getOrderIdTaskState()
    {
        return $this->orderIdTaskState;
    }

    /**
     * Sets the orderIdTaskState
     *
     * @param int $orderIdTaskState
     * @return void
     */
    public function setOrderIdTaskState($orderIdTaskState)
    {
        $this->orderIdTaskState = $orderIdTaskState;
    }

    /**
     * Returns the project
     *
     * @return \ThomasWoehlke\T3gtd\Domain\Model\Project $project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Sets the project
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Project $project
     * @return void
     */
    public function setProject(\ThomasWoehlke\T3gtd\Domain\Model\Project $project=null)
    {
        $this->project = $project;
    }

    /**
     * Returns the context
     *
     * @return \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the context
     *
     * @param \ThomasWoehlke\T3gtd\Domain\Model\Context $context
     * @return void
     */
    public function setContext(\ThomasWoehlke\T3gtd\Domain\Model\Context $context)
    {
        $this->context = $context;
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

    /**
     * Returns the files
     *
     * @return string $files
     */
    public function getFiles() {
        if(!empty($this->files)){
            $returnFile = array();
            $filesArray = explode(',', $this->files);
            if(is_array($filesArray)){
                foreach($filesArray as $item){
                    $file = pathinfo(GeneralUtility::getFileAbsFileName('uploads/tx_t3gtd/'.$item));
                    if(is_file($file['dirname'] . '/' . $file['basename'])){
                        $bytes = filesize($file['dirname'] . '/' . $file['basename']);
                        if ($bytes >= 1073741824) {
                            $bytes = number_format($bytes / 1073741824, 2) . 'GB';
                        } elseif ($bytes >= 1048576){
                            $bytes = number_format($bytes / 1048576, 2) . 'MB';
                        } elseif ($bytes >= 1024){
                            $bytes = number_format($bytes / 1024, 2) . 'KB';
                        } elseif ($bytes > 1){
                            $bytes = $bytes . 'bytes';
                        } elseif ($bytes == 1){
                            $bytes = $bytes . 'byte';
                        } else {
                            $bytes = '0 bytes';
                        }
                        $returnFile[] = array_merge($file, array('filesize'=> $bytes));
                    }
                }
            } else {
                $file = pathinfo(GeneralUtility::getFileAbsFileName('uploads/tx_t3gtd/'. $this->files));
                $bytes = filesize($file['dirname'] . '/' . $file['basename']);
                if ($bytes >= 1073741824){
                    $bytes = number_format($bytes / 1073741824, 2) . 'GB';
                } elseif ($bytes >= 1048576){
                    $bytes = number_format($bytes / 1048576, 2) . 'MB';
                } elseif ($bytes >= 1024){
                    $bytes = number_format($bytes / 1024, 2) . 'KB';
                } elseif ($bytes > 1){
                    $bytes = $bytes . 'bytes';
                } elseif ($bytes == 1){
                    $bytes = $bytes . 'byte';
                } else{
                    $bytes = '0 bytes';
                }
                $returnFile[] = array_merge($file, array('filesize'=> $bytes));
            }
            return $returnFile;
        }
    }

    /**
     * Sets the files
     *
     * @param string $files
     * @return void
     */
    public function setFiles($files) {
        if(is_array($files)){
            $fileString = '';
            foreach($files as $item){
                $fileString .= $item . ',';
            }
            $this->files = substr($fileString,0,-1);
        } else {
            $this->files = $files;
        }
    }
}
