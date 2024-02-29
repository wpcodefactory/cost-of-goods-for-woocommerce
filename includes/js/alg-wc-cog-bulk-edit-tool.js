/**
 * alg-wc-cog-bulk-edit-tool.js.
 *
 * @version 3.3.0
 * @since   1.3.3
 * @author  WPFactory
 */

(function ($, window, document) {
    "use strict";

    /**
     * Document on Ready
     *
     * @version 1.3.4
     * @since   1.3.3
     */
    $(document).on('ready', function () {

        // Dropdowns with description.
        const dropdownsWithDescription = {
            dropdownsSelector: '',

            init: function () {
                this.addDropdownsSelector('[data-dropdown_with_desc="true"]');
                let dropdowns = document.querySelectorAll(this.dropdownsSelector);
                dropdowns.forEach(function (dropdown) {
                    let jQueryDropdown = $(dropdown);
                    jQueryDropdown.on('change', function (e) {
                        let selectedOption = dropdown.options[dropdown.selectedIndex];
                        let targetSelector = dropdown.getAttribute('data-desc_target');
                        dropdownsWithDescription.showDescriptionFromOption(selectedOption, targetSelector);
                    });
                    jQueryDropdown.trigger('change');
                });
            },

            showDescriptionFromOption: function (option, targetSelector) {
                let desc = option.getAttribute('data-desc');
                if (targetSelector !== null) {
                    let targets = document.querySelectorAll(targetSelector);
                    targets.forEach(function (target) {
                        if (desc) {
                            target.classList.remove('hidden');
                            target.innerHTML = desc;
                        } else {
                            target.classList.add('hidden');
                        }
                    })
                }
            },

            addDropdownsSelector: function (selector) {
                this.dropdownsSelector = selector
            }
        }
        dropdownsWithDescription.init();

    });

    $(document).on('ready', function () {

        // Depend on elements.
        const dependOnElements = {
            init: function () {
                let elements = document.querySelectorAll('[data-depends_on]');
                elements.forEach(function (element) {
                    dependOnElements.handleDependOnElem(element);
                });
            },
            handleDependOnElem: function (selfElement) {
                let settingsData = selfElement.getAttribute('data-depends_on');
                let settingsJson = JSON.parse(settingsData);
                if (settingsJson.sourceSelector) {
                    let sourceSelector = document.querySelectorAll(settingsJson.sourceSelector);
                    sourceSelector.forEach(function (sourceElement) {
                        dependOnElements.handleSourceElem(sourceElement, selfElement, settingsJson);
                    })
                }
            },
            handleSourceElem: function (sourceElement, selfElement, settingsJson) {
                if (settingsJson.optionSelected) {
                    let jQuerySource = $(sourceElement);
                    jQuerySource.on('change', function (e) {
                        let selectedOption = sourceElement.options[sourceElement.selectedIndex];
                        if (settingsJson.optionSelected === selectedOption.value) {
                            dependOnElements.enableInputs(selfElement);
                            selfElement.classList.remove('hidden');
                        } else {
                            dependOnElements.disableInputs(selfElement);
                            selfElement.classList.add('hidden');
                        }
                    });
                    jQuerySource.trigger('change');
                }
            },
            enableInputs:function(container){
                if(container){
                    var inputElements = container.querySelectorAll('input');
                    inputElements.forEach(function(input) {
                        input.setAttribute('required', 'required');
                        input.removeAttribute('disabled');
                    });
                }
            },
            disableInputs:function(container){
                if(container) {
                    var inputElements = container.querySelectorAll('input');
                    inputElements.forEach(function (input) {
                        input.removeAttribute('required');
                        input.setAttribute('disabled', 'disabled');
                    });
                }
            }
        }
        dependOnElements.init();
    });

    /**
     * Document on Ready
     *
     * @version 1.3.4
     * @since   1.3.3
     */
    $(document).on('ready', function () {

        let cogBetInput = $(".alg_wc_cog_bet_input");

        cogBetInput.on("focus", function () {
            $(this).closest("tr").addClass("alg_wc_cog_bet_active_row");
        });

        cogBetInput.on("focusout", function () {
            $(this).closest("tr").removeClass("alg_wc_cog_bet_active_row");
        });

        cogBetInput.on("change", function () {
            if ($(this).attr("initial-value") !== jQuery(this).val()) {
                $(this).closest("td").addClass("alg_wc_cog_bet_modified_row");
            } else {
                $(this).closest("td").removeClass("alg_wc_cog_bet_modified_row");
            }
        });
    });

})(jQuery, window, document);


