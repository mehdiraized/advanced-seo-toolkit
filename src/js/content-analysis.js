console.log("Content Analysis script loaded");

(function (wp) {
	const { registerPlugin } = wp.plugins;
	const { PluginSidebar } = wp.editPost;
	const { PanelBody, Button, Notice } = wp.components;
	const { withSelect } = wp.data;
	const { Fragment, useState } = wp.element;

	const ContentAnalysis = (props) => {
		const [suggestions, setSuggestions] = useState([]);

		const analyzeContent = () => {
			const content = props.content;
			fetch(ASTContentAnalysis.ajax_url, {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
				},
				body: new URLSearchParams({
					action: "ast_analyze_content",
					nonce: ASTContentAnalysis.nonce,
					content: content,
				}),
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.success) {
						setSuggestions(data.data);
					}
				});
		};

		return (
			<PluginSidebar
				name="ast-content-analysis-sidebar"
				title="Content Analysis"
				icon="admin-site"
			>
				<PanelBody>
					<Button isPrimary onClick={analyzeContent}>
						Analyze Content
					</Button>
					{suggestions.length > 0 && (
						<Fragment>
							<h3>SEO Suggestions</h3>
							<ul>
								{suggestions.map((suggestion, index) => (
									<li key={index}>{suggestion}</li>
								))}
							</ul>
						</Fragment>
					)}
				</PanelBody>
			</PluginSidebar>
		);
	};

	const mapSelectToProps = (select) => {
		return {
			content: select("core/editor").getEditedPostContent(),
		};
	};

	const ContentAnalysisWithSelect =
		withSelect(mapSelectToProps)(ContentAnalysis);

	registerPlugin("ast-content-analysis", {
		render: ContentAnalysisWithSelect,
	});
})(window.wp);
