<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Search Spell-Correction
 * @version   1.2.5
 * @build     338
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



/**
 * @category Mirasvit
 */
class Mirasvit_Misspell_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            'general_misspell' => 'Enable search spell-correction feature.
                When search return zero result, extension will try fix possible typos in search phase',
            'general_reindex' => 'Run spell-correction reindex.
                During this process, extension select and save all words from your database to own index',

            'general_fallback' => 'Enable fallback search feature.
                When search return zero result and spell-correction can\'t find typos, extension will try find search phase (without some words) with results',
        ),
    );
}
