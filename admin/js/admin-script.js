jQuery(document).ready(function ($) {
	$("#ast-run-analysis").on("click", function () {
		var $button = $(this);
		var $results = $("#ast-analysis-results");

		$button.prop("disabled", true).text(astAdminData.runningAnalysisText);
		$results.html("");

		$.ajax({
			url: astAdminData.ajaxurl,
			type: "POST",
			data: {
				action: "ast_run_analysis",
				nonce: astAdminData.nonce,
			},
			success: function (response) {
				$results.html(response);
			},
			error: function () {
				$results.html('<p class="error">' + astAdminData.errorText + "</p>");
			},
			complete: function () {
				$button.prop("disabled", false).text(astAdminData.runAnalysisText);
			},
		});
	});
});
