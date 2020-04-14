<?php /** * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_System_Config_Form_Field_ManageGridViewsButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('*/ajaxifiedProductEdit_manageGridViews');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('alekseon_ajaxifiedProductEdit')->__('Manage Grid Views'))
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}