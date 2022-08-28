$(function() {
    $('.delete-modal-button').on('click', function () {
        $('.delete-modal').modal('show');
    });

    $('#rate-modal-button').on('click', function () {
        $('#rate-modal').modal('show');
    });

    $('.radio-star').on('change', function () {
        $('form[name="rating"]').trigger("submit")
    });


    $(".dropdown-diet").on('click', function(e) {
        e.stopPropagation();
    })

    $('#uploadFileButton').on('click', function (event) {
        $(this).next()[0].click();
        event.preventDefault();
    });

    $('#recipe-search-icon').on('click', function (event) {
        $(this).next()[0].click();
        event.preventDefault();
    });

    $('#abc-select').on('change', function () {
        window.location.href = $('#abc-route').data('route') + '/' + $(this).val();
    });

    $('form[name="contact"]').on('submit', function (event) {
        if ($('#contact-re-captcha-token').val() === '') {
            // noinspection JSUnresolvedVariable
            grecaptcha.ready(function () {
                // noinspection JSUnresolvedVariable,JSUnresolvedFunction
                grecaptcha.execute($('#re-captcha-key').data('key'), {action: 'contact'}).then(function (token) {
                    $('#contact-re-captcha-token').val(token);
                    $('[name="contact"]').trigger('submit');
                });
            });
            event.preventDefault();
        }
    });

    if ($('input[name="recipe[isSelfInvented]"]:checked').val() === '0') {
        $('#recipe-source').show();
    }
    $('input[name="recipe[isSelfInvented]"]').on('change', function () {
        if ($(this).val() === '1') {
            $('#recipe-source').hide();
        } else {
            $('#recipe-source').show();
        }
    });

    let recipesHomepage = $("#recipes-homepage");
    if (recipesHomepage.length > 0 && !($('#recent-recipes-heading').length > 0)) {
        $('html, body').animate({
            scrollTop: recipesHomepage.offset().top
        }, 1000);
    }
    $('#recipe-filter-icon').on('click', function() {
        let div = $('#filter-sort-div');
        if (div.hasClass('d-none')) {
            div.removeClass('d-none');
        } else {
            div.addClass('d-none');
        }
    });

    $('form[name="rating"]').on('submit', function (event) {
        let rating = $('#rating_rating');
        rating.text('')
        if (rating.val() < 1 || rating.val() > 10) {
            $('#form-errors-client').text('The waardering moet tussen 1 en 10 zijn.')
            event.preventDefault();
        }
    });

    let form = $('.food-form');
    if (form.length > 0) {
        new FoodForm(form);
    }
});
