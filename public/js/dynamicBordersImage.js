(function ($) {
    DynamicCssBordersImage = function (selector) {
        this.selector = selector;
        this.beforeSelector = this.selector + ':before';
        this.afterSelector = this.selector + ':after';

        // Insert and manipulate DOM elements
        //$(selector).addClass('dynamic-css-borders');
        $('body').prepend('<!-- DynamicBordersImage.js Start -->')
        this.$dynamicStyleTagImage = $('<style>').prependTo('body');
        $('body').prepend('<!-- DynamicBordersImage.js: End -->')

        // Update borders
        this.updateBorders();

        // Register event listeners
        $(window).on('resize', $.proxy(this.updateBorders, this));
    }

    DynamicCssBordersImage.prototype.updateBorders = function () {
        var width = $(this.selector).first().outerWidth();
        this.$dynamicStyleTagImage.text('' +
            this.beforeSelector + ', ' + this.afterSelector + '{border-left-width: ' + width + 'px; }'
        );
    }
})(jQuery);

$(document).ready(function () {
    var dynamicCssBorders = new DynamicCssBordersImage('.gallery .image-container');
});