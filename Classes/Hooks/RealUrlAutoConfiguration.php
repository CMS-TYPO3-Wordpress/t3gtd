<?php

namespace GeorgRinger\News\Hooks;

/**
 * This file is part of the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * AutoConfiguration-Hook for RealURL
 *
 */
class RealUrlAutoConfiguration
{

    /**
     * Generates additional RealURL configuration and merges it with provided configuration
     *
     * @param       array $params Default configuration
     * @return      array Updated configuration
     */
    public function addNewsConfig($params)
    {
        return array_merge_recursive($params['config'], [
                'postVarSets' => [
                    '_DEFAULT' => [
                        'taskstate' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'TaskState' => 'TaskState'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'inbox' => 'inbox',
                                    'today' => 'today',
                                    'next' => 'next',
                                    'waiting' => 'waiting',
                                    'scheduled' => 'scheduled',
                                    'someday' => 'someday',
                                    'completed' => 'completed',
                                    'trash' => 'trash',
                                    'focus' => 'focus',
                                ]
                            ]
                        ],
                        'movetask2project' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'TaskMoveToProject' => 'TaskMoveToProject'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'moveTaskToProject' => 'moveTaskToProject',
                                    'moveTaskToRootProject' => 'moveTaskToRootProject'
                                ]
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[task]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_task',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[targetProject]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_project',
                                    'id_field' => 'uid',
                                    'alias_field' => 'name',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                        'movetask' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'TaskMove' => 'TaskMove'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'moveToInbox' => 'moveToInbox',
                                    'moveToToday' => 'moveToToday',
                                    'moveToNext' => 'moveToNext',
                                    'moveToWaiting' => 'moveToWaiting',
                                    'moveToSomeday' => 'moveToSomeday',
                                    'moveToCompleted' => 'moveToCompleted',
                                    'moveToTrash' => 'moveToTrash',
                                    'emptyTrash' => 'emptyTrash',
                                    'moveAllCompletedToTrash' => 'moveAllCompletedToTrash',
                                ],
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[task]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_task',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                        'moveproject2project' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'ProjectMove' => 'ProjectMove'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'moveProjectToProject' => 'moveProjectToProject',
                                    'moveProjectToRootProject' => 'moveProjectToRootProject',
                                ]
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[srcProject]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_project',
                                    'id_field' => 'uid',
                                    'alias_field' => 'name',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[targetProject]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_project',
                                    'id_field' => 'uid',
                                    'alias_field' => 'name',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                        'project' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'Project' => 'Project'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'show' => 'show',
                                    'edit' => 'edit',
                                    'update' => 'update',
                                    'delete' => 'delete',
                                    'list' => 'list',
                                    'new' => 'new',
                                    'create' => 'create',
                                    'createTestData' => 'createTestData',
                                    'moveTask' => 'moveTask'
                                ]
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[project]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_project',
                                    'id_field' => 'uid',
                                    'alias_field' => 'name',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[task]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_task',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                        'task' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'Task' => 'Task'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'edit' => 'edit',
                                    'update' => 'update',
                                    'new' => 'new',
                                    'create' => 'create',
                                    'uploadFiles' => 'uploadFiles',

                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[task]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_task',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                        'taskorder' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'TaskOrder' => 'TaskOrder'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'moveTaskOrder' => 'moveTaskOrder',
                                    'moveTaskOrderInsideProject' => 'moveTaskOrderInsideProject',

                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[srcTask]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_task',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[targetTask]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_task',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                        'context' => [
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[controller]',
                                'valueMap' => [
                                    'Context' => 'Context'
                                ],
                                'noMatch' => 'bypass',
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[action]',
                                'valueMap' => [
                                    'switchContext' => 'switchContext',
                                    'new' => 'new',
                                    'create' => 'create',
                                    'edit' => 'edit',
                                    'update' => 'update',
                                    'delete' => 'delete',
                                ]
                            ],
                            [
                                'GETvar' => 'tx_gtd_frontendgtd[context]',
                                'lookUpTable' => [
                                    'table' => 'tx_gtd_domain_model_context',
                                    'id_field' => 'uid',
                                    'alias_field' => 'name_en',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-'
                                    ]
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );
    }
}
