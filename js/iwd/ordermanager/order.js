;
window.hasOwnProperty = function (obj) {
    return (this[obj]) ? true : false;
};
if (!window.hasOwnProperty('IWD')) {
    IWD = {};
}

IWD.OrderManager = {
    orderId: null,

    init: function (orderId) {
        IWD.OrderManager.orderId = orderId;
    },

    ShowLoadingMask: function () {
        jQuery('#loading-mask').width(jQuery("html").width()).height(jQuery("html").height()).css('top', 0).css('left', -2).show();
    },

    HideLoadingMask: function () {
        jQuery('#loading-mask').hide();
    }
};

IWD.OrderManager.OrderedItems = {
    urlEditOrderedItemsForm: '',
    urlEditOrderedItems: '',
    urlAddOrderedItemsForm: '',
    urlAddOrderedItems: '',
    discountTax: 0,
    applyTaxAfterDiscount: 0,
    order: null,
    orderedItems: null,
    configureItems: {},

    initProductConfigure: function () {
    },

    init: function () {
        jQuery("#ordered_items_edit").on("click", function (event) {
            IWD.OrderManager.OrderedItems.editOrderedItemsForm(event);
        });
    },

    /**** edit ordered items ****/
    editOrderedItemsForm: function (event) {
        event.preventDefault();

        IWD.OrderManager.ShowLoadingMask();

        jQuery.ajax({url: IWD.OrderManager.OrderedItems.urlEditOrderedItemsForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    jQuery('#ordered_items_table').hide();
                    jQuery('#ordered_items_edit_form').remove();
                    jQuery('#add_ordered_items_form').remove();
                    jQuery("#ordered_items_box").append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderedItemsSubmit: function () {
        var orderedItemsFormValidation = new varienForm('ordered_items_form');
        if (!orderedItemsFormValidation.validator.validate())
            return;

        /* if all items checked */
        if (jQuery('.ordered_item_remove input:checkbox').size() == jQuery('.ordered_item_remove input:checkbox:checked').size()) {
            alert("Sorry, but You can not delete all items in order. Maybe, better remove this order?");
            return;
        }

        IWD.OrderManager.ShowLoadingMask();

        var formData = jQuery('#ordered_items_form').serialize();
        jQuery.ajax({
            url: IWD.OrderManager.OrderedItems.urlEditOrderedItems,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function () {
                location.reload();
            },
            error: function () {
                location.reload();
            }
        });
    },

    editOrderedItemsCancel: function () {
        jQuery('#ordered_items_table').show();
        jQuery('#ordered_items_edit_form').remove();
        jQuery('#add_ordered_items_form').remove();
    },

    /**** add new items ****/
    addOrderedItemsForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        jQuery('#button_add_selected_items').show();
        jQuery('#button_search_items_form').hide();

        IWD.OrderManager.OrderedItems.order.gridProducts = $H({});

        if (jQuery("#add_ordered_items_form").length > 0) {
            jQuery("#add_ordered_items_form").show();
            IWD.OrderManager.HideLoadingMask();
            return;
        }

        jQuery.ajax({url: IWD.OrderManager.OrderedItems.urlAddOrderedItemsForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status == 1) {
                    jQuery("#add_ordered_items_form").remove();

                    jQuery.getScript(result.url_configure_js, function () {
                        jQuery('form#product_composite_configure_form').remove();
                        jQuery("#anchor-content").append(result.configure_form.toString());
                        IWD.OrderManager.OrderedItems.initProductConfigure();

                        jQuery("#ordered_items_box").append('<div id="add_ordered_items_form">' + result.form.toString() + '</div>');

                        jQuery('form#product_composite_configure_form button[type="submit"]').on('click', function () {
                            var formData = jQuery('form#product_composite_configure_form').serializeArray();
                            var productId = productConfigure.current.itemId;
                            IWD.OrderManager.OrderedItems.configureItems[productId] = formData;
                        });

                        IWD.OrderManager.HideLoadingMask();
                    });
                }
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    addOrderedItems: function () {
        IWD.OrderManager.ShowLoadingMask();

        var selected_items = IWD.OrderManager.OrderedItems.order.gridProducts.toObject();

        if (Object.keys(selected_items).length <= 0) {
            jQuery("#add_ordered_items_form").hide();
            jQuery('#button_add_selected_items').hide();
            jQuery('#button_search_items_form').show();
            IWD.OrderManager.HideLoadingMask();
            return;
        }

        jQuery.ajax({
            url: IWD.OrderManager.OrderedItems.urlAddOrderedItems,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY +
                "&order_id=" + IWD.OrderManager.orderId +
                "&items=" + JSON.stringify(selected_items, null, 2) +
                "&options=" + JSON.stringify(IWD.OrderManager.OrderedItems.configureItems, null, 2),
            success: function (result) {
                if (result.status == 1) {
                    jQuery("#add_ordered_items_form").hide();
                    jQuery('#button_add_selected_items').hide();
                    jQuery('#button_search_items_form').show();

                    IWD.OrderManager.OrderedItems.order.gridProducts = $H({});
                    IWD.OrderManager.OrderedItems.enabledSubmitButton();
                    jQuery("#ordered_items_edit_table").append(result.form);
                }
                else if(result.status == 0){
                    alert(result.error_message);
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    /**** enabled submit button after edit ****/
    enabledSubmitButton: function () {
        jQuery('#edit_ordered_items_submit').removeAttr('disabled').removeClass('disabled');
    }
};

IWD.OrderManager.Address = {
    urlEditAddressForm: '',
    urlEditAddressSubmit: '',

    init: function () {
        jQuery(".order_address_edit").on("click", function (event) {
            event.preventDefault();
            var address_id = this.id.split('_').last();
            IWD.OrderManager.Address.editAddressForm(address_id);
        });
    },

    editAddressForm: function (address_id) {
        IWD.OrderManager.ShowLoadingMask();

        jQuery.ajax({url: IWD.OrderManager.Address.urlEditAddressForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&address_id=" + address_id,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    var order_address_block = jQuery("#order_address_" + address_id);
                    order_address_block.hide();
                    jQuery('#address_edit_form_' + address_id).remove();

                    var html = result.form.toString();
                    var VRegExp = new RegExp(/"region_id"/g);
                    html = html.replace(VRegExp, '"region_id_' + address_id + '"');
                    VRegExp = new RegExp(/"region"/g);
                    html = html.replace(VRegExp, '"region_' + address_id + '"');
                    VRegExp = new RegExp(/"country_id"/g);
                    html = html.replace(VRegExp, '"country_id_' + address_id + '"');
                    VRegExp = new RegExp(/"vat_id"/g);
                    html = html.replace(VRegExp, '"vat_id_' + address_id + '"');

                    order_address_block.parent().append(html);
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editAddressSubmit: function (address_id) {
        var addressFormValidation = eval('addressFormValidation_' + address_id);
        if (!addressFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var form = jQuery('#address_edit_form_' + address_id);
        var formData = form.serialize();

        jQuery.ajax({
            url: IWD.OrderManager.Address.urlEditAddressSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function (result) {
				
                location.reload();

                /* if (result.ajaxExpired) {
                 location.reload();
                 }
                 else if (result.status) {
                 form.remove();
                 jQuery("#order_address_" + address_id).html(result.address).show();
                 }

                 IWD.OrderManager.HideLoadingMask();*/
            },
            error: function (result) {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editAddressCancel: function (address_id) {
        jQuery('#address_edit_form_' + address_id).remove();
        jQuery("#order_address_" + address_id).show();
    }
};

IWD.OrderManager.AccountInfo = {
    urlEditAccountForm: '',
    urlEditAccountSubmit: '',

    init: function () {
        jQuery(".account_information_edit").click(function (event) {
            event.preventDefault();
            IWD.OrderManager.AccountInfo.editCustomerInfoForm();
        });
    },

    editCustomerInfoForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        jQuery.ajax({url: IWD.OrderManager.AccountInfo.urlEditAccountForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    jQuery("#account_information_" + IWD.OrderManager.orderId).hide();
                    jQuery('#account_information_form_' + IWD.OrderManager.orderId).remove();
                    jQuery("#account_information_" + IWD.OrderManager.orderId).parent().append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCustomerInfoSubmit: function () {
        var accountInfoFormValidation = new varienForm('account_information_form_' + IWD.OrderManager.orderId);
        if (!accountInfoFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var form = jQuery('#account_information_form_' + IWD.OrderManager.orderId);
        var formData = form.serialize();

        jQuery.ajax({
            url: IWD.OrderManager.AccountInfo.urlEditAccountSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function (result) {
                location.reload();
                /*if (result.ajaxExpired) {
                 location.reload();
                 }
                 else if (result.status) {
                 form.remove();
                 jQuery("#account_information_" + IWD.OrderManager.orderId).html(result.text).show();
                 }
                 IWD.OrderManager.HideLoadingMask();*/
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCustomerInfoCancel: function () {
        jQuery('#account_information_form_' + IWD.OrderManager.orderId).remove();
        jQuery("#account_information_" + IWD.OrderManager.orderId).show();
    }
};

IWD.OrderManager.OrderInfo = {
    urlEditOrderInfoForm: '',
    urlEditOrderInfoSubmit: '',

    init: function () {
        jQuery(".order_information_edit").on("click", function (event) {
            event.preventDefault();
            IWD.OrderManager.OrderInfo.editOrderInformationForm();
        });
    },

    editOrderInformationForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        jQuery.ajax({url: IWD.OrderManager.OrderInfo.urlEditOrderInfoForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    jQuery("#order_information").hide();
                    jQuery('#order_information_form').remove();
                    jQuery("#order_information").parent().append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderInformationSubmit: function () {
        var orderInfoFormValidation = new varienForm('order_information_form');
        if (!orderInfoFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var form = jQuery('#order_information_form');
        var formData = form.serialize();

        jQuery.ajax({
            url: IWD.OrderManager.OrderInfo.urlEditOrderInfoSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function (result) {
                location.reload();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderInformationCancel: function () {
        jQuery('#order_information_form').remove();
        jQuery("#order_information").show();
    }
};

IWD.OrderManager.Comments = {
    urlEditCommentForm: '',
    urlEditCommentSubmit: '',
    urlDeleteCommentSubmit: '',
    type: 'order',
    confirmText: "Are you sure?",

    init: function (type) {
        IWD.OrderManager.Comments.type = type;

        jQuery(".delete_history_icon").on('click', function () {
            IWD.OrderManager.Comments.deleteComment(this);
        });

        jQuery(".update_history_icon").on('click', function () {
            IWD.OrderManager.Comments.editCommentForm(this);
        });
    },

    deleteComment: function (item) {
        if (confirm(IWD.OrderManager.Comments.confirmText)) {
            IWD.OrderManager.ShowLoadingMask();

            var comment_id = item.id.split('_').last();

            jQuery.ajax({url: IWD.OrderManager.Comments.urlDeleteCommentSubmit,
                type: "POST",
                dataType: 'json',
                data: "form_key=" + FORM_KEY +
                    "&type=" + IWD.OrderManager.Comments.type +
                    "&comment_id=" + comment_id,
                success: function (result) {
                    if (result.ajaxExpired) {
                        location.reload();
                    }
                    else if (result.status) {
                        jQuery(item).hide();
                        jQuery(item).parent().delay(500).hide(1000);
                    }
                    IWD.OrderManager.HideLoadingMask();
                },
                error: function () {
                    IWD.OrderManager.HideLoadingMask();
                }
            });
        }
    },

    editCommentForm: function (item) {
        IWD.OrderManager.ShowLoadingMask();

        var comment_id = item.id.split('_').last();

        jQuery.ajax({url: IWD.OrderManager.Comments.urlEditCommentForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY +
                "&type=" + IWD.OrderManager.Comments.type +
                "&comment_id=" + comment_id,
            success: function (result) {
                jQuery("#comment_text_" + comment_id).hide();
                jQuery("#updated_comment_form_" + comment_id).remove();
                jQuery(item).parent().append(result.comment);
                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCommentSubmit: function (comment_id) {
        IWD.OrderManager.ShowLoadingMask();

        var comment_text = jQuery("textarea#updated_comment_text_" + comment_id).val();

        jQuery.ajax({url: IWD.OrderManager.Comments.urlEditCommentSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY +
                "&comment_id=" + comment_id +
                "&type=" + IWD.OrderManager.Comments.type +
                "&comment_text=" + comment_text,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else {
                    var comment = (result.comment != null) ? result.comment : '';
                    jQuery("#updated_comment_form_" + comment_id).remove();
                    jQuery("#comment_text_" + comment_id).html(comment).show();
                    IWD.OrderManager.HideLoadingMask();
                }
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCommentCancel: function (comment_id) {
        jQuery("#updated_comment_form_" + comment_id).remove();
        jQuery("#comment_text_" + comment_id).show();
    }
};

IWD.OrderManager.Shipping = {
    urlEditShippingForm: '',
    urlEditShippingSubmit: '',

    init: function () {
        jQuery(".order_shipping_edit").on("click", function (event) {
            event.preventDefault();
            IWD.OrderManager.Shipping.editShippingForm();
        });

        IWD.OrderManager.Shipping.radioInit();
        IWD.OrderManager.Shipping.interactiveForm();
    },

    radioInit: function(){
        jQuery(document).on("change", "#order-shipping-method-choose input[type=radio]", function () {
            IWD.OrderManager.Shipping.showEditTable();
        });

        jQuery(document).on('keypress', "#order-shipping-method-choose input.validate-number", function (e) {
            if (e.which == 13) return 1;
            var letters = '1234567890.,';
            return (letters.indexOf(String.fromCharCode(e.which)) != -1);
        });
    },

    showEditTable: function () {
        jQuery("#order-shipping-method-choose input[type=text]").attr('disabled', 'disabled').removeClass('required-entry');

        jQuery("#order-shipping-method-choose input[name=shipping_method_radio]").each(function() {
            var code = jQuery(this).attr('id');
            if(jQuery("#" + code).prop('checked')){
                jQuery("#" + code + "_edit_table").show();
                jQuery('#order-shipping-method-choose input[name="s_amount_excl_tax[' + jQuery("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
                jQuery('#order-shipping-method-choose input[name="s_amount_incl_tax[' + jQuery("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
                jQuery('#order-shipping-method-choose input[name="s_tax_percent[' + jQuery("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
                jQuery('#order-shipping-method-choose input[name="s_description[' + jQuery("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
            }
            else{
                jQuery("#" + code + "_edit_table").hide();
            }
        });
    },

    getInputId: function (item) {
        var reg = /items\[(\w+)\]\[(\w+)\]/i;
        var attr_name = reg.exec(jQuery(item).attr('name'));
        return attr_name[1];
    },

    interactiveForm: function() {
        jQuery(document).on('change', "#order-shipping-method-choose input.input-text", function () {
            var code = jQuery(this).attr('data-method');

            var amount_excl_tax = jQuery('#order-shipping-method-choose input[name="s_amount_excl_tax[' + code + ']"]');
            var amount_incl_tax = jQuery('#order-shipping-method-choose input[name="s_amount_incl_tax[' + code + ']"]');
            var tax_percent = jQuery('#order-shipping-method-choose input[name="s_tax_percent[' + code + ']"]');

            if(jQuery(this).hasClass('amount_excl_tax') || jQuery(this).hasClass('tax_percent')){
                var incl_tax = parseFloat(amount_excl_tax.val()) + (parseFloat(amount_excl_tax.val()) * parseFloat(tax_percent.val()) / 100);
                amount_incl_tax.val(incl_tax.toFixed(2));
            } else if(jQuery(this).hasClass('amount_incl_tax')){
                var excl_tax = parseFloat(amount_incl_tax.val()) / (1 + parseFloat(tax_percent.val()) / 100);
                amount_excl_tax.val(excl_tax.toFixed(2));
            }
        });
    },

    editShippingForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        jQuery.ajax({url: IWD.OrderManager.Shipping.urlEditShippingForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    jQuery('#iwd_shipping_edit_form').remove();
                    jQuery("#order_shipping").hide();
                    jQuery("#order_shipping").parent().append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editShippingSubmit: function () {
        var shippingFormValidation = new varienForm('iwd_shipping_edit_form');
        if (!shippingFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var formData = jQuery('#iwd_shipping_edit_form').serialize();

        jQuery.ajax({
            url: IWD.OrderManager.Shipping.urlEditShippingSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId + "&" + formData,
            success: function (result) {
                location.reload();
            },
            error: function (result) {
                location.reload();
            }
        });
    },

    editShippingCancel: function () {
        jQuery('#iwd_shipping_edit_form').remove();
        jQuery("#order_shipping").show();
    }

};

IWD.OrderManager.Payment = {
    urlEditPaymentForm: '',
    urlEditPaymentSubmit: '',

    init: function () {
        jQuery(".order_payment_edit").on("click", function (event) {
            event.preventDefault();
            IWD.OrderManager.Payment.editPaymentForm();

        });

        IWD.OrderManager.Payment.radioInit();
    },

    radioInit: function(){
        jQuery(document).on("change", "#iwd_payment_edit_form [name='payment[method]']", function () {
            jQuery("#iwd_edit_payment_form_submit").removeAttr("disabled").removeClass("disabled");
        });
    },

    editPaymentForm: function(){
        IWD.OrderManager.ShowLoadingMask();

        jQuery.ajax({url: IWD.OrderManager.Payment.urlEditPaymentForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    jQuery('#iwd_payment_edit_form').remove();
                    jQuery("#order_payment").hide();
                    jQuery("#order_payment").parent().append(result.form.toString());

                    if(jQuery('#order-billing_method_form dd').length == 1){
                        jQuery("#iwd_edit_payment_form_submit").removeAttr("disabled").removeClass("disabled");
                    }

                    jQuery('#iwd_payment_edit_form input[type=text]').val("");
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editPaymentSubmit: function(){
        var paymentFormValidation = new varienForm('iwd_payment_edit_form');
        if (!paymentFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var formData = jQuery('#iwd_payment_edit_form').serialize();

        jQuery.ajax({
            url: IWD.OrderManager.Payment.urlEditPaymentSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId + "&" + formData,
            success: function (result) {
                location.reload();
            },
            error: function (result) {
                location.reload();
            }
        });
    },

    editPaymentCancel: function () {
        jQuery('#iwd_payment_edit_form').remove();
        jQuery("#order_payment").show();
    }
};

/**** interactive edit grid ****/
IWD.OrderManager.TaxCalculation = {
    taxCalculationMethodBasedOn: 0, /* algorithm */
    taxCalculationBasedOn: 0,
    catalogPrices: 0, /* 1-include; 0-exclude tax */
    shippingPrices: 0,
    applyTaxAfterDiscount: 0, /* applyCustomerTax */
    discountTax: 0, /* applyDiscountOnPrices */

    CALC_TAX_BEFORE_DISCOUNT_ON_EXCL: '0_0',
    CALC_TAX_BEFORE_DISCOUNT_ON_INCL: '0_1',
    CALC_TAX_AFTER_DISCOUNT_ON_EXCL: '1_0',
    CALC_TAX_AFTER_DISCOUNT_ON_INCL: '1_1',

    init: function () {
        jQuery(document).on('keypress', "input.validate-number", function (e) {
            if (e.which == 13) return 1;
            var letters = '1234567890.,';
            return (letters.indexOf(String.fromCharCode(e.which)) != -1);
        });

        jQuery(document).on('change', "input.edit_order_item", function () {
            IWD.OrderManager.TaxCalculation.updateOrderItemInput(this);
            IWD.OrderManager.TaxCalculation.enabledSubmitButton();
        });

        jQuery(document).on('change', "input[type=checkbox].remove_ordered_item", function () {
            IWD.OrderManager.TaxCalculation.removeItemRow(this);
            IWD.OrderManager.TaxCalculation.enabledSubmitButton();
        });
    },

    removeItemRow: function (item) {
        var parent_id = jQuery(item).attr('data-parent-id') || null;
        var id = jQuery(item).attr('data-item-id') || null;
        var result = true;

        if (jQuery(item).prop("checked")) {
            result = IWD.OrderManager.TaxCalculation.disabledRow(id, parent_id);
        } else {
            result = IWD.OrderManager.TaxCalculation.enabledRow(id, parent_id);
        }

        if (parent_id && result) {
            var bundle_items = IWD.OrderManager.TaxCalculation.getBundleItems(parent_id);
            if (!IWD.OrderManager.TaxCalculation.isRemoveAllBundleItems(bundle_items, parent_id))
                IWD.OrderManager.TaxCalculation.calculateBundleTotals(bundle_items, parent_id);
        }
    },

    disabledRow: function (row_id, parent_id) {
        var row_item = jQuery('#ordered_items_edit_table tr[data-item-id="' + row_id + '"]');
        row_item.addClass('removed_item');
        row_item.find('input[type=text]').attr('disabled', 'disabled');

        /* for bundle product */
        jQuery('input.remove_ordered_item.has_parent_' + row_id).prop("checked", true).click(IWD.OrderManager.TaxCalculation.deactivator);
        jQuery('tr.has_parent_' + row_id).addClass('removed_item');
        jQuery('tr.has_parent_' + row_id + ' input[type=text]').attr('disabled', 'disabled');

        return true;
    },

    enabledRow: function (row_id, parent_id) {
        if (parent_id && jQuery('#remove_' + parent_id).prop("checked"))
            return false;

        var row_item = jQuery('#ordered_items_edit_table tr[data-item-id="' + row_id + '"]');
        row_item.removeClass('removed_item');
        row_item.find('input[type=text]').removeAttr('disabled');

        /* for bundle product */
        jQuery('input.remove_ordered_item.has_parent_' + row_id).prop("checked", false).unbind('click', IWD.OrderManager.TaxCalculation.deactivator);
        jQuery('tr.has_parent_' + row_id).removeClass('removed_item');
        jQuery('tr.has_parent_' + row_id + ' input[type=text]').removeAttr('disabled');

        return true;
    },

    calculateBundleTotals: function (bundle_items, bundle_id) {
        /* !canShowPriceInfo */
        if (!bundle_items[Object.keys(bundle_items)[0]].price.val())
            return false;

        var total_price_tax_incl = 0;
        var total_price_tax_excl = 0;
        var total_subtotal_tax_incl = 0;
        var total_subtotal_tax_excl = 0;
        var total_tax_amount = 0;

        jQuery.each(bundle_items, function (i, input) {
            /* item was removed */
            if (input.remove.prop("checked")) {
                return true;
            }
            total_price_tax_incl += parseFloat(input.price_incl_tax.val());
            total_price_tax_excl += parseFloat(input.price.val());
            total_subtotal_tax_incl += parseFloat(input.subtotal_incl_tax.val());
            total_subtotal_tax_excl += parseFloat(input.subtotal.val());
            total_tax_amount += parseFloat(input.tax_amount.val());
        });

        var bundle = IWD.OrderManager.TaxCalculation.getInputs(bundle_id);
        bundle.price_incl_tax.val(total_price_tax_incl.toFixed(2));
        bundle.price.val(total_price_tax_excl.toFixed(2));
        bundle.subtotal_incl_tax.val(total_subtotal_tax_incl.toFixed(2));
        bundle.subtotal.val(total_subtotal_tax_excl.toFixed(2));
        bundle.tax_amount.val(total_tax_amount.toFixed(2));
    },

    isRemoveAllBundleItems: function (bundle_items, bundle_id) {
        var count_removed_items = 0;
        jQuery.each(bundle_items, function (i, input) {
            if (input.remove.prop("checked")) count_removed_items++;
        });

        /* checked all bundle items */
        if (count_removed_items == Object.keys(bundle_items).length) {
            jQuery('input.remove_ordered_item.has_parent_' + bundle_id).prop("checked", false);
            IWD.OrderManager.TaxCalculation.calculateBundleTotals(bundle_items, bundle_id);
            jQuery('input[name="items[' + bundle_id + '][remove]"').prop("checked", true);
            IWD.OrderManager.TaxCalculation.disabledRow(bundle_id, null);
            return true;
        }

        return false;
    },

    updateBundleItems: function (name, id) {
        var bundle_items = IWD.OrderManager.TaxCalculation.getBundleItems(id);
        if (Object.keys(bundle_items).length == 0)
            return;

        switch (name) {
            case "qty":
                var bundle = IWD.OrderManager.TaxCalculation.getInputs(id);
                var bundle_qty = parseFloat(bundle.qty_ordered.val());

                jQuery.each(bundle_items, function (i, input) {
                    var qty_item_in_bundle = parseFloat(input.qty_item_in_bundle.val());
                    input.qty_ordered.val(bundle_qty * qty_item_in_bundle).change();
                });

                break;
        }
    },

    /** helpers methods **/
    deactivator: function (event) {
        event.preventDefault();
    },
    getInputId: function (item) {
        var reg = /items\[(\w+)\]\[(\w+)\]/i;
        var attr_name = reg.exec(jQuery(item).attr('name'));
        return attr_name[1];
    },
    getInputName: function (item) {
        var reg = /items\[(\w+)\]\[(\w+)\]/i;
        var attr_name = reg.exec(jQuery(item).attr('name'));
        return attr_name[2];
    },
    getInputs: function (id) {
        return {
            original_price: jQuery("input[name='items[" + id + "][original_price]']"),
            price: jQuery("input[name='items[" + id + "][price]']"),
            price_incl_tax: jQuery("input[name='items[" + id + "][price_incl_tax]']"),
            qty_ordered: jQuery("input[name='items[" + id + "][qty]']"),
            subtotal: jQuery("input[name='items[" + id + "][subtotal]']"),
            subtotal_incl_tax: jQuery("input[name='items[" + id + "][subtotal_incl_tax]']"),
            tax_amount: jQuery("input[name='items[" + id + "][tax_amount]']"),
            hidden_tax_amount: jQuery("input[name='items[" + id + "][hidden_tax_amount]']"),
            weee_tax_applied_row_amount: jQuery("input[name='items[" + id + "][weee_tax_applied_row_amount]']"),
            tax_percent: jQuery("input[name='items[" + id + "][tax_percent]']"),
            discount_amount: jQuery("input[name='items[" + id + "][discount_amount]']"),
            discount_percent: jQuery("input[name='items[" + id + "][discount_percent]']"),
            row_total: jQuery("input[name='items[" + id + "][row_total]']"),

            qty_item_in_bundle: jQuery("input[name='items[" + id + "][qty_item_in_bundle]']"),

            remove: jQuery("input[name='items[" + id + "][remove]']"),
            parent: jQuery("input[name='items[" + id + "][parent]']")
        };
    },
    getBundleItems: function (bundle_id) {
        var children = {};
        jQuery(".has_parent_" + bundle_id).each(function () {
            var item_id = jQuery(this).attr('data-item-id');
            if (item_id != bundle_id)
                children[item_id] = IWD.OrderManager.TaxCalculation.getInputs(item_id);
        });
        return children;
    },
    getCalculationSequence: function () {
        if (IWD.OrderManager.TaxCalculation.applyTaxAfterDiscount) {
            if (IWD.OrderManager.TaxCalculation.discountTax)
                return IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL;
            return IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL;
        } else {
            if (IWD.OrderManager.TaxCalculation.discountTax)
                return IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL;
            return IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL;
        }
    },
    enabledSubmitButton: function () {
        jQuery('#edit_ordered_items_submit').removeAttr('disabled').removeClass('disabled');
    },
    /*********************/

    _checkQty: function(item){
        var data_stock_qty_increment = parseFloat(jQuery(item.qty_ordered).attr("data-stock-qty-increment"));
        var data_stock_qty = parseFloat(jQuery(item.qty_ordered).attr("data-stock-qty"));
        var data_stock_min_sales_qty = parseFloat(jQuery(item.qty_ordered).attr("data-stock-min-sales-qty"));
        var data_stock_max_sales_qty = parseFloat(jQuery(item.qty_ordered).attr("data-stock-max-sales-qty"));
        var data_stock_qty_min = parseFloat(jQuery(item.qty_ordered).attr("data-stock-qty-min"));
        var data_qty_refunded = parseFloat(jQuery(item.qty_ordered).attr("data-qty-refunded"));

        var qty_value = parseFloat(jQuery(item.qty_ordered).val());

        /* check max sales qty */
        if(qty_value > data_stock_max_sales_qty)
            qty_value = data_stock_max_sales_qty;

        /* check min sales qty */
        if(qty_value < data_stock_min_sales_qty)
            qty_value = data_stock_min_sales_qty;

        /* check stock qty */
        if(data_stock_qty < qty_value)
            qty_value = data_stock_qty;

        /* check qty increment */
        if(qty_value % data_stock_qty_increment != 0){
            qty_value = Math.round((qty_value / data_stock_qty_increment)) * data_stock_qty_increment;
        }

        if(qty_value <= data_stock_max_sales_qty && qty_value >= data_stock_min_sales_qty)
            jQuery(item.qty_ordered).val(qty_value);
    },

    /* 1. After every change */
    updateOrderItemInput: function (item) {
        var id = IWD.OrderManager.TaxCalculation.getInputId(item);
        var name = IWD.OrderManager.TaxCalculation.getInputName(item);
        var input = IWD.OrderManager.TaxCalculation.getInputs(id);

        /* !canShowPriceInfo */
        if (!input.price.val())
            return;

        switch (name) {
            case "original_price":
                break;
            case "price":
                IWD.OrderManager.TaxCalculation._calculatePriceExclTax(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "price_incl_tax":
                IWD.OrderManager.TaxCalculation._calculatePriceInclTax(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "qty":
                IWD.OrderManager.TaxCalculation._checkQty(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "tax_amount":
                break;
            case "tax_percent":
                IWD.OrderManager.TaxCalculation._changePrice(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "discount_amount":
                break;
            case "discount_percent":
                break;
        }

        IWD.OrderManager.TaxCalculation.baseCalculation(input);
        IWD.OrderManager.TaxCalculation._calculateRowTotal(input);

        /* update related items */
        IWD.OrderManager.TaxCalculation.updateBundleItems(name, id);

        /* item is a part of bundle product (has parent) */
        if (input.parent.val()) {
            var parent_id = input.parent.val();
            var bundle_items = IWD.OrderManager.TaxCalculation.getBundleItems(parent_id);
            IWD.OrderManager.TaxCalculation.calculateBundleTotals(bundle_items, parent_id);
        }
    },

    /* 2. Select a tax calculation method */
    baseCalculation: function (input) {
        switch (IWD.OrderManager.TaxCalculation.taxCalculationMethodBasedOn) {
            case 'UNIT_BASE_CALCULATION':
                IWD.OrderManager.TaxCalculation._unitBaseCalculation(input);
                break;
            case 'ROW_BASE_CALCULATION':
                IWD.OrderManager.TaxCalculation._rowBaseCalculation(input);
                break;
            case 'TOTAL_BASE_CALCULATION':
                IWD.OrderManager.TaxCalculation._totalBaseCalculation(input);
                break;
        }
    },

    /* 2.1. Method: Unit price */
    _unitBaseCalculation: function (input) {
        var tax_amount = 0;
        var hidden_tax_amount = 0;

        switch (IWD.OrderManager.TaxCalculation.getCalculationSequence()) {
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                break;
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());

                var qty = parseFloat(input.qty_ordered.val());
                var discountAmount = parseFloat(input.discount_amount.val()) / qty;
                var price = parseFloat(input.price_incl_tax.val());
                var unitTaxDiscount = 0;
                var unitTax = 0;

                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 1);
                    var discountRate = (unitTax / price) * 100;
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, discountRate, 0);  /*1*/
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 1);
                } else {
                    price = parseFloat(input.price.val());
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 0);
                }

                unitTax = Math.max(unitTax - unitTaxDiscount, 0);
                tax_amount = Math.max(qty * unitTax, 0);
                hidden_tax_amount = Math.max(qty * hidden_tax_amount, 0);
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());

                var qty = parseFloat(input.qty_ordered.val());
                var discountAmount = parseFloat(input.discount_amount.val()) / qty;
                var price = parseFloat(input.price_incl_tax.val());
                var unitTax = 0;
                var unitTaxDiscount = 0;

                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 1);
                    var discountRate = (unitTax / price) * 100;
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, discountRate, 0); /*1*/
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 1);
                } else {
                    price = parseFloat(input.price.val());
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 0);
                }

                unitTax = Math.max(unitTax - unitTaxDiscount, 0);
                tax_amount = Math.max(qty * unitTax, 0);
                hidden_tax_amount = Math.max(qty * hidden_tax_amount, 0);
                break;
        }

        input.tax_amount.val(tax_amount.toFixed(2));
        input.hidden_tax_amount.val(hidden_tax_amount.toFixed(2));
    },

    /* 2.2. Method: Row total */
    _rowBaseCalculation: function (input) {
        var tax_amount = 0;
        var hidden_tax_amount = 0;

        switch (IWD.OrderManager.TaxCalculation.getCalculationSequence()) {
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                    tax_amount -= hidden_tax_amount;
                } else {
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val() - input.discount_amount.val(), input.tax_percent.val(), 0);
                }
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                    tax_amount -= hidden_tax_amount;
                } else {
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val() - input.discount_amount.val(), input.tax_percent.val(), 0);
                }
                break;
        }

        input.tax_amount.val(tax_amount.toFixed(2));
        input.hidden_tax_amount.val(hidden_tax_amount.toFixed(2));
    },

    /* 2.3. Method: Total */
    _totalBaseCalculation: function (input) {
        var tax_amount = 0;
        var price = 0;
        var hidden_tax_amount = 0;

        switch (IWD.OrderManager.TaxCalculation.getCalculationSequence()) {
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 0);
                if(IWD.OrderManager.TaxCalculation.catalogPrices) {
                    price = input.subtotal.val() - input.discount_amount.val();
                } else {
                    price = input.subtotal.val() - input.discount_amount.val() - hidden_tax_amount;
                    hidden_tax_amount = 0;
                }
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                if(IWD.OrderManager.TaxCalculation.catalogPrices) {
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                    price = input.subtotal.val() - input.discount_amount.val() + hidden_tax_amount;
                } else {
                    hidden_tax_amount = 0;
                    price = input.subtotal.val() - input.discount_amount.val();
                }
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                break;
        }

        input.tax_amount.val(tax_amount.toFixed(2));
        input.hidden_tax_amount.val(hidden_tax_amount.toFixed(2));
    },

    _calculateDiscountAmount: function (input, subtotal) {
        var discount_percent = parseFloat(input.discount_percent.val());
        var discount_amount = subtotal * discount_percent / 100;

        input.discount_amount.val(discount_amount.toFixed(2));
        input.discount_percent.val(discount_percent.toFixed(2));
    },
    _calcTaxAmount: function (price, tax_percent, priceIncludeTax) {
        var tax_rate = parseFloat(tax_percent) / 100;
        price = parseFloat(price);

        if (priceIncludeTax) {
            return price * (1 - 1 / (1 + tax_rate));
        } else {
            return price * tax_rate;
        }
    },
    _calculateSubtotal: function (input) {
        var subtotal = parseFloat(input.price.val()) * parseFloat(input.qty_ordered.val());
        var subtotal_incl_tax = parseFloat(input.price_incl_tax.val()) * parseFloat(input.qty_ordered.val());
        input.subtotal.val(subtotal.toFixed(2));
        input.subtotal_incl_tax.val(subtotal_incl_tax.toFixed(2));
    },
    _calculateRowTotal: function (input) {
        var subtotal = parseFloat(input.subtotal.val());
        var discount_amount = parseFloat(input.discount_amount.val());
        var tax_amount = parseFloat(input.tax_amount.val());
        var hidden_tax_amount = parseFloat(input.hidden_tax_amount.val());
        var weee_tax_applied_row_amount = parseFloat(input.weee_tax_applied_row_amount.val());

        var row_total = subtotal + tax_amount + hidden_tax_amount + weee_tax_applied_row_amount - discount_amount;

        input.row_total.val(row_total.toFixed(2));
        return row_total;
    },
    _calculatePriceExclTax: function (input) {
        var price_excl_tax = parseFloat(input.price.val());
        var tax_percent = parseFloat(input.tax_percent.val());
        var price = price_excl_tax * (1 + tax_percent / 100);

        input.price.val(price_excl_tax.toFixed(2));
        input.price_incl_tax.val(price.toFixed(2));
        input.tax_percent.val(tax_percent.toFixed(2));
    },
    _calculatePriceInclTax: function (input) {
        var price_incl_tax = parseFloat(input.price_incl_tax.val());
        var tax_percent = parseFloat(input.tax_percent.val());

        var price = price_incl_tax / (1 + tax_percent / 100);

        input.price.val(price.toFixed(2));
        input.price_incl_tax.val(price_incl_tax.toFixed(2));
        input.tax_percent.val(tax_percent.toFixed(2));
    },
    _changePrice: function (input) {
        if (IWD.OrderManager.TaxCalculation.catalogPrices) {
            IWD.OrderManager.TaxCalculation._calculatePriceInclTax(input); /* incl tax fixed */
        } else {
            IWD.OrderManager.TaxCalculation._calculatePriceExclTax(input); /* excl tax fixed */
        }
    }
};
