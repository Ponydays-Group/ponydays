const webpack = require('webpack');

const path = require('path');

const isProduction = process.env.NODE_ENV === 'production';

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const AssetsWebpackPlugin = require('assets-webpack-plugin');
const DelWebpackPlugin = require('del-webpack-plugin');

module.exports = {
    entry: {
        main: ['@babel/polyfill', path.resolve(__dirname, 'js', 'index.js')],
        light: path.resolve(__dirname, 'css', 'light.scss'),
        dark: path.resolve(__dirname, 'css', 'dark.scss'),
        sockets: path.resolve(__dirname, 'js', 'sockets.js'),
    },
    output: {
        path: path.join(__dirname, '..', 'static', 'relevant'),
        filename: '[name].[contenthash:10].bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.(sc|sa|c)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            importLoaders: 3,
                        }
                    },
                    'postcss-loader',
                    'resolve-url-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 8192,
                            fallback: {
                                loader: 'file-loader',
                                options: {
                                    name: 'img-[sha512:hash:base64:10].[ext]'
                                }
                            }
                        }
                    }
                ]
            },
            {
                test: /\.(woff|woff2|eot|ttf)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 8192,
                            fallback: {
                                loader: 'file-loader',
                                options: {
                                    name: 'fnt-[sha512:hash:base64:10].[ext]'
                                }
                            }
                        }
                    }
                ]
            },
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                [
                                    '@babel/preset-env',
                                    {
                                        targets: "> 0.25%, not dead"
                                    }
                                ]
                            ],
                            plugins: [
                                '@babel/plugin-proposal-object-rest-spread',
                                '@babel/plugin-proposal-class-properties'
                            ]
                        }
                    }
                ]
            }
        ]
    },
    optimization: {
        minimize: isProduction,
        minimizer: isProduction ? [new TerserPlugin()] : [],
        splitChunks: {
            chunks: 'all',
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]|[\\/]js[\\/]jquery[\\/]|[\\/]css[\\/]bootstrap[\\/]/
                }
            }
        }
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].[contenthash:10].css',
            chunkFilename: '[id].css'
        }),
        new AssetsWebpackPlugin({
            path: path.join(__dirname, '..', 'config', 'engine_config'),
            filename: 'frontend.config.json',
            processOutput: function (assets) {
                const result = {
                    "frontend": {
                        "webpack": assets
                    }
                };
                return JSON.stringify(result);
            }
        }),
        new DelWebpackPlugin({allowExternal: true})
    ]
};
