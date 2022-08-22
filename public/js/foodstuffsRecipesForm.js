class FoodstuffRecipeForm {

    constructor(form) {
        this.rowId = $('.foodstuff-recipe-table tr').length - 2;
        this.form = form;
        this.formName = this.form.attr('name');
        this.errors = $('#form-errors-client');
        this.options = $('#' + this.formName + '_foodstuffs option');

        $('#add-foodstuff').on('click', this.addFoodstuff.bind(this));
        $('#add-recipe').on('click', this.addRecipe.bind(this));

        this.form.on('input', ".foodstuff-name", this.foodstuffNameInput.bind(this));
        this.form.on('input', ".recipe-name", this.recipeNameInput.bind(this));
        this.form.on('click', ".foodstuff-div", this.foodstuffDivClick.bind(this));
        this.form.on('click', ".recipe-div", this.recipeDivClick.bind(this));
        this.form.on('click', ".remove-row", this.removeRow.bind(this));
        this.form.on('submit', this.submit.bind(this));
    }

    addFoodstuff(event) {
        this.rowId = this.rowId + 1;
        let placeholder;
        if (this.formName === 'foodstuff_from_foodstuffs') {
            placeholder = 'procent';
        } else {
            placeholder = 'gram/ml';
        }
        let html = '<tr id="row' + this.rowId + '"><td><div class="dropdown">' +
            '<input type="text" class="foodstuff-name form-control dropdown-toggle" maxlength="255"' +
            ' id="foodstuff-name' + this.rowId + '" value=""' +
            ' data-bs-toggle="dropdown" placeholder="voedingsmiddel" aria-expanded="false" required>' +
            '<div id="search-result' + this.rowId + '" class="dropdown-menu dropdown-menu-foodstuff"' +
            ' aria-labelledby="foodstuffName' + this.rowId + '">' +
            '</div></div></td>' +
            '<td><input id="weight' + this.rowId + '" type="text" name="' + this.formName + '[foodstuffWeights][]"' +
            ' placeholder="' + placeholder + '" class="form-control foodstuff-weight" required></td>' +
            '<td><i class="remove-row fa fa-minus" id="remove-row' + this.rowId + '"></i></td>' +
            '</tr>';
        $('#add-foodstuff-recipe-button-row').before(html);
        event.preventDefault();
    }

    addRecipe(event) {
        this.rowId = this.rowId + 1;
        let classes = 'form-control recipe-weight'
        if (this.formName === 'cookbook') {
            classes = classes + ' hidden-input';
        }
        let html = '<tr id="row' + this.rowId + '"><td><div class="dropdown">' +
            '<input type="text" class="recipe-name form-control dropdown-toggle" maxlength="255"' +
            ' id="recipe-name' + this.rowId + '" value=""' +
            ' data-bs-toggle="dropdown" placeholder="recept" aria-expanded="false" required>' +
            '<div id="search-result' + this.rowId + '" class="dropdown-menu dropdown-menu-recipe"' +
            ' aria-labelledby="recipeName' + this.rowId + '">' +
            '</div></div></td>' +
            '<td><input id="weight' + this.rowId + '" type="text" name="' + this.formName + '[recipeWeights][]"' +
            ' placeholder="aantal keer" class="' + classes + '" value="1" required></td>' +
            '<td><i class="remove-row fa fa-minus" id="remove-row' + this.rowId + '"></i></td>' +
            '</tr>';
        $('#add-foodstuff-recipe-button-row').before(html);
        event.preventDefault();
    }

    foodstuffNameInput(event) {
        let thisElement = $(event.target);
        let valueInput = thisElement.val().toLowerCase().normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
        let rowId = thisElement.attr('id').replace('foodstuff-name', '');
        let searchResults = $('#search-result' + rowId);
        searchResults.html('');

        if (valueInput !== '') {
            $.ajax({
                url: '/voedingsmiddel/zoeken/' + rowId + '/' + valueInput,
                type: 'GET',
                success: (data) => {
                    searchResults.html(data);
                    $('.foodstuff-div').each((index, element) => {
                        let id = $(element).attr('id').replace('foodstuff-div', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            $('#weight' + rowId).attr('name', this.formName +
                                '[foodstuffWeights][' + id + ']');
                        }
                    });
                },
                error: () => {
                    searchResults.html('Er ging iets fout.');
                }
            });
        }
    }

    recipeNameInput(event) {
        let thisElement = $(event.target);
        let valueInput = thisElement.val().toLowerCase().normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
        let rowId = thisElement.attr('id').replace('recipe-name', '');
        let searchResults = $('#search-result' + rowId);
        searchResults.html('');
        this.errors.text('');

        if (valueInput !== '' && valueInput.length > 0 && valueInput.length < 255) {
            $.ajax({
                url: '/recept/zoeken/' + rowId + '/' + valueInput,
                type: 'GET',
                success: (data) => {
                    searchResults.html(data);
                    $('.recipe-div').each((index, element) => {
                        let id = $(element).attr('id').replace('recipe-div', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            $('#weight' + rowId).attr('name', this.formName +
                                '[recipeWeights][' + id + ']');
                        }
                    });
                },
                error: () => {
                    searchResults.html('Er ging iets fout.');
                }
            });
        } else if (valueInput.length > 255) {
            this.errors.text('De zoekterm mag niet meer dan 255 tekens bevatten.');
        }
    }

    foodstuffDivClick(event) {
        let thisElement = $(event.target);
        let value = thisElement.attr('id').replace('foodstuff-div', '');
        let name = thisElement.text();
        let rowId = thisElement.data('row');
        $('#foodstuff-value' + rowId).val(value);
        $('#weight' + rowId).attr('name', this.formName +
            '[foodstuffWeights][' + value + ']');
        $('#foodstuff-name' + rowId).val(name);
        $('#search-result' + rowId).hide();
    }
    
    recipeDivClick(event) {
        let thisElement = $(event.target);
        let value = thisElement.attr('id').replace('recipe-div', '');
        let name = thisElement.text();
        let rowId = thisElement.data('row');
        $('#recipe-value' + rowId).val(value);
        $('#weight' + rowId).attr('name', this.formName +
            '[recipeWeights][' + value + ']');
        $('#recipe-name' + rowId).val(name);
        $('#search-result' + rowId).hide();
    }

    removeRow(event) {
        let rowId = $(event.target).attr('id').replace('remove-row', '');
        $('#row' + rowId).remove();
    }

    submit(event) {
        let text = '';

        let foodstuffNames = [];
        $('.foodstuff-name').each((index, element) => {
            if (foodstuffNames.includes($(element).val())) {
                text = text + 'Dubbel voedingsmiddel. <br>';
                event.preventDefault();
            }
            foodstuffNames.push($(element).val());
        });

        let recipeNames = [];
        $('.recipe-name').each((index, element) => {
            if (recipeNames.includes($(element).val())) {
                text = text + 'Dubbel recept.';
                event.preventDefault();
            }
            recipeNames.push($(element).val());
        });

        if (this.formName === "foodstuff_from_foodstuffs") {
            let sum = 0;
            $('.foodstuff-weight').each((index, element) => {
                if ($(element).attr('id') !== 'weight-row-idf') {
                    sum = sum + parseFloat($(element).val().replace(',', '.'));
                }
            });
            if (Math.round(sum * 100) !== 10000) {
                text = text + 'De gewichten zijn samen niet gelijk aan 100 procent. <br>';
                event.preventDefault();
            }
        }

        this.errors.html(text);
    }
}

let form = $('.foodstuff-recipe-form');
if (form.length > 0) {
    new FoodstuffRecipeForm(form);
}
