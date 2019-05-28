<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    "t3gtd",
    'Configuration/TypoScript',
    'GTD'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_t3gtd_domain_model_context',
    'EXT:t3gtd/Resources/Private/Language/locallang_csh_tx_t3gtd_domain_model_context.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_t3gtd_domain_model_context'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_t3gtd_domain_model_project',
    'EXT:t3gtd/Resources/Private/Language/locallang_csh_tx_t3gtd_domain_model_project.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_t3gtd_domain_model_project'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_t3gtd_domain_model_task',
    'EXT:t3gtd/Resources/Private/Language/locallang_csh_tx_t3gtd_domain_model_task.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3gtd_domain_model_task');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_t3gtd_domain_model_userconfig',
    'EXT:t3gtd/Resources/Private/Language/locallang_csh_tx_t3gtd_domain_model_userconfig.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_t3gtd_domain_model_userconfig'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_t3gtd_domain_model_usermessage',
    'EXT:t3gtd/Resources/Private/Language/locallang_csh_tx_t3gtd_domain_model_usermessage.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_t3gtd_domain_model_usermessage'
);
