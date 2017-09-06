(function () {
    /**
     * Display only the week days available for the selected period
     */
    function handleRange(element) {
        var onAction;
        var containerEl = document.getElementById(element.getAttribute('data-container'));
        var startAtEl = document.getElementById(element.getAttribute('data-start'));
        var endAtEl = document.getElementById(element.getAttribute('data-end'));
        var rangeEls = containerEl.querySelectorAll('.opening-form');

        function onAction(element) {
            var startAt = $(startAtEl).data('DateTimePicker').date();
            var endAt = $(endAtEl).data('DateTimePicker').date();

            for (var i = 0; i < rangeEls.length; i++) {
                if (rangeEls[i].classList.contains('hide') === false) {
                    rangeEls[i].classList.add('hide'); // Hide all ranges
                }
            }

            if (endAt >= startAt) {
                var x = 7; // Useless to loop more than 7 times
                var showRangeEls = [];

                for (var dayIterator = moment(startAt); dayIterator <= endAt; dayIterator.add(1, 'days')) {
                    if (x === 0) break;
                    for (var i = 0; i < rangeEls.length; i++) {
                        if (rangeEls[i].classList.contains('range-' + dayIterator.day())) {
                            rangeEls[i].classList.remove('hide');
                            showRangeEls.push(rangeEls[i]); // Build array of showed elements
                        }
                    }
                    x--;
                }

                $.each($(rangeEls).not(showRangeEls).get(), function () {
                    $(this).find('select').each(function () { // Reset ONLY old ranges
                            $(this).val('');
                        }
                    );
                });
            }
        };

        $(startAtEl).on('dp.change', function (event) { onAction(this); });
        $(endAtEl).on('dp.change', function (event) { onAction(this); });
        onAction(element);
    }

    var elements = document.querySelectorAll('[role=range][data-start][data-end][data-container]');
    for (var i = 0; i < elements.length; i++) {
        handleRange(elements[i]);
    }
})();
