(function ($) {
    DynamicCssBordersBigger = function (selector) {
        this.selector = selector;
        this.biggerselector = selector + '.bigger';
        this.beforeSelector = this.biggerselector + '.dynamic-css-borders:before';
        this.afterSelector = this.biggerselector + '.dynamic-css-borders:after';

        // Insert and manipulate DOM elements
        //$(selector).addClass('dynamic-css-borders');
        $('body').prepend('<!-- DynamicBordersBigger.js Start -->')
        this.$dynamicStyleTagBigger = $('<style>').prependTo('body');
        $('body').prepend('<!-- DynamicBordersBigger.js: End -->')

        // Update borders
        this.updateBorders();

        // Register event listeners
        $(window).on('resize', $.proxy(this.updateBorders, this));
    }

    DynamicCssBordersBigger.prototype.updateBorders = function () {
        var width = $(this.selector).first().outerWidth();
        var widthbigger = $(this.biggerselector).first().outerWidth();
        var margin = (widthbigger / 2) - (width/2) ;
        this.$dynamicStyleTagBigger.text('' +
            this.beforeSelector + ', ' + this.afterSelector + '{margin-left: ' + margin + 'px; }'
        );
    }
})(jQuery);

$(document).ready(function () {
    var dynamicCssBorders = new DynamicCssBordersBigger('main > section');
});