(function ($) {
	"use strict";

	$(document).ready(function () {
		// Sitemap preview toggle
		$(".toggle-sitemap-preview").on("click", function (e) {
			e.preventDefault();
			$(".sitemap-preview").toggle();
		});

		// Checkbox toggle all
		$(".toggle-all").on("change", function () {
			var isChecked = $(this).prop("checked");
			$(this)
				.closest("table")
				.find('input[type="checkbox"]')
				.prop("checked", isChecked);
		});

		// AJAX sitemap regeneration
		$("#regenerate-sitemap").on("click", function (e) {
			e.preventDefault();
			var $button = $(this);
			$button.prop("disabled", true).text("Regenerating...");

			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action: "ast_regenerate_sitemap",
					nonce: astSitemapAdmin.nonce,
				},
				success: function (response) {
					if (response.success) {
						alert("Sitemap regenerated successfully!");
					} else {
						alert("Error regenerating sitemap. Please try again.");
					}
				},
				error: function () {
					alert("Error regenerating sitemap. Please try again.");
				},
				complete: function () {
					$button.prop("disabled", false).text("Regenerate Sitemap");
				},
			});
		});
	});
})(jQuery);
