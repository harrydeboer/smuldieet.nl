let rowId = $('.createUpdateTable tr').length - 2;

$('#addFoodstuff').on('click', function () {
    rowId = rowId + 1;
    let html = $('#rowROW_IDF')[0].outerHTML;
    html = html.replaceAll('ROW_IDF', rowId);
    $('.createUpdateTable tr:last').before(html);
});

$('#addRecipe').on('click', function () {
    rowId = rowId + 1;
    let html = $('#rowROW_IDR')[0].outerHTML;
    html = html.replaceAll('ROW_IDR', rowId);
    $('.createUpdateTable tr:last').before(html);
});

$('form[name="recipe"], form[name="day"], form[name="standard_day"], form[name="cookbook"], ' +
    'form[name="foodstuff_from_foodstuffs"]').on('submit', function (event) {
    let foodstuffNames = [];
    let text = '';
    $('.foodstuffName').each(function() {
        if (foodstuffNames.includes($(this).val())) {
            text = text + 'Dubbel voedingsmiddel. <br>';
            event.preventDefault();
        }
        foodstuffNames.push($(this).val());
    });
    if ($(this)[0] === $('form[name="foodstuff_from_foodstuffs"]')[0]) {
        let sum = 0;
        $('input[name="foodstuff_from_foodstuffs[foodstuffWeights][]"]').each(function() {
            if ($(this).attr('id') !== 'weightROW_IDF') {
                sum = sum + parseFloat($(this).val().replace(',', '.'));
            }
        });
        if (Math.round(sum * 100) !== 10000) {
            text = text + 'De gewichten zijn samen niet gelijk aan 100 procent. <br>';
            event.preventDefault();
        }
    }

    let recipeNames = [];
    $('.recipeName').each(function() {
        if (recipeNames.includes($(this).val())) {
            text = text + 'Dubbel recept.';
            event.preventDefault();
        }
        recipeNames.push($(this).val());
    });
    $('#formErrorsClient').html(text);
});

$(document).on('input', ".foodstuffName", function () {
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
    let rowId = $(this).attr('id').replace('foodstuffName', '');
    let searchResults = $('#searchResult' + rowId);
    searchResults.html('');

    if (valueInput !== '') {
        options.each(function () {
            let value = $(this).val();
            let nameOriginal = $(this).text();
            let name = $(this).text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            if (valueInput === name) {
                $('#foodstuffValue' + rowId).val(value);
            }
            if ($('#searchResult' + rowId + ' .foodstuffDiv').length > 20) {
            } else {
                if (name.includes(valueInput)) {
                    searchResults.html(searchResults.html() +
                        '<div id="foodstuffDiv' + value + '" data-row="' + rowId +
                        '" class="foodstuffDiv">' + nameOriginal + '</div>');
                }
            }
        });
    }
});

$(document).on('input', ".recipeName", function () {
    let valueInput = $(this).val().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    let rowId = $(this).attr('id').replace('recipeName', '');
    let searchResults = $('#searchResult' + rowId);
    searchResults.html('');
    let errorField = $('#formErrorsClient');
    errorField.text('');

    if (valueInput !== '' && valueInput.length > 0 && valueInput.length < 255) {
        $.ajax({
            url: '/recept/zoeken/' + rowId + '/' + valueInput,
            type: 'GET',
            success: function (data) {
                searchResults.html(data);
                $('.recipeDiv').each(function () {
                    let id = $(this).attr('id').replace('recipeDiv', '');
                    let name = $(this).text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    if (valueInput === name) {
                        $('#recipeValue' + rowId).val(id);
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

$(document).on('click', ".foodstuffDiv", function () {
    let value = $(this).attr('id').replace('foodstuffDiv', '');
    let name = $(this).text();
    let rowId = $(this).data('row');
    $('#foodstuffValue' + rowId).val(value);
    $('#foodstuffName' + rowId).val(name);
    $('#searchResult' + rowId).hide();
});

$(document).on('click', ".recipeDiv", function () {
    let value = $(this).attr('id').replace('recipeDiv', '');
    let name = $(this).text();
    let rowId = $(this).data('row');
    $('#recipeValue' + rowId).val(value);
    $('#recipeName' + rowId).val(name);
    $('#searchResult' + rowId).hide();
});

$(document).on('click', ".removeRow", function () {
    let rowId = $(this).attr('id').replace('removeRow', '');
    $('#row' + rowId).remove();
});

$("#day_foodstuffs option:selected").removeAttr("selected");
$("#standard_day_foodstuffs option:selected").removeAttr("selected");
$("#recipe_foodstuffs option:selected").removeAttr("selected");
$('#day_foodstuffWeights').remove();
$('#day_recipeWeights').remove();
$('#standard_day_foodstuffWeights').remove();
$('#standard_day_recipeWeights').remove();
$('#foodstuff_from_foodstuffs_foodstuffWeights').remove();
$('#recipe_foodstuffWeights').remove();
