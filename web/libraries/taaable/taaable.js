$(document).ready(function () {
    /*
     * Redirect user on click on the row
     */
    $('.table.aaa-table > tbody > tr').click(function (event) {
        var link = $(this).data('url-show');

        // redirect only if user click on a row, not on a button or link or...
        if ($(event.target).is("td")) {
            window.location.href = link;
        }
    });

    /*
     * Add content of a long data as title
     * to allow user to preview it on hover
     */
    $('.table.aaa-table > tbody > tr > td.ellipsis').each(function (index) {
        $(this).attr('title', $(this).html());
    });
});