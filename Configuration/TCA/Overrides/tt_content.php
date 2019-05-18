<?php
defined('TYPO3_MODE') or die();
$extKey='t3gtd';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'ThomasWoehlke.' . $extKey,
    'Gtd',
    'GTD'
);
