var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('skipLinks', null, ['.skip-links .header-minicart>']);
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: true,
    updateOnActionRequest: false,

    beforeUpdate: function(html){
        return null;
    },
    afterUpdate: function(html, selectors){
        var skipLinks = $j('.skip-cart');
        var skipContents = $j('#header-cart');

        skipLinks.on('click', function (e) {
            e.preventDefault();

            var self = $j(this);
            var target = self.attr('href');

            // Get target element
            var elem = $j(target);

            // Check if stub is open
            var isSkipContentOpen = elem.hasClass('skip-active') ? 1 : 0;

            // Hide all stubs
            skipLinks.removeClass('skip-active');
            skipContents.removeClass('skip-active');

            // Toggle stubs
            if (isSkipContentOpen) {
                self.removeClass('skip-active');
            } else {
                self.addClass('skip-active');
                elem.addClass('skip-active');
            }
        });

        $j('#header-cart').on('click', '.skip-link-close', function(e) {
            var parent = $j(this).parents('.skip-content');
            var link = parent.siblings('.skip-link');

            parent.removeClass('skip-active');
            link.removeClass('skip-active');

            e.preventDefault();
        });

        return null;
    }
});
AW_AjaxCartPro.registerUpdater(AW_AjaxCartProUpdaterObject);
delete AW_AjaxCartProUpdaterObject;

