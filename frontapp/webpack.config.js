'use strict';

const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const merge = require('webpack-merge');
const WebpackNotifierPlugin = require('webpack-notifier');

const nodeModulesPath = path.resolve(__dirname, 'node_modules');


const webpackCommon = {
    entry: {
        app: ['./app/initialize'],
    },
    //entry:['webpack/hot/only-dev-server','babel-polyfill','./app/initialize'],
    module: {
        loaders: [
            {
                test: /\.js?$/,
                exclude: /node_modules/,
                loader: 'babel',
                query: {
                    presets: ['es2015']
                }
            },
            {
                test: /\.jst$/,
                loader: 'underscore-template-loader'
            },
            {
                test: /\.css$/,
                exclude: /node_modules/,
                loader: ExtractTextPlugin.extract('style-loader', 'css-loader')
            }
        ]
    },
    output: {
        filename: 'app.js',
        path: path.join(__dirname, './public'),
        publicPath: '/public/'
    },
    plugins: [
        new ExtractTextPlugin('app.css'),
        new CopyWebpackPlugin([
            {
                from: './app/assets/index.html',
                to: './index.html'
            },
            {
                from: "./node_modules/bootstrap/dist/css",
                to: './css'
            },
            {
                from: "./node_modules/bootstrap/dist/fonts",
                to: './fonts'
            },
            {
                from: './app/assets/image',
                to: './image'
            }

        ]),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            _: 'underscore'
        }),
        new WebpackNotifierPlugin(),
        new webpack.HotModuleReplacementPlugin()
    ],
    resolve: {
        extensions: ['', '.js', '.jsx'],
        root: path.join(__dirname, './app')
    },
    resolveLoader: {
        root: path.join(__dirname, './node_modules')
    }
};

switch (process.env.npm_lifecycle_event) {
    case 'start':
    case 'dev':
        webpackCommon.plugins.push();
        module.exports = merge(webpackCommon, {
            //devtool: '#inline-source-map',
            devtool: 'eval',

            devServer: {
                inline: true,
                host: "localhost",
                port: 9000,
                contentBase: './public/',
                hot: true
            }
        });

        break;
    default:
        module.exports = merge(webpackCommon, {
            devtool: 'source-map'
        });
        break;
}
