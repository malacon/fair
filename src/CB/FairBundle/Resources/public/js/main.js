if (typeof console == "undefined") {
    this.console = {log: function() {}};
}
$(function() {
    $('.content-item .help').popover({trigger: 'hover', html: true, container: 'body'});
    $('.btn-booth').button();
    $('[data-toggle="button"]').button('toggle');
});
