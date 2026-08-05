(function ($) {
    'use strict';

    var selectors = {
        wrapper: '.vybose-repeater-accordion__list',
        title: '.vybose-repeater-accordion__title',
        content: '.vybose-repeater-accordion__content'
    };

    var activeClass = 'active-toggle';

    function activateTab($wrapper, tabIndex, $title) {
        $title = $title || $wrapper.find(selectors.title + '[data-tab="' + tabIndex + '"]');

        var $content = $wrapper.find(selectors.content + '[data-tab="' + tabIndex + '"]');

        $title.addClass(activeClass).attr('aria-expanded', 'true');
        $content.addClass(activeClass).stop(true, true).slideDown(300);
    }

    function deactivateTab($wrapper, $title) {
        var tabIndex = $title.data('tab');
        var $content = $wrapper.find(selectors.content + '[data-tab="' + tabIndex + '"]');

        $title.removeClass(activeClass).attr('aria-expanded', 'false');
        $content.removeClass(activeClass).stop(true, true).slideUp(300);
    }

    function deactivateAllTabs($wrapper) {
        $wrapper.find(selectors.title + '.' + activeClass).each(function () {
            deactivateTab($wrapper, $(this));
        });
    }

    function bindWrapper($wrapper, settings) {
        if (!$wrapper.length || $wrapper.data('vyboseRepeaterAccordionBound')) {
            return;
        }

        settings = settings || {};
        $wrapper.data('vyboseRepeaterAccordionBound', true);

        $wrapper.find(selectors.title).each(function () {
            var $title = $(this);

            if (!$title.attr('aria-expanded')) {
                $title.attr('aria-expanded', $title.hasClass(activeClass) ? 'true' : 'false');
            }
        });

        $wrapper.on('click.vyboseRepeaterAccordion', selectors.title, function (event) {
            event.preventDefault();

            var $clickedTitle = $(event.currentTarget);
            var tabIndex = $clickedTitle.data('tab');
            var isAccordion = settings.type === 'accordion';
            var isActive = $clickedTitle.hasClass(activeClass);

            if (isActive) {
                deactivateTab($wrapper, $clickedTitle);
                return;
            }

            if (isAccordion) {
                deactivateAllTabs($wrapper);
            }

            activateTab($wrapper, tabIndex, $clickedTitle);
        });

        $wrapper.on('keydown.vyboseRepeaterAccordion', selectors.title, function (event) {
            if (event.which === 13 || event.which === 32) {
                event.preventDefault();
                $(event.currentTarget).trigger('click');
            }
        });

        if (window.location.hash) {
            openHashTarget($wrapper, window.location.hash);
        } else if (settings.defaultToggle > 0 && !$wrapper.find(selectors.title + '.' + activeClass).length) {
            activateTab($wrapper, settings.defaultToggle);
        }
    }

    function openHashTarget($wrapper, hash) {
        var cleanHash = String(hash).replace('#', '');

        if (!cleanHash) {
            return;
        }

        var $targetItem = $wrapper.find('[data-toggle-custom-id="' + cleanHash + '"]');

        if (!$targetItem.length) {
            return;
        }

        var $title = $targetItem.find(selectors.title).first();
        activateTab($wrapper, $title.data('tab'), $title);

        $('html, body').animate({
            scrollTop: $targetItem.offset().top - 100
        }, 500);
    }

    function bindAll() {
        $(selectors.wrapper).each(function () {
            var $wrapper = $(this);
            bindWrapper($wrapper, {
                type: $wrapper.data('type') || 'toggles',
                defaultToggle: parseInt($wrapper.data('default-toggle'), 10) || 0
            });
        });
    }

    $(bindAll);

    $(window).on('elementor/frontend/init', function () {
        if (!window.elementorFrontend || !window.elementorModules) {
            return;
        }

        var VyboseRepeaterAccordionHandler = window.elementorModules.frontend.handlers.Base.extend({
            onInit: function () {
                window.elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

                var settings = this.getElementSettings();
                bindWrapper(this.$element.find(selectors.wrapper), {
                    type: settings.type || 'toggles',
                    defaultToggle: parseInt(settings.default_toggle, 10) || 0
                });
            }
        });

        window.elementorFrontend.hooks.addAction('frontend/element_ready/vybose-repeater-accordion.default', function ($scope) {
            new VyboseRepeaterAccordionHandler({ $element: $scope });
        });
    });
})(jQuery);
