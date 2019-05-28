<?php

namespace ThomasWoehlke\T3gtd\Command;

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
 * Class RemoveUnusedFilesController
 *
 * @package ThomasWoehlke\T3gtd\Command
 */
class RemoveUnusedFilesController extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    /**
     * This is the main method that is called when a task is executed
     * It MUST be implemented by all classes inheriting from this one
     * Note that there is no error handling, errors and failures are expected
     * to be handled and logged by the client implementations.
     * Should return TRUE on successful execution, FALSE on error.
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute() {
        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Core\Log\LogManager'
        )->getLogger(__CLASS__);
        $logger->info('execute Start');
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Object\\ObjectManager'
        );
        /** @var \ThomasWoehlke\T3gtd\Domain\Repository\TaskRepository $taskRepository */
        $taskRepository = $objectManager->get('ThomasWoehlke\\T3gtd\\Domain\\Repository\\TaskRepository');
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
        $configurationManager = $objectManager->get(
            'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface'
        );
        $settings = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $storagePid = $settings['plugin.']['tx_t3gtd_frontendgtd.']['persistence.']['storagePid'];
        $logger->info('storagePid: '.$storagePid);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $querySettings */
        $querySettings = $objectManager->get(
            'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings'
        );
        $querySettings->setStoragePageIds(array($storagePid));
        $taskRepository->setDefaultQuerySettings($querySettings);
        $tasks = $taskRepository->getTasksWithFiles();
        $logger->info('execute found: '.count($tasks));
        $filenamesFromDatabase = array();
        foreach ($tasks as $task){
            $logger->info('task: '.$task->getTitle());
            $taskFiles = $task->getFiles();
            if($taskFiles !== null){
                foreach ($taskFiles as $file) {
                    $filenamesFromDatabase[] = $file['basename'];
                }
            }
        }
        foreach ($filenamesFromDatabase as $filenameFromDatabase){
            $logger->info('filenameFromDatabase: '.$filenameFromDatabase);
        }
        $filePath = PATH_site . 'uploads/tx_t3gtd/';
        $logger->info($filePath);
        $filesOnDisk = \TYPO3\CMS\Core\Utility\GeneralUtility::getFilesInDir($filePath);
        $filesOnDiskToBeDeleted = array();
        foreach ($filesOnDisk as $fileOnDisk){
            $logger->info('fileOnDisk: '.$fileOnDisk);
            if(! in_array($fileOnDisk,$filenamesFromDatabase)){
                $filesOnDiskToBeDeleted[] = $fileOnDisk;
            }
        }
        foreach ($filesOnDiskToBeDeleted as $fileOnDiskToBeDeleted){
            $filepath = $filePath.$fileOnDiskToBeDeleted;
            if(unlink($filepath)){
                $logger->info('deleted File: '.$filepath);
            }
        }
        $logger->info('execute DONE');
        return TRUE;
    }

}
