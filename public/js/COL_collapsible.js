(function ($) {
    /**
     * Collapsible list
     *
     *
     * @param root
     * @returns {COL_collapsible}
     * @constructor
     */
    function COL_collapsible(root) {
        this.root = root;
        this.title = $(root).find('.COL_title');
        this.content = $(root).find('.COL_content');

        this.create();
        return this;
    }

    /**
     * Evaluates the action and value parameter from $().COL_collapsible(action,value)
     * and tries to call the corresponding action if defined here.
     *
     * @param action
     */
    COL_collapsible.prototype.callAction = function (action) {
        switch (action) {
            case 'create':
                this.create();
                break;
            case 'destroy':
                this.destroy();
                break;
            case 'expand':
                this.expand();
                break;
            case 'collapse':
                this.collapse();
                break;
        }
    }

    /**
     * Create the collapsible, change DOM elements etc.
     */
    COL_collapsible.prototype.create = function () {

        $(this.root).addClass('collapsible-ready');

        // Collapse content at start
        if ($(this.root).hasClass('expanded-at-start')) {
            $(this.content).removeAttr('hidden').attr('aria-expanded', 'true');
            if($(this.title).attr('data-collapse-title')){
                $(this.title).attr('aria-label',$(this.title).attr('data-collapse-title')).attr('title',$(this.title).attr('data-collapse-title'));
            }
        } else {
            $(this.root).addClass('collapsed');
            $(this.content).attr('aria-expanded', 'false').attr('hidden', 'true');
            if($(this.title).attr('data-expand-title')){
                $(this.title).attr('aria-label',$(this.title).attr('data-expand-title')).attr('title',$(this.title).attr('data-expand-title'));
            }
        }

        // Set aria-haspopup and stuff
        $(this.title).attr('aria-haspopup', 'true').attr('tabindex', '0').attr('aria-hidden', 'false').removeAttr('hidden');

        // Bind event handlers
        $(this.title).click($.proxy(this._handle_title_click, this));
        $(this.title).keydown($.proxy(this._handle_title_keydown, this));
    }

    /**
     * Destroy the collapsible and return revert all changes to the DOM
     */
    COL_collapsible.prototype.destroy = function () {
        $(this.root).removeClass('collapsed');
        $(this.content).removeAttr('aria-expanded').removeAttr('hidden');

        $(this.title).removeAttr('aria-haspopup').removeAttr('tabindex');
        $(this.title).off('click keydown');
    }

    /**
     * Handle title link event, expand corresponding jobs list
     *
     * @param event
     * @private
     */
    COL_collapsible.prototype._handle_title_click = function (event) {
        this.toggle();
    }

    /**
     * Handle keydown events, expand corresponding jobs list
     *
     * @param event
     * @private
     */
    COL_collapsible.prototype._handle_title_keydown = function (event) {
        switch (event.keyCode) {
            case 13:
                this.toggle();
                break;
        }
    }

    /**
     * Collapse / expand content
     *
     * @param titleElement
     */
    COL_collapsible.prototype.toggle = function () {
        if ($(this.root).hasClass('collapsed')) {
            this.expand();
        } else {
            this.collapse();
        }
    }

    /**
     * Expand the content of this collapsible
     */
    COL_collapsible.prototype.expand = function () {
        $(this.root).removeClass('collapsed');
        $(this.content).attr('aria-expanded', 'true').removeAttr('hidden');
        if($(this.title).attr('data-collapse-title')){
            var title = $(this.title).attr('data-collapse-title');
            $(this.title).attr('aria-label',title).attr('title',title);
        }
    }

    /**
     * Collapse the content of this collapsible
     */
    COL_collapsible.prototype.collapse = function () {
        $(this.root).addClass('collapsed');
        $(this.content).attr('aria-expanded', 'false').attr('hidden', true);
        if($(this.title).attr('data-expand-title')){
            var title = $(this.title).attr('data-expand-title');
            $(this.title).attr('aria-label',title).attr('title',title);
        }
    }

    /**
     * Collapsible constructor
     *
     * @constructor
     */
    $.fn.COL_collapsible = function (action, value) {
        var action = action || null;
        var value = value || null;

        $(this).each(function () {
            // If called with action parameter, we asume the plugin was already created
            // and we want to call an available action there. Else we create the plugin.
            if (action && (typeof this.COL_collapsible != 'undefined')) {
                this.COL_collapsible.callAction(action, value);
            } else {
                this.COL_collapsible = new COL_collapsible(this);
            }
        });
        return this;
    }
})(jQuery);

$(document).ready(function(){
    $('.COL_collapsible').COL_collapsible();
});






