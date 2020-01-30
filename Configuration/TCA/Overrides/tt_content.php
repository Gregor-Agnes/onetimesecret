<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Zwo3.Onetimesecret',
    'Onetimesecret',
    'Create a onetimelink to a secret string',
    'EXT:onetimesecret/Resources/Public/Gfx/icon.svg'
);



$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['onetimesecret_onetimesecret'] = 'layout,recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['onetimesecret_onetimesecret'] = 'pi_flexform';


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'onetimesecret_onetimesecret',
    'FILE:EXT:onetimesecret/Configuration/FlexForm/flexform_onetimesecret.xml'
);
