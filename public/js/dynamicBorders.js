(function ($) {
    DynamicCssBorders = function (selector) {
        this.selector = selector;
        this.beforeSelector = selector + '.dynamic-css-borders:before';
        this.afterSelector = selector + '.dynamic-css-borders:after';

        // Insert and manipulate DOM elements
        $(selector).addClass('dynamic-css-borders');
        $('body').prepend('<!-- DynamicBorders.js Start -->')
        this.$dynamicStyleTag = $('<style>').prependTo('body');
        $('body').prepend('<!-- DynamicBorders.js: End -->')

        // Update borders
        this.updateBorders();

        // Register event listeners
        $(window).on('resize', $.proxy(this.updateBorders, this));
    }

    DynamicCssBorders.prototype.updateBorders = function () {
        var width = $(this.selector).first().outerWidth();
        this.$dynamicStyleTag.text('' +
            this.beforeSelector + ', ' + this.afterSelector + '{border-left-width: ' + width + 'px; }'
        );
    }
})(jQuery);

$(document).ready(function () {
    var dynamicCssBorders = new DynamicCssBorders('main > section');
});