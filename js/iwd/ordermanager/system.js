jQuery(document).ready(function () {
    jQuery("#iwd_ordermanager_grid_order_columns").multiselect();
    jQuery("#iwd_ordermanager_customer_orders_orders_grid_columns").multiselect();
    jQuery("#iwd_ordermanager_customer_orders_resent_orders_grid_columns").multiselect();

    if (jQuery("#iwd_ordermanager_grid_order_status_color").length > 0) {
        var colorsArray = init();
        var currentColor = "ffffff";
        jQuery('.color-box').on('click',function () {
            currentColor = jQuery(this).closest('li').css('background-color');
            currentColor = rgb2hex(currentColor);
        }).colpick({
                onBeforeShow: function () {
                    jQuery(this).colpickSetColor(currentColor);
                },
                colorScheme: 'light',
                layout: 'rgbhex',
                onSubmit: function (hsb, hex, rgb, el) {
                    jQuery(el).colpickHide();
                    jQuery(el).closest('li').css('background-color', '#' + hex);
                    var id = jQuery(el).closest('li')[0].id;
                    colorsArray[id] = hex;
                    var colorsString = serialize(colorsArray);
                    jQuery("#iwd_ordermanager_grid_order_status_color").val(colorsString);
                }
            });

        jQuery('.clear-color').on('click', function (event) {
            var li = jQuery(this).closest('li')[0];
            if (delete(colorsArray[li.id])) {
                jQuery(li).css('background-color', '');
                var colorsString = serialize(colorsArray);
                jQuery("#iwd_ordermanager_grid_order_status_color").val(colorsString);
            }
        });
    }
});

function init() {
    var colorsString = jQuery("#iwd_ordermanager_grid_order_status_color").val();
    var colorsArray = unserialize(colorsString);

    jQuery('#order_status_color li').each(function () {
        var color = colorsArray[this.id];
        if (color)
            jQuery(this).css('background-color', '#' + color);
    });

    return colorsArray;
}

function serialize(arrayData) {
    var a = [];
    for (key in arrayData) {
        a.push(key + ":" + arrayData[key]);
    }
    return a.join(";");
}

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

function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\w*\((\d+),\s*(\d+),\s*(\d+)/);

    if(rgb == null)
        return "#FFFFFF";

    return "#" +
        ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2);
}