const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require("extract-text-webpack-plugin");


module.exports = {
    entry: './src/main.js',

    output: {
        filename: 'main.js',
        path: path.resolve(__dirname, 'resources/dist')
    },

    module: {
        rules: [
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: "style-loader",
                    use: {
                        loader: "css-loader",
                        options:{
                            minimize: {
                                // safe: true,
                                discardComments: {
                                    removeAll: true
                                }
                            }
                        }
                    }
                }),
            },
            {
                test: /\.(png|woff|woff2|eot|ttf|svg)$/,
                use: { loader: 'url-loader', options: { limit: 100000 } },
            },
            {
                test: /\.jpg$/,
                use: [ 'file-loader' ]
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
                        // the "scss" and "sass" values for the lang attribute to the right configs here.
                        // other preprocessors should work out of the box, no loader config like this nessessary.
                        'scss': 'vue-style-loader!css-loader!sass-loader',
                        'sass': 'vue-style-loader!css-loader!sass-loader?indentedSyntax'
                    }
                }
            },
            // {
            //     test: /\.js$/,
            //     loader: 'babel-loader',
            //     exclude: /node_modules/
            // },
        ]
    },

    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.common.js'
        }
    },

    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery",
            // semantic: 'semantic-ui-css',
        }),
        new ExtractTextPlugin("main.css"),
    ],
};