$(function() {
    $('.delete-modal-button').on('click', function () {
        $('.delete-modal').modal('show');
    });

    $('#login-form').on('submit', function () {
        let button = $('#login-submit-button');
        button.prop('disabled', true);
        button.text('Even geduld...');
    });

    $('#rate_modal_button').on('click', function () {
        $('#rate_modal').modal('show');
    });

    $('.radio-star').on('change', function () {
        $('form[name="rating"]').trigger("submit")
    });

    $(".dropdown-diet").on('click', function(e) {
        e.stopPropagation();
    })

    $('#upload_file_button').on('click', function (event) {
        $(this).next()[0].click();
        event.preventDefault();
    });

    $('#recipe_search_icon').on('click', function (event) {
        $(this).next()[0].click();
        event.preventDefault();
    });

    $('#abc_select').on('change', function () {
        window.location.href = $('#abc_route').data('route') + '/' + $(this).val();
    });

    $('form[name="contact"]').on('submit', function (event) {
        if ($('#contact_re_captcha_token').val() === '') {
            // noinspection JSUnresolvedVariable
            grecaptcha.ready(function () {
                // noinspection JSUnresolvedVariable,JSUnresolvedFunction
                grecaptcha.execute($('#re_captcha_key').data('key'), {action: 'contact'}).then(function (token) {
                    $('#contact_re_captcha_token').val(token);
                    $('[name="contact"]').trigger('submit');
                });
            });
            event.preventDefault();
        }
    });

    /**
     * The is self invented radio button shows a source field when it is not self invented.
     */
    if ($('input[name="recipe[is_self_invented]"]:checked').val() === '0') {
        $('#recipe_source').removeClass('d-none');
    }
    $('input[name="recipe[is_self_invented]"]').on('change', function () {
        if ($(this).val() === '1') {
            $('#recipe_source_title').addClass('d-none');
            $('#recipe_source').addClass('d-none');
        } else {
            $('#recipe_source_title').removeClass('d-none');
            $('#recipe_source').removeClass('d-none');
        }
    });

    /**
     * After a recipe search has been performed on the homepage the browser animates to the search results.
     */
    let recipesHomepage = $("#recipes_homepage");
    if (recipesHomepage.length > 0 && !($('#recent_recipes_heading').length > 0)) {
        $('html, body').animate({
            scrollTop: recipesHomepage.offset().top
        }, 1000);
    }

    $('#recipe_filter_icon').on('click', function() {
        let div = $('#filter_sort_div');
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
            $('#form_errors_client').text('The waardering moet tussen 1 en 10 zijn.')
            event.preventDefault();
        }
    });

    let foodstuffsForm = $('.foodstuffs-form');
    if (foodstuffsForm.length > 0) {
        new FoodstuffsForm(foodstuffsForm);
    }

    let recipesForm = $('.recipes-form');
    if (recipesForm.length > 0) {
        new RecipesForm(recipesForm);
    }

    $('#add_tag').on('click', function (event) {
        let html = '<tr><td>' + $('#recipe_tags_array').data('prototype') +
            '</td><td><i class="remove-tag-row fa fa-minus"></i></td></tr>';
        html = html.replaceAll(/<label[\s\S]+?<\/label>/g, '');
        $('#add_tag_button_row').before(html);
        let prototype = $('#recipe_tags_array___name__');
        prototype.attr('name', 'recipe[tags_array][]')
        prototype.removeAttr('id');
        event.preventDefault();
    });

    foodstuffsForm.on('click', '.remove-tag-row', function (event) {
        let row = $(event.target).closest('tr');
        row.remove();
    });
});
