var path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

// Change these variables to fit your project.
const outputPath = './assets';
const entryPoints = {
	admin: ['./src/scss/admin.scss'],
	//admin: ['./src/js/admin.js', './src/scss/admin.scss'],
	//frontend: ['./src/js/frontend.js','./src/scss/frontend.scss']
	//frontend: ['./src/js/frontend.js']
};

// Rules.
const rules = [
	{
		test: /\.scss$/i,
		use: [
			MiniCssExtractPlugin.loader,
			{loader: 'css-loader', options: {url: true, sourceMap: true}},
			{
				loader: "postcss-loader",
				options: {
					postcssOptions: {
						plugins: [
							[
								"postcss-preset-env",
								{
									browsers: 'defaults'
								},
							],
						],
					},
				},
			},
			'sass-loader',
		]
	},
	{
		test: /\.(jpg|jpeg|png|gif|woff|woff2|eot|ttf|svg)$/i,
		type: 'asset/resource',
		generator: {
			filename: '../img/[name][ext]',
			publicPath: 'img/',
			outputPath: 'img/',
		},
	},
	{
		exclude: /node_modules/,
		test: /\.jsx?$/,
		loader: 'babel-loader',
		options: {
			presets: ["@babel/preset-env"]
		}
	}
];

// Development.
const devConfig = {
	entry: entryPoints,
	output: {
		publicPath: 'auto',
		path: path.resolve(__dirname, outputPath),
		filename: 'js/[name].js',
		chunkFilename: 'js/modules/dev/[name].js',

	},
	plugins: [
		new RemoveEmptyScriptsPlugin(),
		new MiniCssExtractPlugin({
			filename: 'css/[name].css',
		}),
	],
	module: {
		rules: rules
	},
	devtool: 'eval-source-map',
};

// Production.
const prodConfig = {
	entry: entryPoints,
	output: {
		publicPath: 'auto',
		path: path.resolve( __dirname, outputPath ),
		filename: 'js/[name].min.js',
		chunkFilename: 'js/modules/[name].js',
	},
	plugins: [
		new RemoveEmptyScriptsPlugin(),
		new MiniCssExtractPlugin({
			filename: 'css/[name].min.css',
		}),
	],
	module: {
		rules: rules
	},
	optimization: {
		chunkIds: 'named',
	},

};

// Exports.
module.exports = (env, argv) => {
	switch (argv.mode) {
		case 'production':
			return prodConfig;
		default:
			return devConfig;
	}
}