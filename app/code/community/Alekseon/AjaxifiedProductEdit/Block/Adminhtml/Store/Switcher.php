<?php /** * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
 class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('alekseon/ajaxifiedProductEdit/store/switcher.phtml');
    }

}