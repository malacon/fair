/**
 * User: cbaker
 * Date: 7/3/13
 * Time: 8:38 PM
 */
$(function() {
    var $booths = $('#booths'),
        $boothDisplay = $('.boothDisplay'),
        $loadingBooth = $("#loadingBooth");

    $('#instructions').modal('show');

    // Booth Navigation
    $('.boothSelection li').on('click', 'a', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        $this.parent().siblings('li').removeClass('active');
        $this.parent().addClass('active');
        if (!$this.hasClass('active')) {
            loadingBooth(url, true);
        }
    });

    // Spouse navigation
    $booths.on('click', '.spouse.btn', function(e) {
        var $this = $(this),
            url = $('.boothSelection .nav').find('.active a').attr('href');

        e.preventDefault();

        $this.siblings('.btn').removeClass('active');
        $this.addClass('active');

        loadingBooth(url, true);
        if ($this.hasClass('active')) {
            loadingBooth(url, true);
        }

    });

    // Submit booth time
    $('.boothData').on('click', 'a.btn-booth', function(e) {
        var $this = $(this),
            url = $this.attr('href'),
            spouseId = $('.spouse.disabled').data('spouse-id');

        e.preventDefault();
        if (!$this.hasClass('disabled')) {
            loadingBooth(url, false);
        }

        return false;
    });

    // Update baked item
    $('.bakedItems').on('click', 'a', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        if (!$this.hasClass('disabled')) {
            $.post(url, null, updateBakedButtons);
        }
    });

    // Remove Auction Item
    $('.auctionItems').on('click', '.close', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        $.post(url, null, updateAuctionButtons);
    });

    // Add Auction Item
    $('.content-item').on('click', '.auction-add', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        $.post(url, null, updateAuctionButtons);
    });

    // Show User Report for printing
    $('#modal-status').on('show', function() {
        var url = $(this).data('href');

        $.get(url, updateUserStatusReport);
    })

    function updateUserStatusReport(data) {
        var $status = $('#modal-status'),
            $body = $status.children('.modal-body'),
            $timeList = $('.modal-times'),
            $booths = $('<ul></ul>');

        $body.html(data);
    }

    function loadingBooth(url, fade)
    {
        var spouseId = $('.btn-group-spouses .active').data('spouse-id');

        fade = fade || false;
        i = 0;
        if (fade) {
            $boothDisplay.fadeOut('slow');
        }
        $loadingBooth.removeClass('hidden');
        setInterval(function() {
            i = ++i % 4;
            $loadingBooth.html("Checking"+Array(i+1).join("."));
        }, 500);

        $.getJSON(url, {spouse: spouseId}, function(data) {
            updateStatus(data);
            updateHourCounter(data);
            console.log(data);
            $('.hours').text(data['quantities']['hours']);
            $loadingBooth.addClass('hidden');
            $boothDisplay.html(data.html).fadeIn('slow');
        });
    }


    /**
     * Will cycle through the submit buttons and will disable if the times are the same as the current times
     *
     * @param data
     */
    function updateDisabledSubmitButtons(data) {
        // for each data.timestamps
        //   compare them to each list's timestamp
        //      if they are equal and the IDs don't match, then disable the button
        $('[data-timestamp='+data.timestamp+']').each(function() {
            var $this = $(this),
                id = $this.data('time-id');

            if (isWorking(data)) {
                $this.find('.attend-toggle').addClass('disabled').children('.msg').text('Already Working');
            } else if (isFilled()) {
                $this.find('.attend-toggle').addClass('disabled').children('.msg').text('Currently Occupied');
            } else if (isRemoved()) {
                $this.find('.attend-toggle').removeClass('disabled').addClass('btn-info').children('.msg').text('Signup');
            }

            function isWorking() { return data.timeWorked; }
            function isFilled() { return id !== parseInt(data.id, 10) && data.userAdded; }
            function isRemoved() { return data.userRemoved; }
        });
    }

    function updateStatus(data) {
        var $status = $('.status'),
            url = $status.data('url'),
            $isPassed = $('#isPassed');

        $isPassed.toggleClass('alert-danger', !data.isPassed).toggleClass('alert-success', data.isPassed);
        if (data.isPassed) {
            $status.addClass('btn-success').removeClass('btn-danger');
            $status.text($status.data('passed'));
            $isPassed.find('i.icon').addClass('icon-thumbs-up').removeClass('icon-exclamation-sign');
        } else {
            $status.removeClass('btn-success').addClass('btn-danger');
            $status.text($status.data('notpassed'));
            $isPassed.find('i.icon').removeClass('icon-thumbs-up').addClass('icon-exclamation-sign');
        }

    }

    function updateHourCounter(data) {
        // Clear out the hours
        $('span[data-booth-id]').text('0');
        // Update new hour values
        $.each(data.boothHours, function(id, data) {
            $('span[data-booth-id='+id+']').text(data);
        });

        $.each(data.spouseHours, function(id, data) {
            $('span[data-spouse-hour='+id+']').text(data);
        })
    }

    function updateAuctionButtons(data) {
        updateStatus(data);
        $('.auction').text(data.numAuctions);
        // IF removed, find the id and remove the tag
        if (data.isRemoved) {
            $('[data-auction-id='+data.id+']').remove();
        } else {
            var $div = $('<div data-auction-id='+data.id+'>'+data.description+'</div>')
                .addClass('auctionItem alert')
                .append($('<a>&times;</a>')
                    .addClass('close')
                    .attr('href', data.url));

            $('.auctionItems').append($div);
        }
    }

    function updateBakedButtons(data) {
        var bakedItemButtons = $('[data-baked-id]'),
            btnBakingTXT = 'You are baking:',
            btnUnavailableTXT = 'is Unavailable';

        // Update the hours counter
        updateStatus(data);
        $('.baked').text(data.isWorkerBaking?1:0);
        // Reset all buttons
        bakedItemButtons.each(function() {
            var $this = $(this),
                id = $this.data('baked-id'),
                description = $this.children('span');

            // Reset all buttons
            $this.removeClass();
            // set all buttons to btn-success
            if (id !== data.id || !data.isWorkerBaking) {
                $this.addClass('btn btn-success').html(description);
            } else {
                // Change the button you clicked
                $this.addClass('btn btn-warning').text(btnBakingTXT+' ').append(description);
            }
        });

        // Set all unavailable items
        $.each(data.unavailableItems, function() {
            if (this.id !== data.id) {
                $('[data-baked-id='+this.id+']').addClass('disabled').removeClass('btn-success').text(this.description + ' ' + btnUnavailableTXT);
            }
        });

    }

});

