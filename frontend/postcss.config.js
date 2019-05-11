module.exports = {
	plugins: [
		require('autoprefixer'),
	]
};

if(process.env.NODE_ENV === 'production') {
	module.exports.plugins.push(
		require('cssnano') ({
			preset: 'default'
		})
	);
}
