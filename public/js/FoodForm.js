class FoodForm {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#form_errors_client');
        this.options = $('#' + this.formName + '_foodstuffs option');
        $('.food-unit').each((index, element) => {
            let pieceName = $(element).data('piece-name');
            $(element).children().each((choiceIndex, choice) => {
                if ($(choice).val() === 'stuks' && pieceName !== '') {
                    $(choice).text(pieceName);
                }
            });
        });

        $('#add_foodstuff').on('click', this.addFoodstuff.bind(this));
        $('#add_recipe').on('click', this.addRecipe.bind(this));
        form.on('input', ".foodstuff-name", this.foodNameInput.bind(this, 'foodstuff'));
        form.on('input', ".recipe-name", this.foodNameInput.bind(this, 'recipe'));
        form.on('click', ".foodstuff-result", this.foodSearchResultClick.bind(this, 'foodstuff'));
        form.on('click', ".recipe-result", this.foodSearchResultClick.bind(this, 'recipe'));
        form.on('click', ".remove-row", this.removeRow.bind(this));
        form.on('submit', this.submit.bind(this));
    }

    /**
     * A row in the food form consists of a search dropdown, a weight, a piece name or empty cell
     * and a minus icon for deletion of the row.
     * The label of the weight input is removed.
     * When the food type is a recipe then the weight value is set to 1.
     * The id is removed from the new weight.
     */
    addFoodstuff(event) {
        let html = '<tr><td>' + $('#' + this.formName + '_foodstuff_dropdown').data('prototype') + '</td>';
        html += '<td>' + $('#' + this.formName + '_foodstuff_weights').data('prototype') + '</td>';
        if (this.formName === 'foodstuff_from_foodstuffs') {
            html += '<td>%</td>';
        } else {
            html += '<td>' + $('#' + this.formName + '_foodstuff_units').data('prototype') + '</td>';
        }
        html += '<td><i class="remove-row fa fa-minus"></i></td></tr>';
        html = this.removeLabels(html);
        $('#add_foodstuff_recipe_button_row').before(html);
        $('#' + this.formName + '_foodstuff_weights___name__').removeAttr('id');
        $('#' + this.formName + '_foodstuff_units___name__').removeAttr('id');
        event.preventDefault();
    }

    addRecipe(event) {
        let html = '<tr><td>' + $('#' + this.formName + '_recipe_dropdown').data('prototype') + '</td>';
        html += '<td colspan="2">' + $('#' + this.formName + '_recipe_weights').data('prototype') + '</td>';
        html += '<td><i class="remove-row fa fa-minus"></i></td></tr>';
        html = this.removeLabels(html);
        $('#add_foodstuff_recipe_button_row').before(html);
        let options = $('#' + this.formName + '_recipe_weights___name__');
        options.val(1);
        options.removeAttr('id');
        event.preventDefault();
    }

    removeLabels(html) {
        return html.replaceAll(/<label[\s\S]+?<\/label>/g, '');
    }

    /**
     * When the input of the search dropdown changes an Ajax call is sent to the server.
     * When the call is successful the search dropdown is filled with search result.
     * If the search string is equal to a search result then the weight name id is set.
     */
    foodNameInput(foodType, event) {
        let thisElement = $(event.target);
        let row = new this.FoodRow(event.target);
        let valueInput = encodeURI(thisElement.val().toLowerCase().normalize("NFD")
            .replace(/[\u0300-\u036f]/g, ""));
        let searchResults = row.getSearchResults();
        searchResults.html('');
        let url;
        if (foodType === 'foodstuff') {
            url = $('#foodstuff_search').data('search').replace('__name__', valueInput);
        } else {
            url = $('#recipe_search').data('search').replace('__title__', valueInput);
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
                        let id = $(element).attr('id').replace(foodType + '_result_', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            this.setWeightAndUnit(foodType, row, id, $(element))
                        }
                    });
                },
                error: () => {
                    searchResults.html('Er ging iets fout.');
                }
            });
        }, 1000);
    }

    setWeightAndUnit(foodType, row, id, element) {
        row.getWeight().attr('name', this.formName + '[' + foodType + '_weights][' + id + ']');
        if (foodType === 'foodstuff') {
            let pieceName = element.data('piece-name');
            let pieceWeight = element.data('piece-weight');
            row.getUnit().attr('name', this.formName + '[' + foodType + '_units][' + id + ']');
            row.getUnit().val('g');
            if (element.data('is-liquid') === 0) {
                row.getUnit().addClass('not-liquid')
            } else {
                row.getUnit().removeClass('not-liquid')
            }
            if (element.data('piece-weight') === '') {
                row.getUnit().addClass('not-piece');
            } else {
                row.getUnit().removeClass('not-piece');
            }
            if (pieceName !== '') {
                row.getUnitPiece().text(pieceName);
                row.getUnit().val('stuks');
            } else if (pieceWeight !== '') {
                row.getUnit().val('stuks');
            }
        }
    }

    /**
     * When a search result is clicked upon the weight name id gets set and the search input value is set.
     */
    foodSearchResultClick(foodType, event) {
        let thisElement = $(event.target);
        let row = new this.FoodRow(event.target);
        let id = thisElement.attr('id').replace(foodType + '_result_', '');
        let name = thisElement.text();
        row.getName().val(name);
        this.setWeightAndUnit(foodType, row, id, thisElement)
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
                text = text + 'De gewichten zijn samen niet gelijk aan 100%. <br>';
                event.preventDefault();
            }
        }

        this.errors.html(text);
    }

    /**
     * The FoodForm has a FoodRow class in which the current element can be put and the row of the element is returned.
     * From this row the name, weight, piece name and search results can be retrieved.
     * The inspection JSCheckFunctionSignatures is disabled because the find method works for class names.
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

        getUnit() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.food-unit');
        }

        getUnitPiece() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.food-unit .piece-option');
        }

        getSearchResults() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-menu-food')
        }
    }
}
