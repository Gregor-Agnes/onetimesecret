<?php
defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Zwo3.Onetimesecret',
    'Onetimesecret',
    [
        'Onetimesecret' => 'showForm, createLink, showSecret, success, unsuccess',
    ],
    // non-cacheable actions
    [
        'Onetimesecret' => 'showForm, createLink, showSecret, success, unsuccess',
    ]
);


$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_onetimesecret_onetimesecret[token]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_onetimesecret_onetimesecret[uid]';
