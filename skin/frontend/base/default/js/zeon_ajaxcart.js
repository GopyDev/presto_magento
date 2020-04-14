/**
* zeonsolutions inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/shop/license-community.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * This package designed for Magento Community edition
 * =================================================================
 * zeonsolutions does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * zeonsolutions does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   design
 * @package    base_default
 * @version    0.0.1
 * @copyright  @copyright Copyright (c) 2013 zeonsolutions.Inc. (http://www.zeonsolutions.com)
 * @license    http://www.zeonsolutions.com/shop/license-community.txt
 */

var AjaxCart = Class.create();
AjaxCart.prototype = {
    initialize: function () {
        this.container = $$('body')[0];
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },

    resetLoadWaiting: function(transport){
        this.setLoadWaiting(false);
    },

    setLoadWaiting: function(enable) {
        if (enable) {
            var container = this.container;
            container.setStyle({
                opacity:.5
            });
            Element.show('loading-mask');
            $('additional-content').update('');
        }
        else {
            var container = this.container;
            container.setStyle({
                opacity:1
            });
            Element.hide('loading-mask');
        }
    },

    toggleSelectsUnderBlock: function(block, flag){
        if(Prototype.Browser.IE){
            var selects = document.getElementsByTagName("select");
            for(var i=0; i<selects.length; i++){
                /**
                 * @todo: need check intersection
                 */
                if(flag){
                    if(selects[i].needShowOnSuccess){
                        selects[i].needShowOnSuccess = false;
                        // Element.show(selects[i])
                        selects[i].style.visibility = '';
                    }
                }
                else{
                    if(Element.visible(selects[i])){
                        // Element.hide(selects[i]);
                        //                        selects[i].style.visibility = 'hidden';
                        selects[i].needShowOnSuccess = true;
                    }
                }
            }
        }
    },
    
    showButtons: function(loginRequired, isWishlist, isCompare) {
        if (loginRequired) {
            $('btn-login').show();
            $('btn-register').show();
        } else if (isWishlist) {
            $('btn-wishlist').show();
            $('btn-continue-shopping').show();
        } else if (isCompare) {
            $('btn-view-compare').show();
            $('btn-continue-shopping').show();
        } else {
            $('btn-cart-checkout').show();
            $('btn-continue-shopping').show();
        }
    },

    hideButtons: function() {
        $('btn-login').hide();
        $('btn-register').hide();
        $('btn-view-compare').hide();
        $('btn-wishlist').hide();
        $('btn-cart-checkout').hide();
        $('btn-continue-shopping').hide();
    },

    center: function(elem) {
        var scrollPosition = document.viewport.getScrollOffsets();
        var width = elem.clientWidth;
        var height = elem.clientHeight; 
        var H = (document.viewport.getHeight() - height)/2 + scrollPosition.top;
        var L = (document.viewport.getWidth() - width)/2 - scrollPosition.left;
        elem.setStyle({
            top: H+'px', 
            left: L+'px'
        });
    },

    openMessagePopup: function(timePeriod) {
        var height = this.container.getHeight();
        $('message-popup-window-mask').setStyle({
            'height':height+'px'
            });
        this.toggleSelectsUnderBlock($('message-popup-window-mask'), false);
        Element.show('message-popup-window-mask');
        var x = (window.innerWidth / 2) - ($('message-popup-window').offsetWidth / 2);
        var y = (window.innerHeight / 2) - ($('message-popup-window').offsetHeight / 2);
        Element.show('message-popup-window');
        this.center($('message-popup-window'));
        if(timePeriod) {
            setTimeout(this.closeMessagePopup.bind(this), timePeriod*1000);
        }
    },

    closeMessagePopup: function() {
        this.toggleSelectsUnderBlock($('message-popup-window-mask'), true);
        Element.hide('message-popup-window');
        Element.hide('message-popup-window-mask');
        $('message-content').update('');
        $('additional-content').update('');
        this.hideButtons();
    },

    initializeClasses: function() {
        mainNav("nav", {
            "show_delay":"100",
            "hide_delay":"100"
        });
    },

    extractScripts: function(strings) {
        var scripts = strings.extractScripts();
        scripts.each(function(script){
            try {
                eval(script.replace(/var /gi, ""));
            }
            catch(e){
                if(window.console) console.log(e.name);
            }
        });
    },

    addByUrl: function(postUrl){
        var isCompare = false;
        var isWishlist = false;
        if (postUrl.include('wishlist')) {
            isWishlist = true;
            postUrl = postUrl.gsub('wishlist/index/add', 'ajaxcart/wishlist/add');
        } else if (postUrl.include('product_compare')) {
            isCompare = true;
            postUrl = postUrl.gsub('catalog/product_compare/add', 'ajaxcart/compare/add');
        }

        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            postUrl,
            {
                method: 'post',
                onComplete: this.onComplete,
                onFailure: function(response){
                    alert('An error occurred while processing your request');
                    this.onComplete;
                },
                onSuccess: function(response){
                    if (response && response.responseText){
                        if (typeof(response.responseText) == 'string') {
                            eval('result = ' + response.responseText);
                        }
                        if (result.message) {
                            $('message-content').update(result.message);
                        }
                        //Update the header
                        if (typeof(result.header) != 'undefined') {
                            var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                            var end = result.header.length - '</div>'.length;
                            var header = result.header.substring(begin,end);
                            $$('.header-container')[0].innerHTML = header;
                            this.extractScripts(header);
                            this.initializeClasses();
                        }
                        //Update the sidebar block
                        if (typeof(result.sidebar) != 'undefined' && $(result.block_id) != null) {
                            $(result.block_id).innerHTML = result.sidebar;
                            this.extractScripts(result.sidebar);
                            this.initializeClasses();
                        }
                        var loginRequired = false;
                        if (typeof(result.loginRequired) != 'undefined' && result.loginRequired) {
                            loginRequired = true;
                        }
                        this.showButtons(loginRequired, isWishlist, isCompare);
                        this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                    }
                }.bind(this)
            }
            );
    },

    remove: function(postUrl){
        postUrl = postUrl.gsub('checkout/cart/delete', 'ajaxcart/cart/delete');
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            postUrl,
            {
                method: 'post',
                onComplete: this.onComplete,
                onFailure: function(response){
                    alert('An error occurred while processing your request');
                    this.onComplete;
                },
                onSuccess: function(response){
                    if (response && response.responseText){
                        if (typeof(response.responseText) == 'string') {
                            eval('result = ' + response.responseText);
                        }
                        if (result.message) {
                            $('message-content').update(result.message);
                        }
                        //Update the header
                        if (typeof(result.header) != 'undefined') {
                            var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                            var end = result.header.length - '</div>'.length;
                            var header = result.header.substring(begin,end);
                            $$('.header-container')[0].innerHTML = header;
                            this.extractScripts(header);
                            this.initializeClasses();
                        }
                        //Update the content
                        if (typeof(result.content) != 'undefined') {
                            if (result.content.indexOf('<div class="cart">') == -1) {
                                var content = result.content;
                            } else {
                                var begin = result.content.indexOf('<div class="cart">') + '<div class="cart">'.length;
                                var end = result.content.length - '</div>'.length;
                                var content = result.content.substring(begin,end);
                            }
                            if ($$('.cart')[0]) {
								$$('.cart')[0].innerHTML = content;
								this.extractScripts(content);
                            }
                        }
                        this.hideButtons();
                        this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                    }
                }.bind(this)
            }
            );
    },

    viewProductOptions: function(postUrl, id){
        this.closeMessagePopup();
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            BASE_URL+postUrl,
            {
                method: 'get',
                parameters: {
                    id:id
                },
                evalJS: true,
                onComplete: this.onComplete,
                onFailure: function(response){
                    alert('An error occurred while processing your request');
                    this.onComplete;
                },
                onSuccess: function(response){
                    try {
                        if (response && response.responseText){
                            //                        response.responseText = response.responseText.replace(/(\r\n|\n|\r)/gm,"");
                            $('message-content').innerHTML = response.responseText;
                            this.extractScripts(response.responseText);
                            this.hideButtons();
                            this.openMessagePopup(0);
                        }
                    }
                    catch (error){
                        alert(error);
                    }
                }.bind(this)
            }
            );
    },
    

    updateCart: function(postUrl){
        postUrl = postUrl.gsub('checkout/cart/updatePost', 'ajaxcart/cart/updatePost');
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            postUrl,
            {
                method: 'post',
                parameters: Form.serialize($('product_updatecart_form')),
                onComplete: this.onComplete,
                onFailure: function(response){
                    alert('An error occurred while processing your request');
                    this.onComplete;
                },
                onSuccess: function(response){
                    if (response && response.responseText){
                        if (typeof(response.responseText) == 'string') {
                            eval('result = ' + response.responseText);
                        }
                        if (result.message) {
                            $('message-content').update(result.message);
                        }
                        //Update the header
                        if (typeof(result.header) != 'undefined') {
                            var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                            var end = result.header.length - '</div>'.length;
                            var header = result.header.substring(begin,end);
                            $$('.header-container')[0].innerHTML = header;
                            this.extractScripts(header);
                            this.initializeClasses();
                        }
                        //Update the content
                        if (typeof(result.content) != 'undefined') {
                            if (result.content.indexOf('<div class="cart">') == -1) {
                                var content = result.content;
                            } else {
                                var begin = result.content.indexOf('<div class="cart">') + '<div class="cart">'.length;
                                var end = result.content.length - '</div>'.length;
                                var content = result.content.substring(begin,end);
                            }
                            $$('.cart')[0].innerHTML = content;
                            this.extractScripts(content);
                        }
                        this.hideButtons();
                        this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                    }
                }.bind(this)
            }
            );
    },



    addUpdate: function(postUrl){
        var isWishlist = false;
        if (postUrl.include('wishlist')) {
            isWishlist = true;
        }
        var productForm = new VarienForm('product_addtocart_form', true);
        if (!productForm.validator.validate() && !isWishlist) {
            return;
        }
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            BASE_URL+postUrl,
            {
                method: 'post',
                parameters: Form.serialize($('product_addtocart_form')),
                evalJS: true,
                onComplete: this.onComplete,
                onFailure: function(response){
                    alert('An error occurred while processing your request');
                    this.onComplete;
                },
                onSuccess: function(response){
                    if (response && response.responseText){
                        if (typeof(response.responseText) == 'string') {
                            eval('result = ' + response.responseText);
                        }
                        if (typeof(result.item_id) != 'undefined') {
                            $('item_id').value = result.item_id;
                        }
                        if (typeof(result.wishlist_item_id) != 'undefined') {
                            $('wishlist_item_id').value = result.wishlist_item_id;
                        }
                        if (result.message) {
                            $('message-content').update(result.message);
                        }
                        //Update the header
                        if (typeof(result.header) != 'undefined') {
                            var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                            var end = result.header.length - '</div>'.length;
                            var header = result.header.substring(begin,end);
                            $$('.header-container')[0].innerHTML = header;
                            this.extractScripts(header);
                            this.initializeClasses();
                        }
                        //Update the sidebar block
                        if (typeof(result.sidebar) != 'undefined' && $(result.block_id) != null) {
                            $(result.block_id).innerHTML = result.sidebar;
                            this.extractScripts(result.sidebar);
                            this.initializeClasses();
                        }
                        //Display additional product block
                        if (typeof(result.additional) != 'undefined' && result.additional) {
                            $('additional-content').innerHTML = result.additional;
                        }
                        var loginRequired = false;
                        if (typeof(result.loginRequired) != 'undefined' && result.loginRequired) {
                            loginRequired = true;
                        }
                        this.showButtons(loginRequired, isWishlist, false);
                        this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                    }
                }.bind(this)
            }
            );
    },
    
    removeWishListItem: function(postUrl, enabled){
        var conf = confirm('Are you sure you want to remove this product from your wishlist?');
        if (enabled && conf) {
            postUrl = postUrl.gsub('wishlist/index/remove', 'ajaxcart/wishlist/remove');
            this.setLoadWaiting(true);
            var request = new Ajax.Request(
                postUrl,
                {
                    method: 'post',
                    onComplete: this.onComplete,
                    onFailure: function(response){
                        alert('An error occurred while processing your request');
                        this.onComplete;
                    },
                    onSuccess: function(response){
                        if (response && response.responseText){
                            if (typeof(response.responseText) == 'string') {
                                eval('result = ' + response.responseText);
                            }
                            if (result.message) {
                                $('message-content').update(result.message);
                            }
                            //Update the header
                            if (typeof(result.header) != 'undefined') {
                                var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                                var end = result.header.length - '</div>'.length;
                                var header = result.header.substring(begin,end);
                                $$('.header-container')[0].innerHTML = header;
                                this.extractScripts(header);
                                this.initializeClasses();
                            }
                            //Update the content
                            if (typeof(result.content) != 'undefined') {
                                if (result.content.indexOf('<div class="my-wishlist">') == -1) {
                                    var content = result.content;
                                } else {
                                    var begin = result.content.indexOf('<div class="my-wishlist">') + '<div class="my-wishlist">'.length;
                                    var end = result.content.length - '</div>'.length;
                                    var content = result.content.substring(begin,end);
                                }
                                $$('.my-wishlist')[0].innerHTML = content;
                                this.extractScripts(content);
                            }
                            this.hideButtons();
                            this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                        }
                    }.bind(this)
                }
                );
        } else if (conf) {
            setLocation(postUrl);
        } else {
            return false;
        }
    },
    
    updateWishList: function(postUrl, enabled){
        if (enabled) {
            postUrl = postUrl.gsub('wishlist/index/update', 'ajaxcart/wishlist/update');
            this.setLoadWaiting(true);
            var request = new Ajax.Request(
                postUrl,
                {
                    method: 'post',
                    parameters: Form.serialize($('wishlist-view-form')),
                    onComplete: this.onComplete,
                    onFailure: function(response){
                        alert('An error occurred while processing your request');
                        this.onComplete;
                    },
                    onSuccess: function(response){
                        if (response && response.responseText){
                            if (typeof(response.responseText) == 'string') {
                                eval('result = ' + response.responseText);
                            }
                            if (result.message) {
                                $('message-content').update(result.message);
                            }
                            //Update the header
                            if (typeof(result.header) != 'undefined') {
                                var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                                var end = result.header.length - '</div>'.length;
                                var header = result.header.substring(begin,end);
                                $$('.header-container')[0].innerHTML = header;
                                this.extractScripts(header);
                                this.initializeClasses();
                            }
                            //Update the content
                            if (typeof(result.content) != 'undefined') {
                                if (result.content.indexOf('<div class="my-wishlist">') == -1) {
                                    var content = result.content;
                                } else {
                                    var begin = result.content.indexOf('<div class="my-wishlist">') + '<div class="my-wishlist">'.length;
                                    var end = result.content.length - '</div>'.length;
                                    var content = result.content.substring(begin,end);
                                }
                                $$('.my-wishlist')[0].innerHTML = content;
                                this.extractScripts(content);
                            }
                            this.hideButtons();
                            this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                        }
                    }.bind(this)
                }
                );
        } else {
            $('wishlist-view-form').submit();
        }
    },

    addWishListItemsToCart: function(postUrl){
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            postUrl,
            {
                method: 'post',
                evalJS: true,
                onComplete: this.onComplete,
                onFailure: function(response){
                    alert('An error occurred while processing your request');
                    this.onComplete;
                },
                onSuccess: function(response){
                    if (response && response.responseText){
                        if (typeof(response.responseText) == 'string') {
                            eval('result = ' + response.responseText);
                        }
                        if (result.message) {
                            $('message-content').update(result.message);
                        }
                        if (result.error) {
                            $('error-content').update(result.error);
                        }
                        //Update the header
                        if (typeof(result.header) != 'undefined') {
                            var begin = result.header.indexOf('<div class="header-container">') + '<div class="header-container">'.length;
                            var end = result.header.length - '</div>'.length;
                            var header = result.header.substring(begin,end);
                            $$('.header-container')[0].innerHTML = header;
                            this.extractScripts(header);
                            this.initializeClasses();
                        }
                        
                        //Update the content
                        if (typeof(result.content) != 'undefined' && AJAX_CART.isWishlistPage) {
                            var begin = result.content.indexOf('<div class="my-account">') + '<div class="my-account">'.length;
                            var end = result.content.length - '</div>'.length;
                            var content = result.content.substring(begin,end);
                            $$('.my-account')[0].innerHTML = content;
                            this.extractScripts(content);
                        }
                        //Update the sidebar block
                        if (typeof(result.sidebar) != 'undefined' && $(result.block_id) != null) {
                            $(result.block_id).innerHTML = result.sidebar;
                            this.extractScripts(result.sidebar);
                            this.initializeClasses();
                        }
                        var loginRequired = false;
                        if (typeof(result.loginRequired) != 'undefined' && result.loginRequired) {
                            loginRequired = true;
                        }
                        this.showButtons(loginRequired, false, false);
                        this.openMessagePopup(AJAX_CART.isAutoHidePopup);
                    }
                }.bind(this)
            }
            );
    }
};

function validateDownloadableCallback(elmId, result) {
    var container = $('downloadable-links-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}