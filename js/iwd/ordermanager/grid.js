;(function(c,g,k){function e(d,e){var a=this;a.$el=c(d);a.el=d;a.$window=c(g);a.$clonedHeader=null;a.$originalHeader=null;a.isCloneVisible=!1;a.leftOffset=null;a.topOffset=null;a.init=function(){a.options=c.extend({},h,e);a.$el.each(function(){var b=c(this);b.css("padding",0);a.$originalHeader=c("thead:first .headings",this);a.$clonedHeader=a.$originalHeader.clone();a.$clonedHeader.addClass("tableFloatingHeader");a.$clonedHeader.css({position:"fixed",top:0,"z-index":100,display:"none"});a.$originalHeader.addClass("tableFloatingHeaderOriginal");a.$originalHeader.after(a.$clonedHeader);c("th",a.$clonedHeader).click(function(b){b=c("th",a.$clonedHeader).index(this);c("th",a.$originalHeader).eq(b).click()});b.bind("sortEnd",a.updateWidth)});a.updateWidth();a.toggleHeaders();a.$window.scroll(a.toggleHeaders);a.$window.resize(a.toggleHeaders);a.$window.resize(a.updateWidth);jQuery("#sales_order_grid .grid .hor-scroll").on("scroll",a.toggleHeaders)};a.toggleHeaders=function(){a.$el.each(function(){var b=c(this),f=isNaN(a.options.fixedOffset)?a.options.fixedOffset.height():a.options.fixedOffset,d=b.offset(),e=a.$window.scrollTop()+f,g=a.$window.scrollLeft();e+37>d.top&&e<d.top+b.height()?(b=d.left-g,a.isCloneVisible&&b===a.leftOffset&&f===a.topOffset||(a.$clonedHeader.css({top:f+37,"margin-top":0,left:b+1,display:"block"}),a.$originalHeader.css("visibility","hidden"),a.isCloneVisible=!0,a.leftOffset=b,a.topOffset=f)):a.isCloneVisible&&(a.$clonedHeader.css("display","none"),a.$originalHeader.css("visibility","visible"),a.isCloneVisible=!1)})};a.updateWidth=function(){c("th",a.$clonedHeader).each(function(b){var d=c(this);b=c("th",a.$originalHeader).eq(b);this.className=b.attr("class")||"";d.css("width",b.width())});a.$clonedHeader.css("width",a.$originalHeader.width())};a.init()}var h={fixedOffset:0};c.fn.stickyTableHeaders=function(d){return this.each(function(){c.data(this,"plugin_stickyTableHeaders")||c.data(this,"plugin_stickyTableHeaders",new e(this,d))})}})(jQuery,window);
window.hasOwnProperty = function (obj) {return (this[obj]) ? true : false;};
if (!window.hasOwnProperty('IWD')) {IWD = {};}
if (!window.hasOwnProperty('IWD.OrderManager')) {IWD.OrderManager = {};}

IWD.OrderManager.Grid = {
    singleton: 0,
    isFixGridHeader: 1,
    iwdViewOrderedItems: "",
    iwdViewProductItems: "",
    statusColors: "",

    init: function(){
        if(IWD.OrderManager.Grid.singleton == 1)
            return;
        jQuery(document).on('click', ".iwd_order_grid_more.show", function (e) {
            e.stopPropagation();
            jQuery(this).removeClass('show').addClass('hide');
            jQuery(this).prev('.iwd_order_items_in_grid').css("max-height", "none");
        });
        jQuery(document).on('click', ".iwd_order_grid_more.hide", function (e) {
            e.stopPropagation();
            jQuery(this).removeClass('hide').addClass('show');
            jQuery(this).prev('.iwd_order_items_in_grid').css("max-height", "84px");
        });

        jQuery(document).on('click', ".action_view_ordered_items", function () {
            IWD.OrderManager.Grid.ViewOrderedItems(this);
        });

        jQuery(document).on('click', ".action_view_product_items", function () {
            IWD.OrderManager.Grid.ViewProductItems(this);
        });

        jQuery(document).on('click', ".close-popup-table", function () {
            IWD.OrderManager.Grid.ClosePopupTable(this);
        });

        if (IWD.OrderManager.Grid.isFixGridHeader) {
            jQuery("#sales_order_grid_table").stickyTableHeaders({ fixedOffset: $(".header") });
        }

        IWD.OrderManager.Grid.ColorGridRow();
        IWD.OrderManager.Grid.singleton = 1;
    },

    ViewOrderedItems: function (elem) {
        var order_id = elem.id.split('_').last();

        IWD.OrderManager.Grid.ShowLoadingMask();

        var parent_table_id = jQuery(elem).closest("table").attr("id");

        jQuery.ajax({url: IWD.OrderManager.Grid.iwdViewOrderedItems,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + order_id,
            success: function (result) {
                if (result.status) {
                    jQuery("#view_ordered_item_" + order_id).remove();
                    var offset = jQuery(elem).parent().offset();
                    jQuery('#' + parent_table_id).append(result.table);
                    jQuery("#view_ordered_item_" + order_id).offset(function (i, coord) {
                        var newOffset = {};
                        newOffset.top = offset.top;
                        newOffset.left = offset.left;
                        var right = offset.left + jQuery(this).width();
                        if (jQuery(window).width() < right)
                            newOffset.left -= jQuery(this).width() + 20;
                        return newOffset;
                    });
                }
                IWD.OrderManager.Grid.HideLadingMask();
            },
            error: function () {
                IWD.OrderManager.Grid.HideLadingMask();
            }
        });
    },

    ViewProductItems: function (elem) {
        var order_id = elem.id.split('_').last();
        IWD.OrderManager.Grid.ShowLoadingMask();

        var parent_table_id = jQuery(elem).closest("table").attr("id");

        jQuery.ajax({url: IWD.OrderManager.Grid.iwdViewProductItems,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + order_id,
            success: function (result) {
                if (result.status) {
                    jQuery("#view_product_item_" + order_id).remove();
                    var offset = jQuery(elem).parent().offset();
                    jQuery('#' + parent_table_id).append(result.table);
                    jQuery("#view_product_item_" + order_id).offset(function (i, coord) {
                        var newOffset = {};
                        newOffset.top = offset.top;
                        newOffset.left = offset.left;

                        var right = offset.left + jQuery(this).width();
                        if (jQuery(window).width() < right)
                            newOffset.left -= jQuery(this).width() + 20;

                        return newOffset;
                    });
                }
                IWD.OrderManager.Grid.HideLadingMask();
            },
            error: function () {
                IWD.OrderManager.Grid.HideLadingMask();
            }
        });
    },

    ColorGridRow: function () {
        function unserialize(stringData) {
            var parts = stringData.split(";");
            var a = {};
            for (var i = 0, len = parts.length; i < len; i++) {
                var temp = parts[i].split(":");
                if (temp.length == 2) {
                    var key = temp[0];
                    a[key] = temp[1];
                }
            }
            return a;
        }

        var statusColorsArray = unserialize(IWD.OrderManager.Grid.statusColors);

        var grids = {
            '1':'#sales_order_grid_table',
            '2':'#sales_order_archive_grid',
            '3':'#customer_view_orders_grid_table',
            '4':'#customer_orders_grid_table'
        };

        jQuery.each(grids, function(key, value){
            if(jQuery(value).length) {
                jQuery(value + " tbody td.status-row").each(function () {
                    var key = jQuery.trim(jQuery(this).html());
                    var color = statusColorsArray[key];
                    if (color)
                        jQuery(this).parent('tr').css('background-color', '#' + color);
                });
            }
        });
    },



    ClosePopupTable: function (item) {
        jQuery(item).parent().remove();
    },

    ShowLoadingMask: function () {
        jQuery('#loading-mask').width(jQuery("html").width()).height(jQuery("html").height()).css('top', 0).css('left', -2).show();
    },

    HideLadingMask: function(){
        jQuery('#loading-mask').hide();
    }
};
