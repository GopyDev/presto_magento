<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @version    3.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Ajaxcartpro_Model_Renderer_Skiplinks extends Varien_Object implements AW_Ajaxcartpro_Model_Renderer_Interface
{
    const BLOCK_NAME = 'minicart_head';

    public function renderFromLayout($layout)
    {
        $block = $layout->getBlock(self::BLOCK_NAME);
        if (!$block) {
            return null;
        }

        /*
         * Attempt to save the cart before rendering the skiplinks car contents to avoid
         * the AFPTC problem not showing the correct price for bonus item on RWD theme.
         * See:
         * AJAXCARTPRO-436 - Two products added in the cart if "Buy X get Y free" rule configured
         * for reverence.
         */
        $cart = Mage::getSingleton('checkout/cart');
        $cart->save();

        return $block->toHtml();
    }
}
