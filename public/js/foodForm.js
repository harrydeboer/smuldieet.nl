class FoodForm {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#form-errors-client');
        this.options = $('#' + this.formName + '_foodstuffs option');

        $('#add-foodstuff').on('click', this.addRow.bind(this, 'foodstuff'));
        $('#add-recipe').on('click', this.addRow.bind(this, 'recipe'));
        form.on('input', ".foodstuff-name", this.foodNameInput.bind(this, 'foodstuff'));
        form.on('input', ".recipe-name", this.foodNameInput.bind(this, 'recipe'));
        form.on('click', ".foodstuff-div", this.foodDivClick.bind(this, 'foodstuff'));
        form.on('click', ".recipe-div", this.foodDivClick.bind(this, 'recipe'));
        form.on('click', ".remove-row", this.removeRow.bind(this));
        form.on('submit', this.submit.bind(this));
    }

    addRow(foodType, event) {
        let html = '<tr><td>';
        html = html + $('#' + this.formName + '_' + foodType + 'Dropdown').data('prototype') + '</td><td>';
        html = html + $('#' + this.formName + '_' + foodType + 'Weights').data('prototype');
        html = html + '</td><td><i class="remove-row fa fa-minus"></i></td></tr>';
        html = html.replace('<label for="' + this.formName + '_' + foodType +
            'Weights___name__" class="required">__name__label__</label>', '');
        html = html.replaceAll('__name__', '');
        html = html.replace('__required__', 'required')
        $('#add-foodstuff-recipe-button-row').before(html);
        if (foodType === 'recipe') {
            $('#' + this.formName + '_recipeWeights_').val(1);
        }
        $('#' + this.formName + '_' + foodType + 'Weights_').attr('id', '');
        event.preventDefault();
    }

    foodNameInput(foodType, event) {
        let thisElement = $(event.target);
        let row = new FoodRow(event.target);
        let valueInput = thisElement.val().toLowerCase().normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
        let searchResults = row.getSearchResults();
        searchResults.html('');
        let url;
        if (foodType === 'foodstuff') {
            url = $('#foodstuffSearch').data('search').replace('__name__', valueInput);
        } else {
            url = $('#recipeSearch').data('search').replace('__title__', valueInput);
        }

        if (valueInput !== '') {
            $.ajax({
                url:  url,
                type: 'GET',
                success: (data) => {
                    searchResults.html(data);
                    $('.food-div').each((index, element) => {
                        let id = $(element).attr('id').replace(foodType + '-div', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            row.getWeight().attr('name', this.formName + '[' + foodType + 'Weights][' + id + ']');
                        }
                    });
                },
                error: () => {
                    searchResults.html('Er ging iets fout.');
                }
            });
        }
    }

    foodDivClick(foodType, event) {
        let thisElement = $(event.target);
        let row = new FoodRow(event.target);
        let id = thisElement.attr('id').replace(foodType + '-div', '');
        let name = thisElement.text();
        row.getWeight().attr('name', this.formName + '[' + foodType + 'Weights][' + id + ']')
        row.getName().val(name);
    }

    removeRow(event) {
        new FoodRow(event.target).row.remove();
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

class FoodRow {
    constructor(thisElement) {
        this.row = $(thisElement).closest('tr');
    }

    getName() {
        return this.row.find('.dropdown-toggle');
    }

    getWeight() {
        return this.row.find('.food-weight');
    }

    getSearchResults() {
        return this.row.find('.dropdown-menu-food')
    }
}

$(function() {
    let form = $('.food-form');
    if (form.length > 0) {
        new FoodForm(form);
    }
});
