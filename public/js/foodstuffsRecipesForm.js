let rowId = $('.foodstuff-recipe-table tr').length - 2;

$('#add-foodstuff').on('click', function (event) {
    rowId = rowId + 1;
    let html = $('#row-row-idf')[0].outerHTML;
    html = html.replaceAll('-row-idf', rowId);
    $('#add-foodstuff-recipe-button-row').before(html);
    event.preventDefault();
});

$('#add-recipe').on('click', function (event) {
    rowId = rowId + 1;
    let html = $('#row-row-idr')[0].outerHTML;
    html = html.replaceAll('-riw-idr', rowId);
    $('#add-foodstuff-recipe-button-row').before(html);
    event.preventDefault();
});

$('.foodstuff-recipe-form').on('submit', function (event) {
    let foodstuffNames = [];
    let text = '';
    $('.foodstuff-name').each(function() {
        if (foodstuffNames.includes($(this).val())) {
            text = text + 'Dubbel voedingsmiddel. <br>';
            event.preventDefault();
        }
        foodstuffNames.push($(this).val());
    });
    if ($(this)[0] === $('form[name="foodstuff_from_foodstuffs"]')[0]) {
        let sum = 0;
        $('input[name="foodstuff_from_foodstuffs[foodstuffWeights][]"]').each(function() {
            if ($(this).attr('id') !== 'weight-row-idf') {
                sum = sum + parseFloat($(this).val().replace(',', '.'));
            }
        });
        if (Math.round(sum * 100) !== 10000) {
            text = text + 'De gewichten zijn samen niet gelijk aan 100 procent. <br>';
            event.preventDefault();
        }
    }

    let recipeNames = [];
    $('.recipe-name').each(function() {
        if (recipeNames.includes($(this).val())) {
            text = text + 'Dubbel recept.';
            event.preventDefault();
        }
        recipeNames.push($(this).val());
    });
    $('#form-errors-client').html(text);
});

$(document).on('input', ".foodstuff-name", function () {
    let form;
    if ($('form[name="day"]').length > 0) {
        form = 'day';
    } else if ($('form[name="standard_day"]').length > 0) {
        form = 'standard_day';
    } else if ($('form[name="recipe"]').length > 0) {
        form = 'recipe';
    } else {
        form = 'foodstuff_from_foodstuffs';
    }
    let options = $('#' + form + '_foodstuffs option');
    let valueInput = $(this).val().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    let rowId = $(this).attr('id').replace('foodstuff-name', '');
    let searchResults = $('#search-result' + rowId);
    searchResults.html('');

    if (valueInput !== '') {
        options.each(function () {
            let value = $(this).val();
            let nameOriginal = $(this).text();
            let name = $(this).text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            if (valueInput === name) {
                $('#foodstuff-value' + rowId).val(value);
            }
            if ($('#search-result' + rowId + ' .foodstuff-div').length > 20) {
            } else {
                if (name.includes(valueInput)) {
                    searchResults.html(searchResults.html() +
                        '<div id="foodstuff-div' + value + '" data-row="' + rowId +
                        '" class="foodstuff-div">' + nameOriginal + '</div>');
                }
            }
        });
    }
});

$(document).on('input', ".recipe-name", function () {
    let valueInput = $(this).val().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    let rowId = $(this).attr('id').replace('recipe-name', '');
    let searchResults = $('#search-result' + rowId);
    searchResults.html('');
    let errorField = $('#form-errors-client');
    errorField.text('');

    if (valueInput !== '' && valueInput.length > 0 && valueInput.length < 255) {
        $.ajax({
            url: '/recept/zoeken/' + rowId + '/' + valueInput,
            type: 'GET',
            success: function (data) {
                searchResults.html(data);
                $('.recipe-div').each(function () {
                    let id = $(this).attr('id').replace('recipe-div', '');
                    let name = $(this).text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    if (valueInput === name) {
                        $('#recipe-value' + rowId).val(id);
                    }
                });
            },
            error: function () {
                searchResults.html('Er ging iets fout.');
            }
        });
    } else if (valueInput.length > 255) {
        errorField.text('De zoekterm mag niet meer dan 255 tekens bevatten.');
    }
});

$(document).on('click', ".foodstuff-div", function () {
    let value = $(this).attr('id').replace('foodstuff-div', '');
    let name = $(this).text();
    let rowId = $(this).data('row');
    $('#foodstuff-value' + rowId).val(value);
    $('#foodstuff-name' + rowId).val(name);
    $('#search-result' + rowId).hide();
});

$(document).on('click', ".recipe-div", function () {
    let value = $(this).attr('id').replace('recipe-div', '');
    let name = $(this).text();
    let rowId = $(this).data('row');
    $('#recipe-value' + rowId).val(value);
    $('#recipe-name' + rowId).val(name);
    $('#search-result' + rowId).hide();
});

$(document).on('click', ".remove-row", function () {
    let rowId = $(this).attr('id').replace('remove-row', '');
    $('#row' + rowId).remove();
});

$("#day_foodstuffs option:selected").removeAttr("selected");
$("#standard_day_foodstuffs option:selected").removeAttr("selected");
$("#recipe_foodstuffs option:selected").removeAttr("selected");
$("#foodstuff_from_foodstuffs_foodstuffs option:selected").removeAttr("selected");
$('#day_foodstuffWeights').remove();
$('#day_recipeWeights').remove();
$('#standard_day_foodstuffWeights').remove();
$('#standard_day_recipeWeights').remove();
$('#foodstuff_from_foodstuffs_foodstuffWeights').remove();
$('#recipe_foodstuffWeights').remove();
