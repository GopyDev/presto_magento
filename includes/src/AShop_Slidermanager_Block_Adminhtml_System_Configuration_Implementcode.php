<?php
/**
 * AShop Slider
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the AccessshopThemes.com license that is
 * available through the world-wide-web at this URL:
 * http://www.accessshopThemes.com
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @copyright 	Copyright (c) 2015 Accessshop (http://www.accessshopThemes.com)
 * @license     http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Accessshop Block
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @author  	AccessShop Developer
 */
class AShop_Slidermanager_Block_Adminhtml_System_Configuration_Implementcode extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        return '
<div class="entry-edit-head collapseable"><a onclick="Fieldset.toggleCollapse(\'slidermanager_template\'); return false;" href="#" id="slidermanager_template-head" class="open">Implement Code</a></div>
<input id="slidermanager_template-state" type="hidden" value="1" name="config_state[slidermanager_template]">
<fieldset id="slidermanager_template" class="config collapseable" style="">
<h4 class="icon-head head-edit-form fieldset-legend">Code for AShop Slidermanager</h4>

<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>
                <li>'.Mage::helper('slidermanager')->__('Add code below to a template file, where slidermanager_id is active slider_id').'</li>				
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li>
		<code>
			$this->getLayout()->createBlock('.'slidermanager/slidermanager'.')->setTemplate('.'slidermanager/slidermanager.phtml'.')->setSlidermanagerId(slider_id)->toHtml();
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>
                <li>'.Mage::helper('slidermanager')->__('Add code below to a cms page, where slidermanager_id is active slider_id').'</li>				
            </ul>
        </li>
    </ul>
</div>
<br>
<ul>
	<li>
		<code>
			{{block type="slidermanager/slidermanager" name="slider" template="slidermanager/slidermanager.phtml" slidermanager_id="slider_id"}}
		</code>
	</li>
</ul>
<br>
<div id="messages">
    <ul class="messages">
        <li class="notice-msg">
            <ul>
                <li>'.Mage::helper('slidermanager')->__('Please copy and paste the code below on one of xml layout files where you want to show the banner. where slidermanager_id is active slider_id').'</li>				
            </ul>
        </li>
    </ul>
</div>

<ul>
	<li>
		<code>
		 &lt;block type="slidermanager/slidermanager" name="slider" template="slidermanager/slidermanager.phtml"&gt;<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;action method="addData"&gt;&lt;slidermanager_id&gt;slider_id&lt;/slidermanager_id&gt;&lt;/action&gt;<br>
		&lt;/block&gt;
		</code>	
	</li>
</ul>
</fieldset>';
    }
}

