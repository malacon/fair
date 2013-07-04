/**
 * User: cbaker
 * Date: 7/3/13
 * Time: 8:38 PM
 */

// Get the ul that holds the collection of times
var collectionHolder = $('ul.times');

// setup an "add a time" link
var $addTimeLink = $('<a href="#" class="add_time_link">Add a time</a>');
var $newLinkLi = $('<li></li>').append($addTimeLink);
jQuery(document).ready(function() {
    collectionHolder.find('li').each(function() {
        addTimeDeleteLink($(this));
    });
    var initDatePicker = function(){

        if($.datepicker.regional['en_US'] != undefined ){
            $.datepicker.setDefaults( $.datepicker.regional['en_US'] );
        }else if($.datepicker.regional['en'] != undefined){
            $.datepicker.setDefaults( $.datepicker.regional['en'] );
        }else{
            $.datepicker.setDefaults( $.datepicker.regional['']);
        }

        $('.cbDatePicker').each(function(){
            var id_input=this.id.split('_datepicker')[0];
            var sfInput = $('#'+id_input)[0];
            if(! (sfInput)){
                console.error('An error has occurred while creating the datepicker');
            }
            $(this).datepicker({
                'yearRange':$(this).data('yearrange'),
                'changeMonth':$(this).data('changemonth'),
                'changeYear':$(this).data('changeyear'),
                'altField' : '#'+id_input,
                'altFormat' : 'yy-mm-dd',
                'minDate' : null,
                'maxDate': null
            });

            $(this).keyup(function(e) {
                if(e.keyCode == 8 || e.keyCode == 46) {
                    $.datepicker._clearDate(this);
                    $('#'+id_input)[0].value = '';
                }
            });
            var dateSf = $.datepicker.parseDate('yy-mm-dd',sfInput.value);

            $(this).datepicker('setDate',dateSf);
            $(this).show();
            $(sfInput).hide();
        })
    }
    initDatePicker();

    // add the "add a time" anchor and li to the times ul
    collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    collectionHolder.data('index', collectionHolder.find(':input').length);

    $addTimeLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new time form (see next code block)
        addTimeForm(collectionHolder, $newLinkLi);
    });

    function addTimeForm(collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = collectionHolder.data('prototype');

        // get the new index
        var index = collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a time" link li
        var $newFormLi = $('<li></li>').append(newForm);
        console.log(newForm)
        $newLinkLi.before($newFormLi);

        addTimeDeleteLink($newLinkLi);
    }

    function addTimeDeleteLink($timeFormLi) {
        var $removeFormA = $('<a href="#"><i class="icon-remove-sign"></i></a>')
        $timeFormLi.children('div').append($removeFormA);

        $removeFormA.on('click', function(e) {
            e.preventDefault();
            $timeFormLi.remove();
        });
    }
});