<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3gtd/Resources/Private/Language/locallang_db.xlf:tx_t3gtd_domain_model_project',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,description,context,user_account,parent,children,',
        'iconfile' => 'EXT:t3gtd/Resources/Public/Icons/tx_t3gtd_domain_model_project.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, description, context, user_account, parent, children',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, description, context, user_account, parent, children, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages'
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_t3gtd_domain_model_project',
                'foreign_table_where' => 'AND tx_t3gtd_domain_model_project.pid=###CURRENT_PID### AND tx_t3gtd_domain_model_project.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3gtd/Resources/Private/Language/locallang_db.xlf:tx_t3gtd_domain_model_project.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],

        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3gtd/Resources/Private/Language/locallang_db.xlf:tx_t3gtd_domain_model_project.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]

        ],
        'context' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3gtd/Resources/Private/Language/locallang_db.xlf:tx_t3gtd_domain_model_project.context',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_t3gtd_domain_model_context',
                'minitems' => 0,
                'maxitems' => 1,
            ],

        ],
        'user_account' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:fe_users.username',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'minitems' => 0,
                'maxitems' => 1,
            ],

        ],
        'parent' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3gtd/Resources/Private/Language/locallang_db.xlf:tx_t3gtd_domain_model_project.parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'treeConfig' => [
                    'parentField' => 'parent',
                    'childrenField' => 'children',
                    'rootUid' => 0,
                ],
                'foreign_table' => 'tx_t3gtd_domain_model_project',
                'minitems' => 0,
                'maxitems' => 1,
            ],

        ],
        'children' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3gtd/Resources/Private/Language/locallang_db.xlf:tx_t3gtd_domain_model_project.children',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_t3gtd_domain_model_project',
                'foreign_field' => 'project3',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
        'project3' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
