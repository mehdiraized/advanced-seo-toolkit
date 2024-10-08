jQuery(document).ready(function ($) {
	$(".ast-upload-image").click(function (e) {
		e.preventDefault();
		var button = $(this);
		var targetInput = $(button.data("target"));

		var frame = wp.media({
			title: "Select or Upload Image",
			button: {
				text: "Use this image",
			},
			multiple: false,
		});

		frame.on("select", function () {
			var attachment = frame.state().get("selection").first().toJSON();
			targetInput.val(attachment.url);
		});

		frame.open();
	});
});
