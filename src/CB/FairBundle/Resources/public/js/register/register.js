/**
 * User: cbaker
 * Date: 7/3/13
 * Time: 8:38 PM
 */

// Get the ul that holds the collection of bakedItems

var collectionHolder1 = $('.bakedItems');
var collectionHolder2 = $('.auctionItems');

// setup an "add a baked item" link
var $addBakedItemLink = $('<a href="#" class="add_baked_item_link">Add a baked good</a>');
var $addAuctionItemLink = $('<a href="#" class="add_auction_item_link">Add a auction item</a>');
var $newLinkLi1 = $('<li></li>').append($addBakedItemLink);
var $newLinkLi2 = $('<li></li>').append($addAuctionItemLink);

$(function() {
    collectionHolder1.find('li').each(function() {
        addItemDeleteLink($(this));
    });
    collectionHolder2.find('li').each(function() {
        addItemDeleteLink($(this));
    });

    // add the "add a baked item" anchor and li to the bakedItems ul
    collectionHolder1.append($newLinkLi1);
    collectionHolder2.append($newLinkLi2);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    collectionHolder1.data('index', collectionHolder1.find(':input').length);
    collectionHolder2.data('index', collectionHolder2.find(':input').length);

    $addBakedItemLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new baked item form (see next code block)
        addItemForm(collectionHolder1, $newLinkLi1);
    });

    $addAuctionItemLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new baked item form (see next code block)
        addItemForm(collectionHolder2, $newLinkLi2);
    });

    $('[data-time-id]').on('click', 'a', function(e) {
        var $this = $(this),
            url = $this.attr('href') + '.json';

        e.preventDefault();
        $.post(url, null, updateButtons);

        return false;
    });

    function addItemForm(collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = collectionHolder.data('prototype');

        // get the new index
        var index = collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);


        // increase the index with one for the next item
        collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a baked item" link li
        var $newFormLi = $('<li></li>').append(newForm);

        $newLinkLi.before($newFormLi);

        addItemDeleteLink($newFormLi);
        submitEnabled();
    }

    function addItemDeleteLink($itemFormLi) {
        var $removeFormA = $('<a href="#"><i class="icon-remove-sign"></i></a>')
        $itemFormLi.append($removeFormA);

        $removeFormA.on('click', function(e) {
            e.preventDefault();
            $itemFormLi.remove();
            submitEnabled()
        });
    }
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

function submitEnabled() {
    var saveTopButton = $('#cb_user_registration_saveTop'),
        saveBottomButton = $('#cb_user_registration_saveBottom');

    saveTopButton.removeClass('disabled').addClass('btn-primary').text('Click here to save your items');
    saveBottomButton.removeClass('disabled').addClass('btn-primary').text('Click here to save your items');
}