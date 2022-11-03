/**
 * alg-wc-cog-bulk-edit-tool.js.
 *
 * @version 2.7.3
 * @since   1.3.3
 * @author  WPFactory
 */

(function ($, window, document) {
    "use strict";

    /**
     * Handle ajax form submission in bulk edit form for both by-price and by-profit.
     *
     * @version 2.5.1
     * @since 2.5.1
     */
    $(document).on('submit', '.bulk-edit-form.ajax-submission', function (e) {

        let bulkEditForm = $(this),
            bulkEditFormType = bulkEditForm.data('type'),
            bulkEditToolType = bulkEditForm.data('tool-type'),
            bulkEditFormSpinner = bulkEditForm.find('.spinner'),
            bulkEditFormNotice = $('.alg_wc_cog_notice');

        if (confirm(algWcCog.confirmText) && typeof bulkEditFormType !== 'undefined') {

            // Showing the spinner
            bulkEditFormSpinner.addClass('is-active');
            bulkEditFormNotice.removeClass('notice-success notice-error').fadeOut(100);

            $.ajax({
                type: 'POST',
                context: this,
                url: algWcCog.ajaxURL,
                data: {
                    'action': 'alg_wc_cog_update_product_data',
                    'form_data': bulkEditForm.serialize(),
                    'update_type': bulkEditFormType,
                    'tool_type': bulkEditToolType,
                },
                success: function (response) {
					// Hiding the spinner
					bulkEditFormSpinner.removeClass('is-active');
                    if (response.success) {
                        bulkEditFormNotice.addClass('notice-success').find(' > p').html(response.data).parent().fadeIn(100);
                    } else {
                        bulkEditFormNotice.addClass('notice-error').find(' > p').html(response.data).parent().fadeIn(100);
                    }
                    bulkEditForm.find('select').val('').trigger('change');
                    bulkEditForm.trigger("reset");

                    return false;
                }
            });
        }

        e.preventDefault();
        return false;
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

    // Json search product tags
	$(':input.wc-tag-search').filter(':not(.enhanced)').each(function () {
		var return_format = $(this).data('return_id') ? 'id' : 'slug';
		var select2_args = $.extend({
			allowClear: $(this).data('allow_clear') ? true : false,
			placeholder: $(this).data('placeholder'),
			minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : 3,
			escapeMarkup: function (m) {
				return m;
			},
			ajax: {
				url: wc_enhanced_select_params.ajax_url,
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						term: params.term,
						action: 'alg_wc_cog_json_search_tags',
						security: wc_enhanced_select_params.search_categories_nonce
					};
				},
				processResults: function (data) {
					var terms = [];
					if (data) {
						$.each(data, function (id, term) {
							terms.push({
								id: 'id' === return_format ? term.term_id : term.slug,
								text: term.formatted_name
							});
						});
					}
					return {
						results: terms
					};
				},
				cache: true
			}
		}, '');
		$(this).selectWoo(select2_args).addClass('enhanced');
	});

})(jQuery, window, document);


