import $ from 'jquery';

export class RecipeWeights {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#recipe_weights_error');
        this.weightsNumber = $('.recipe-weight').length;

        $('#add_recipe').on('click', this.addRecipe.bind(this));
        form.on('input', ".recipe-title", this.recipeTitleInput.bind(this));
        form.on('click', ".recipe-result", this.recipeSearchResultClick.bind(this));
        form.on('click', ".remove-row", this.removeRow.bind(this));
        form.on('submit', this.submit.bind(this));
    }

    addRecipe(event) {
        let selector = '#' + this.formName + '_recipe_weights__name__';
        let html = '<div class="row row-weight"><div class="col-7">' + $(selector + '_recipe_id').data('prototype') +
            '<div class="dropdown">' + $(selector + '_title').data('prototype') +
            '<div class="dropdown-menu dropdown-menu-recipe"></div></div>' +
            '</div>';
        html += '<div class="col-4">' + $(selector + '_value').data('prototype')
            + '</div>';
        html += '<div class="col-1">' +
            '<img src="'+ $('#recipe-weights-minus-img').attr('src') +
            '" class="remove-row" alt="minus" width="25"></div></div>';
        html = html.replaceAll('__name__', this.weightsNumber.toString());
        $('#add_foodstuff_recipe_button_row').before(html);
        if (this.formName === 'cookbook') {
            let value = $('[name="' + this.formName + '[recipe_weights][' + this.weightsNumber + '][value]' + '"]');
            value.val(1);
            value.addClass('hidden-input');
        }
        this.weightsNumber = this.weightsNumber + 1;
        event.preventDefault();
    }

    /**
     * When the input of the search dropdown changes an Ajax call is sent to the server.
     * When the call is successful the search dropdown is filled with search result.
     * If the search string is equal to a search result then the weight id is set.
     */
    recipeTitleInput(event) {
        let thisElement = $(event.target);
        let row = new this.RecipeRow(event.target);
        row.getRecipeId().val('');
        let valueInput = encodeURI(thisElement.val().toString().toLowerCase().normalize("NFD")
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
                        let title = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (thisElement.val().toString().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "") === title) {
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
     * When a search result is clicked upon the weight its id gets set and the search input value is set.
     */
    recipeSearchResultClick(event) {
        let thisElement = $(event.target);
        let row = new this.RecipeRow(event.target);
        let id = thisElement.attr('id').replace('recipe_result_', '');
        let title = thisElement.text();
        row.getTitle().val(title);
        row.getRecipeId().val(id);
    }

    removeRow(event) {
        new this.RecipeRow(event.target).row.remove();
    }

    /**
     * When the form submits it is checked that recipes appear only once in the form.
     */
    submit(event) {
        let text = '';

        let recipeTitles = [];
        $('.recipe-title').each((index, element) => {
            if (recipeTitles.includes($(element).val())) {
                text = text + 'Dubbel recept.';
                event.preventDefault();
            }
            recipeTitles.push($(element).val());
        });

        $('.recipe-id').each((index, element) => {
            let row = new this.RecipeRow(element);
            if ($(element).val() === '') {
                text = text + 'Ongeldige recepten naam: ' + row.getTitle().val() + '.<br>';
                $('html, body').animate({
                    scrollTop: this.errors.offset().top
                }, 1000);
                event.preventDefault();
            }
        });

        this.errors.html(text);
    }

    /**
     * The RecipesForm has a RecipeRow class in which the current element can be
     * put and the row of the element is returned.
     * From this row the id, title and search results can be retrieved.
     */
    RecipeRow = class {
        constructor(thisElement) {
            this.row = $(thisElement).closest('.row');
        }

        getRecipeId() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.recipe-id');
        }

        getTitle() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-toggle');
        }

        getSearchResults() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-menu-recipe')
        }
    }
}
