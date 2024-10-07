const path = require("path");

module.exports = {
	entry: {
		"content-analysis": "./src/js/content-analysis.js",
		// Add other entry points here if you have more JS files
	},
	output: {
		filename: "[name].js",
		path: path.resolve(__dirname, "assets/js"),
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: "babel-loader",
					options: {
						presets: ["@babel/preset-env", "@babel/preset-react"],
					},
				},
			},
		],
	},
	externals: {
		react: "React",
		"react-dom": "ReactDOM",
		"@wordpress/element": "wp.element",
		"@wordpress/components": "wp.components",
		"@wordpress/data": "wp.data",
		"@wordpress/plugins": "wp.plugins",
		"@wordpress/edit-post": "wp.editPost",
		// Add other WordPress dependencies as needed
	},
};
