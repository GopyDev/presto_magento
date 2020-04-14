/**
 * BelVG
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 
 * @category   Belvg
 * @package    Chameleon
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
	jQblvg(document).ready(function(){
		jQblvg('a.user-menu-button').click(function(){
			 jQblvg('.user-menu').slideToggle();
		});
		jQblvg('span.current').click(function(){
			 jQblvg('.header-dropdown').slideToggle();
		});

		jQblvg("table#product-attribute-specs-table tr:nth-child(even)").addClass("color");
		jQblvg("table#shopping-cart-table tr:nth-child(even)").addClass("color");

		jQblvg("table tr:nth-child(even)").addClass("color");
		
		jQblvg(".footer-usermenu div").hide();
		jQblvg(".footer-usermenu h3").click(function(){
			jQblvg(this).next().slideToggle();
			});

	}); 
 



