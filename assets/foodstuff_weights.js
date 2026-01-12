import $ from 'jquery';

export class FoodstuffWeights {

    constructor(form) {
        this.formName = form.attr('name');
        this.errors = $('#foodstuff_weights_error');
        this.weightsNumber = $('.foodstuff-weight').length;

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
        let selector = '#' + this.formName + '_foodstuff_weights__name__';
        let html = '<div class="row row-weight"><div class="col-4">' +
            $(selector + '_foodstuff_id').data('prototype') +
            '<div class="dropdown">' + $(selector + '_name').data('prototype') +
            '<div class="dropdown-menu dropdown-menu-foodstuff"></div></div>' +
            '</div>';
        html += '<div class="col-3">' + $(selector + '_value').data('prototype') + '</div>';
        if (this.formName === 'combine_foodstuffs') {
            html += '<div class="col-4">%<span id="percentage-unit">' +
                $(selector + '_unit').data('prototype') + '</span></div>';
        } else {
            html += '<div class="col-4">' + $(selector + '_unit').data('prototype') + '</div>';
        }
        html += '<div class="col-1">' +
            '<img src="' + $('#foodstuff-weights-minus-img').attr('src') +
            '" class="remove-row" alt="minus" width="25"></div></div>';
        html = html.replaceAll('__name__', this.weightsNumber.toString());
        $('#add_foodstuff_recipe_button_row').before(html);
        let unit = $('#' + this.formName + '_foodstuff_weights_' + this.weightsNumber + '_unit');
        if (this.formName === 'combine_foodstuffs') {
            unit.attr('tabindex', -1);
            unit.val('g');
        } else {
            unit.attr('class', unit.attr('class') + ' not-piece not-liquid')
        }
        this.weightsNumber = this.weightsNumber + 1;
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
        row.getFoodstuffId().val('');
        if (this.formName !== 'combine_foodstuffs') {
            row.getUnit().val('');
        }
        row.getUnit().removeClass('not-piece').removeClass('not-liquid')
        row.getUnit().addClass('not-piece not-liquid');
        let valueInput = encodeURI(thisElement.val().toString().toLowerCase().normalize("NFD")
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
                        if (thisElement.val().toString().toLowerCase().normalize("NFD")
                            .replace(/[\u0300-\u036f]/g, "") === name) {
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
        if (pieceName !== '' && pieceWeight === '') {
            row.getUnit().val(pieceName);
        } else if (pieceName !== '' && pieceWeight !== '') {
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
     * When the form is combine_foodstuffs it is checked that the percentages add up to 100.
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

        $('.foodstuff-id').each((index, element) => {
            let row = new this.FoodstuffRow(element);
            if ($(element).val() === '') {
                text = text + 'Ongeldige voedingsmiddelen naam: ' + row.getName().val() + '.<br>';
                $('html, body').animate({
                    scrollTop: this.errors.offset().top
                }, 1000);
                event.preventDefault();
            }
        });

        if (this.formName === "combine_foodstuffs") {
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
            this.row = $(thisElement).closest('.row');
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
