/**
 * User: cbaker
 * Date: 7/3/13
 * Time: 8:38 PM
 */

$(function() {
    $('[data-time-id]').on('click', 'a', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        if (!$this.hasClass('disabled')) {
            $.post(url, null, updateButtons);
        }

        return false;
    });

    $('.bakedItems').on('click', 'a', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        if (!$this.hasClass('disabled')) {
            $.post(url, null, updateBakedButtons);
        }
    });

    $('.auctionItems').on('click', '.close', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        $.post(url, null, updateAuctionButtons);
    });

    $('#auctionGoods').on('click', '.auction-add', function(e) {
        var $this = $(this),
            url = $this.attr('href');

        e.preventDefault();
        $.post(url, null, updateAuctionButtons);
    });

});

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

        if (data.timeWorked) {
            $this.find('.attend-toggle').addClass('disabled').text('Already Working at this Time');
        }
        else if (id !== parseInt(data.id, 10) && data.userAdded) {
            $this.find('.attend-toggle').addClass('disabled').text('Currently Occupied');
        }
        else if (data.userRemoved) {
            $this.find('.attend-toggle').removeClass('disabled').text('Signup');
        }
    });
}
function updateButtons(data) {
    if (data.userChanged) {
        $('[data-time-id='+data.id+'] .attend-toggle').toggleClass('hidden', data.userAdded);
        $('[data-time-id='+data.id+'] .not-attend-toggle').toggleClass('hidden',data.userRemoved);
        $('.hours').text(data['quantities']['hours']);
        $('#isPassed').toggleClass('alert-danger', !data.isPassed).toggleClass('alert-success', data.isPassed);

        updateDisabledSubmitButtons(data);
    } else if (data.timeFilled && !data.timeWorked) {
        $('[data-time-id='+data.id+']').find('.attend-toggle').addClass('disabled').text('Currently Occupied');
    }
}

function updateAuctionButtons(data) {
    $('#isPassed').toggleClass('alert-danger', !data.isPassed).toggleClass('alert-success', data.isPassed);
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

//        $div.append($a);

        $('.auctionItems').append($div);
    }
}

function updateBakedButtons(data) {
    var bakedItemButtons = $('[data-baked-id]'),
        btnBakingTXT = 'You are baking:',
        btnUnavailableTXT = 'is Unavailable';

    // Update the hours counter
    $('#isPassed').toggleClass('alert-danger', !data.isPassed).toggleClass('alert-success', data.isPassed);
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
