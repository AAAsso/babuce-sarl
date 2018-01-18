$(document).ready(function () {
    $('.table.aaa-table > tbody > tr').click(function() {
        var link = $(this).data('url-show');
        window.location.href = link;
    });
});