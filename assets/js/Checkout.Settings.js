if (Checkout === undefined) {
    var Checkout = {};
}

Checkout.Settings = (function($) {
    function init() {
        startObservers();
    }

    function startObservers()
    {
        var countriesSelect = jQuery('.moloni-multiple-select');

        if (countriesSelect.length && countriesSelect.select2) {
            countriesSelect.select2();
        }

        var isRequired = jQuery('#drop_down_is_required');

        if (isRequired.length) {
            isRequired
            .on('click', function () {
                if (isRequired.val() === '2') {
                    countriesSelect.closest('tr').show();
                } else {
                    countriesSelect.closest('tr').hide();
                }
            })
            .trigger('click');
        }
    }

    return {
        init: init,
    }
}(jQuery));
