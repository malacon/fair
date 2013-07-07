/**
 * User: cbaker
 * Date: 7/3/13
 * Time: 8:38 PM
 */

// Get the ul that holds the collection of bakedItems

var collectionHolder1 = $('ul.bakedItems');
var collectionHolder2 = $('ul.auctionItems');

// setup an "add a baked item" link
var $addBakedItemLink = $('<a href="#" class="add_baked_item_link">Add a baked good</a>');
var $addAuctionItemLink = $('<a href="#" class="add_baked_item_link">Add a auction item</a>');
var $newLinkLi1 = $('<li></li>').append($addBakedItemLink);
var $newLinkLi2 = $('<li></li>').append($addAuctionItemLink);

jQuery(document).ready(function() {
    collectionHolder1.find('li').each(function() {
        addBakedItemDeleteLink($(this));
    });
    collectionHolder2.find('li').each(function() {
        addBakedItemDeleteLink($(this));
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

        addBakedItemDeleteLink($newFormLi);
    }

    function addBakedItemDeleteLink($itemFormLi) {
        var $removeFormA = $('<a href="#"><i class="icon-remove-sign"></i></a>')
        $itemFormLi.append($removeFormA);

        $removeFormA.on('click', function(e) {
            e.preventDefault();
            $itemFormLi.remove();
        });
    }
});