class RecipesForm {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#form_errors_client');
        this.weightsNumber = $('.recipe-weight').length - 1.

        $('#add_recipe').on('click', this.addRecipe.bind(this));
        form.on('input', ".recipe-name", this.recipeNameInput.bind(this));
        form.on('click', ".recipe-result", this.recipeSearchResultClick.bind(this));
        form.on('click', ".remove-row", this.removeRow.bind(this));
        form.on('submit', this.submit.bind(this));
    }

    addRecipe(event) {
        this.weightsNumber = this.weightsNumber + 1;
        let html = '<tr><td>' + $('#' + this.formName + '_recipe_dropdown').data('prototype') +
            $('#' + this.formName + '_recipe_weights__name___recipe_id').data('prototype') + '</td>';
        html += '<td colspan="2">' + $('#' + this.formName + '_recipe_weights__name___value').data('prototype') + '</td>';
        html += '<td><i class="remove-row fa fa-minus"></i></td></tr>';
        $('#add_foodstuff_recipe_button_row').before(html);
        $('[name="' + this.formName + '[recipe_weights][__name__][recipe_id]' + '"]')
            .attr('name', this.formName + '[recipe_weights][' + this.weightsNumber + '][recipe_id]')
            .removeAttr('id');
        $('[name="' + this.formName + '[recipe_weights][__name__][value]' + '"]')
            .attr('name', this.formName + '[recipe_weights][' + this.weightsNumber + '][value]')
            .removeAttr('id');
        if (this.formName === 'cookbook') {
            let value = $('[name="' + this.formName + '[recipe_weights][' + this.weightsNumber + '][value]' + '"]');
            value.val(1);
            value.addClass('hidden-input');
        }
        event.preventDefault();
    }

    /**
     * When the input of the search dropdown changes an Ajax call is sent to the server.
     * When the call is successful the search dropdown is filled with search result.
     * If the search string is equal to a search result then the weight name id is set.
     */
    recipeNameInput(event) {
        let thisElement = $(event.target);
        let row = new this.FoodRow(event.target);
        let valueInput = encodeURI(thisElement.val().toLowerCase().normalize("NFD")
            .replace(/[\u0300-\u036f]/g, ""));
        let searchResults = row.getSearchResults();
        searchResults.html('');
        let url = $('#recipe_search').data('search').replace('__title__', valueInput);

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
                    $('.recipe-result').each((index, element) => {
                        let id = $(element).attr('id').replace('recipe_result_', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            row.getRecipeId().val(id);
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
    recipeSearchResultClick(event) {
        let thisElement = $(event.target);
        let row = new this.FoodRow(event.target);
        let id = thisElement.attr('id').replace('recipe_result_', '');
        let name = thisElement.text();
        row.getName().val(name);
        row.getRecipeId().val(id);
    }

    removeRow(event) {
        new this.FoodRow(event.target).row.remove();
    }

    /**
     * When the form submits it is checked that recipes appear only once in the form.
     */
    submit(event) {
        let text = '';

        let recipeNames = [];
        $('.recipe-name').each((index, element) => {
            if (recipeNames.includes($(element).val())) {
                text = text + 'Dubbel recept.';
                event.preventDefault();
            }
            recipeNames.push($(element).val());
        });

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

        getRecipeId() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.recipe-id');
        }

        getName() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-toggle');
        }

        getWeight() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.recipe-weight');
        }

        getSearchResults() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-menu-food')
        }
    }
}
