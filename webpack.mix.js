const mix = require("laravel-mix");
const glob = require("glob");
const path = require("path");

// PurgeCSS Plugin import (compatible with v2.3.0)
const PurgeCSSPlugin = require("purgecss-webpack-plugin");

const PATHS = {
    src: path.join(__dirname, "resources")
};

// JS compile
mix.js("resources/js/app.js", "public/js")
   .sass("resources/sass/app.scss", "public/css");

// Merge + minify CSS (web)
mix.styles([
    // "web/css/bootstrap.css",
    // "web/css/floating-wpp.min.css",
    // "web/css/style.css",
    // "web/css/responsive.css",
], "public/css/web-all.css");

// Merge + minify JS (web)
mix.scripts([
    "web/js/jquery.js",
    "web/js/popper.min.js",
    "web/js/bootstrap.min.js",
    "web/js/jquery.fancybox.js",
    "web/js/owl.js",
    "web/js/wow.js",
    "web/js/appear.js",
    "web/js/isotope.js",
    "web/js/jquery.mCustomScrollbar.concat.min.js",
    "web/js/jquery-ui.js",
    "web/js/mixitup.js",
    "web/js/script.js",
], "public/js/web-all.js");

// Versioning for cache-busting
if (mix.inProduction()) {
    mix.version();

    mix.webpackConfig({
        plugins: [
            new PurgeCSSPlugin({
                paths: glob.sync([
                    path.join(PATHS.src, "views/**/*.blade.php"),
                    path.join(PATHS.src, "js/**/*.js")
                ]),
                whitelist: [
                    /^swiper-/,
                    /^owl-/,
                    /^fa-/,
                    /^animate__/,
                    /^aos-/,
                ],
            })
        ]
    });
}
