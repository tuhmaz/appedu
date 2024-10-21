'use strict';

$(document).ready(function() {
    $('.filter-category').on('click', function(e) {
        e.preventDefault();

        var category = $(this).data('category');
        var database = '{{ $database }}';

        $.ajax({
            url: "{{ route('frontend.news.filter') }}",
            method: 'GET',
            data: {
                category: category,
                database: database,
            },
            success: function(response) {
                $('#news-list').html(response);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
});
