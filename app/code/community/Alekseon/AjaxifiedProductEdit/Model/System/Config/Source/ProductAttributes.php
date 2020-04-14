<?php /** * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Model_System_Config_Source_ProductAttributes
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();
                
            $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entityTypeId);

            foreach ($collection as $attribute) {
                if ($attribute->getFrontendLabel()) {
                    $this->_options[] = array(
                        'value' => $attribute->getAttributeCode(),
                        'label' => $attribute->getFrontendLabel(),
                    );
                }
            }

            usort($this->_options, array($this,'_compare'));
        }

        return $this->_options;
    }
    
    protected function _compare($option1, $option2)
    {
        return strcmp($option1['label'], $option2['label']);
    }
}
