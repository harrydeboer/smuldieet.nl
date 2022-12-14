class FoodstuffsForm {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#form_errors_client');
        this.weightsNumber = $('.foodstuff-weight').length - 1.

        $('#add_foodstuff').on('click', this.addFoodstuff.bind(this));
        form.on('input', ".foodstuff-name", this.foodstuffNameInput.bind(this));
        form.on('click', ".foodstuff-result", this.foodstuffSearchResultClick.bind(this));
        form.on('click', ".remove-row", this.removeRow.bind(this));
        form.on('submit', this.submit.bind(this));
    }

    /**
     * A row in the food form consists of a search dropdown, a weight, a piece name or empty cell
     * and a minus icon for deletion of the row.
     * The label of the weight input is removed.
     * The id is removed from the new weight.
     */
    addFoodstuff(event) {
        this.weightsNumber = this.weightsNumber + 1;
        let selector = '#' + this.formName + '_foodstuff_weights__name__';
        let html = '<tr><td><div class="dropdown">' +
            $(selector + '_name').data('prototype') +
            '<div class="dropdown-menu dropdown-menu-foodstuff"></div></div>' +
            $(selector + '_foodstuff_id').data('prototype') +
            '</td>';
        html += '<td>' + $(selector + '_value').data('prototype') + '</td>';
        if (this.formName === 'foodstuff_from_foodstuffs') {
            html += '<td>%</td>';
        } else {
            html += '<td>' + $(selector + '_unit').data('prototype') + '</td>';
        }
        html += '<td><i class="remove-row fa fa-minus"></i></td></tr>';
        html = html.replaceAll('__name__', this.weightsNumber);
        $('#add_foodstuff_recipe_button_row').before(html);
        event.preventDefault();
    }

    /**
     * When the input of the search dropdown changes an Ajax call is sent to the server.
     * When the call is successful the search dropdown is filled with search result.
     * If the search string is equal to a search result then the weight its id and unit is set.
     */
    foodstuffNameInput(event) {
        let thisElement = $(event.target);
        let row = new this.FoodstuffRow(event.target);
        let valueInput = encodeURI(thisElement.val().toLowerCase().normalize("NFD")
            .replace(/[\u0300-\u036f]/g, ""));
        let searchResults = row.getSearchResults();
        searchResults.html('');
        let url;
        url = $('#foodstuff_search').data('search').replace('__name__', valueInput);

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
                    $('.foodstuff-result').each((index, element) => {
                        let id = $(element).attr('id').replace('foodstuff_result_', '');
                        let name = $(element).text().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "");
                        if (valueInput === name) {
                            this.setIdAndUnit(row, id, $(element))
                        }
                    });
                },
                error: () => {
                    searchResults.html('Er ging iets fout.');
                }
            });
        }, 1000);
    }

    setIdAndUnit(row, id, element) {
        row.getFoodstuffId().val(id)
        let pieceName = element.data('piece-name');
        let pieceWeight = element.data('piece-weight');
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

    /**
     * When a search result is clicked upon the weight its name, id, and unit gets set
     * and the search input value is set.
     */
    foodstuffSearchResultClick(event) {
        let thisElement = $(event.target);
        let row = new this.FoodstuffRow(event.target);
        let id = thisElement.attr('id').replace('foodstuff_result_', '');
        let name = thisElement.text();
        row.getName().val(name);
        this.setIdAndUnit(row, id, thisElement)
    }

    removeRow(event) {
        new this.FoodstuffRow(event.target).row.remove();
    }

    /**
     * When the form submits it is checked that foodstuffs appear only once in the form.
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

        if (this.formName === "foodstuff_from_foodstuffs") {
            let sum = 0;
            $('.foodstuff-weight').each((index, element) => {
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
     * The FoodstuffsForm has a FoodstuffRow class in which the current element can be put
     * and the row of the element is returned.
     * From this row the id, name, unit, piece option and search results can be retrieved.
     */
    FoodstuffRow = class {
        constructor(thisElement) {
            this.row = $(thisElement).closest('tr');
        }

        getFoodstuffId() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.foodstuff-id');
        }

        getName() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-toggle');
        }

        getUnit() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.foodstuff-unit');
        }

        getUnitPiece() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.foodstuff-unit .piece-option');
        }

        getSearchResults() {
            // noinspection JSCheckFunctionSignatures
            return this.row.find('.dropdown-menu-foodstuff')
        }
    }
}
