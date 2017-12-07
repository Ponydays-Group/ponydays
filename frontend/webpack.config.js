"use strict"

let path = require('path');
let fs = require('fs');
let webpack = require('webpack');
let ExtractTextPlugin = require('extract-text-webpack-plugin');

let isProduction = process.env.NODE_ENV == 'production';

let vendors = [
    'babel-polyfill',
    path.resolve(__dirname,'js','jquery'),
    path.resolve(__dirname,'js','jquery.file.js'),
    path.resolve(__dirname,'js','jquery.jqmodal.js'),
    path.resolve(__dirname,'js','jquery.notifier.js'),
    path.resolve(__dirname,'js','jquery.markitup.js'),
    path.resolve(__dirname,'js','jquery.serialize.js'),
    path.resolve(__dirname,'js','jquery.poshytip.js'),
    path.resolve(__dirname,'js','jquery.Jcrop.js'),
    'jquery.hotkeys',
    'jquery-form',
    path.resolve(__dirname,'css','bootstrap','assets','javascripts','bootstrap'),
    'jquery-ui-bundle',
];

// let contextPath = path.join(__dirname, 'frontend');
let config = {
//    context: contextPath,
    cache: true,

    entry: {
        main: path.resolve(__dirname, 'js', 'index'),
        vendor: vendors,
        light: path.resolve(__dirname, 'css', 'light.scss'),
        dark: path.resolve(__dirname, 'css', 'dark.scss'),
        sockets: path.resolve(__dirname, 'js', 'sockets.js'),
    },
    output: {
        path: path.join(__dirname, '..', 'static', '[hash]'),
        filename: '[name].bundle.js'
    },
    module: {
        loaders: [{
            test: /\.jsx?$/,
            loader: 'babel-loader',
            exclude: /node_modules/,
            query: {
                presets: ['es2015', 'stage-0', 'react']
            },
            compact: true
        },
            {
                test: /\.scss$/,
                loader: ExtractTextPlugin.extract("style-loader", "css!autoprefixer-loader?browsers=last 15 versions!resolve-url-loader!sass")
            },
            {
                test: /\.css$/,
                loader: "style!css-loader?modules&importLoaders=1&localIdentName=[name]__[local]___[hash:base64:5]"
            },
            {
                test: /\.(jpe?g|png|gif|svg)$/i,
                loaders: [
                    'file-loader?name=img-[sha512:hash:base64:7].[ext]'
                ]
            },
            {
                test: /\.(png|woff|woff2|eot|ttf|svg)$/,
                loader: 'url-loader?limit=100000'
            }
        ]
    },
    resolve: {
        extensions: ['', '.js', '.jsx', '.scss'],
        modulesDirectories: ['node_modules', 'js'],
        root: [
            process.env.NODE_PATH,
//            path.resolve(contextPath)
        ]
    },
    plugins: [
        new ExtractTextPlugin("[name].css"),
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendor'
        }),
        new webpack.optimize.DedupePlugin(),
        function () {
            this.plugin('done', function (stats) {
                fs.writeFileSync(
                    path.join(__dirname, '..', 'config', 'engine_config', 'frontend.config.json'),
                    JSON.stringify({"frontend": {"version": stats.hash}})
                );
            });
        }
    ],
    resolveLoader: {
        root: process.env.NODE_PATH
    }
};

if (isProduction) {
    config.plugins.push(new webpack.optimize.UglifyJsPlugin())
}
module.exports = config;
