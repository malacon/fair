function getUserList($query, $process) {
    var url = $('.search-query').data('action'),
        userData;
    $.post(url, {user: $query}, function(data) {
        var length = data.length, i = 0, usernames = [];
        for ( ; i < length; i++) {
            if (data[i]['roles'].length === 0) {
                usernames.push(data[i]['username']);
            }
        }
        $process(usernames);
    });
    return userData;
}

jQuery(document).ready(function() {
    $('.search-query').typeahead({source: getUserList}).tooltip({placement: 'bottom'});

    $('.navbar-search').on('submit', function(e) {
        var $this = $(this),
            $searchQuery = $('.search-query'),
            username = $searchQuery.val(),
            url = $searchQuery.data('check');
        e.preventDefault();
        $.post(url, {user: username}, function(data) {
            if (data.isUser) {
                window.location = $('.search-query').data('target') + '?_switch_user=' + username;
            }
        });
    });

    $('.fileBlock').on('click', '.btn-edit', function(e) {
        var form,
            label;
        e.preventDefault();
        form = $(this).parent().parent().siblings('.fileName').data('edit-form');
        label = $(this).parent().parent().siblings('.fileName').html();
        $(this).parent().parent().siblings('.fileName').html(form);
    });

    $('.isrun').on('click', function(e) {
        e.preventDefault();
        var c = confirm('You have already run this file, are you sure you want to run it again?');
        if (c) {
            window.location = $(this).attr('href');
        }
    });

    $('.delete-rule').on('click', function(e) {
        e.preventDefault();
        var c = confirm('Are you sure you want to delete this?');
        if (c) {
            $(this).parent().parent().submit();
        }
    });
});