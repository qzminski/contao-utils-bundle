<?php

/**
 * JS
 */
array_insert(
    $GLOBALS['TL_JAVASCRIPT'],
    1,
    [
        'contao-utils-bundle'        => 'bundles/heimrichhannotcontaoutils/js/contao-utils-bundle.js|static'
    ]
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_cfg_tag'] = 'HeimrichHannot\UtilsBundle\Model\CfgTagModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags']['huh.utils.listener.insert_tags'] = ['huh.utils.listener.insert_tags', 'onReplaceInsertTags'];
$GLOBALS['TL_HOOKS']['initializeSystem']['huh.utils.template']              = ['huh.utils.template', 'getAllTemplates'];