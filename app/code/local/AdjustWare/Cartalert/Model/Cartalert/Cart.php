<?php
/**
 * Abandoned Carts Alerts Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Cartalert
 * @version      3.3.4
 * @license:     W22sKZAc65sLiShWmPFxsroCMZvSx8DyIvGLqZPs4w
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Cartalert_Model_Cartalert_Cart extends AdjustWare_Cartalert_Model_Cartalert
{
    const CARTALERT_INSTANCE_TYPE = 'cart';

    public function getResource()
    {
        return Mage::getResourceSingleton('adjcartalert/cartalert_cart');
    }
}