class FoodForm {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#form-errors-client');
        this.options = $('#' + this.formName + '_foodstuffs option');

        $('#add-foodstuff').on('click', this.addRow.bind(this, 'foodstuff'));
        $('#add-recipe').on('click', this.addRow.bind(this, 'recipe'));
        form.on('input', ".foodstuff-name", this.foodNameInput.bind(this, 'foodstuff'));
        form.on('input', ".recipe-name", this.foodNameInput.bind(this, 'recipe'));
        form.on('click', ".foodstuff-result", this.foodSearchResultClick.bind(this, 'foodstuff'));
        form.on('click', ".recipe-result", this.foodSearchResultClick.bind(this, 'recipe'));
        form.on('click', ".remove-row", this.removeRow.bind(this));
        form.on('submit', this.submit.bind(this));
    }

    /**
     * A row in the food form consists of a search dropdown, a weight and a minus icon for deletion of the row.
     * The label of the weight input is removed.
     * When the food type is a recipe then the weight value is set to 1.
     * The id is removed from the weight prototype.
     */
    addRow(foodType, event) {
        let html = '<tr><td>' + $('#' + this.formName + '_' + foodType + 'Dropdown').data('prototype') +
            '</td><td>';
        if (foodType === 'recipe') {
            html += $('#' + this.formName + '_' + foodType + 'Choices').data('prototype');
        } else {
            html += $('#' + this.formName + '_' + foodType + 'Weights').data('prototype');
        }
        html += '</td><td><span class="piece-name"></span></td><td><i class="remove-row fa fa-minus"></i></td></tr>';
        html = this.removeLabel(html);
        $('#add-foodstuff-recipe-button-row').before(html);
        if (foodType === 'recipe') {
            let options = $('#' + this.formName + '_recipeChoices___name__');
            options.val(1);
            options.removeAttr('id');
        } else {
            $('#' + this.formName + '_' + foodType + 'Weights___name__').removeAttr('id');
        }
        event.preventDefault();
    }

    removeLabel(html) {
        return html.replace(/<label[\s\S]+?<\/label>/, '');
    }

    /**
     * When the input of the search dropdown changes an Ajax call is sent to the server.
     * When the call is successful the search dropdown is filled with search result.
     * If the search string is equal to a search result then the weight name id is set.
     */
    foodNameInput(foodType, event) {
        let thisElement = $(event.target);
        let row = new this.FoodRow(event.target);
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

        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }
        this.timeout = setTimeout(() => {
            $.ajax({
                url: url,
                type: 'GET',
                success: (data) => {
                    if (valueInput !== '') {
                        searchResults.html(data);
                    } else {
                        searchResults.html('');
                    }
                    $('.' + foodType + '-result').each((index, element) => {
                        let id = $(element).attr('id').replace(foodType + '-result', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            if (this.formName !== 'foodstuff_from_foodstuffs' && foodType === 'foodstuff') {
                                let pieceWeight = $(element).data('piece-weight');
                                let pieceName = $(element).data('piece-name');
                                this.replaceWeight(pieceWeight, pieceName, id, row);
                            } else {
                                row.getWeight().attr('name', this.formName + '[' + foodType + 'Weights][' + id + ']');
                            }
                        }
                    });
                },
                error: () => {
                    searchResults.html('Er ging iets fout.');
                }
            });
        }, 1000);
    }

    /**
     * When a search result is clicked upon the weight name id gets set and the search input value is set.
     */
    foodSearchResultClick(foodType, event) {
        let thisElement = $(event.target);
        let row = new this.FoodRow(event.target);
        let id = thisElement.attr('id').replace(foodType + '-result', '');
        let name = thisElement.text();
        let pieceWeight = thisElement.data('piece-weight');
        let pieceName = thisElement.data('piece-name');
        row.getName().val(name);
        if (this.formName !== 'foodstuff_from_foodstuffs' && foodType === 'foodstuff') {
            this.replaceWeight(pieceWeight, pieceName, id, row);
        } else {
            row.getWeight().attr('name', this.formName + '[' + foodType + 'Weights][' + id + ']');
        }
    }

    replaceWeight(pieceWeight, pieceName, id, row)
    {
        if (pieceWeight === '') {
            let input = $('#' + this.formName + '_foodstuffWeights').data('prototype');
            row.getWeight().replaceWith(this.removeLabel(input));
            row.getWeight().val('');
            row.getWeight().attr('name', this.formName + '[foodstuffWeights][' + id + ']');
            $('#' + this.formName + '_foodstuffWeights___name__').removeAttr('id');
        } else {
            let select = $('#' + this.formName + '_foodstuffChoices').data('prototype');
            row.getWeight().replaceWith(this.removeLabel(select));
            row.getWeight().val(1);
            row.getWeight().attr('name', this.formName + '[foodstuffChoices][' + id + ']');
            $('#' + this.formName + '_foodstuffChoices___name__').removeAttr('id');
        }
        if (pieceName === '') {
            row.getPieceName().text('');
        } else {
            row.getPieceName().text(pieceName);
        }
    }

    removeRow(event) {
        new this.FoodRow(event.target).row.remove();
    }

    /**
     * When the form submits it is checked that foodstuffs and recipes appear only once in the form.
     * When the form is foodstuff_from_foodstuffs it is checked that the percentages add up to 100.
     */
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
            $('.food-weight').each((index, element) => {
                sum = sum + parseFloat($(element).val().replace(',', '.'));
            });
            if (Math.round(sum * 100) !== 10000) {
                text = text + 'De gewichten zijn samen niet gelijk aan 100 procent. <br>';
                event.preventDefault();
            }
        }

        this.errors.html(text);
    }

    /**
     * The FoodForm has a FoodRow class in which the current element can be put and the row of the element is returned.
     * From this row the name, weight and search results can be retrieved.
     * The inspection JSCheckFunctionSignatures is disabled because the find method works on class names.
     */
    FoodRow = class {
        constructor(thisElement) {
            this.row = $(thisElement).closest('tr');
        }

        getName() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-toggle');
        }

        getWeight() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.food-weight');
        }

        getPieceName() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.piece-name');
        }

        getSearchResults() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-menu-food')
        }
    }
}
