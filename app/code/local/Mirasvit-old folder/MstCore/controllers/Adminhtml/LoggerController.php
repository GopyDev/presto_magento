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


class Mirasvit_MstCore_Adminhtml_LoggerController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_addContent($this->getLayout()->createBlock('mstcore/adminhtml_logger'))
            ->renderLayout();
    }
}