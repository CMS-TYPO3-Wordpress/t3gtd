<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "gtd_site"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Todo-List in the Style of Getting Things Done',
    'description' => 'Frontend Users can use this Extension as Todo List for Timemanagement or Projectmanangement with the Getting Things Done Scheme introduced by David Allen.',
    'category' => 'plugin',
    'author' => 'Thomas Woehlke',
    'author_email' => 'thomas.woehlke@rub.de',
    'author_company' => 'Ruhr University Bochum',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => '1',
    'createDirs' => 'uploads/tx_t3gtd',
    'clearCacheOnLoad' => 1,
    'lockType' => '',
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'felogin' => '8.7.25-9.5.99',
            'scheduler' => '8.7.25-9.5.99',
            'extbase' => '8.7.25-9.5.99',
            'fluid' => '8.7.25-9.5.99',
            'typo3' => '8.7.25-9.5.99',
            'php' => '7.2.0-7.99.99',
        ],
        'conflicts' => [],
        'suggests' => [
            't3sbootstrap' => '4.1.18-4.3.3',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'ThomasWoehlke\\T3gtd\\' => 'Classes',
        ],
    ],
];
