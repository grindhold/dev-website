var elixir = require('laravel-elixir');
require('laravel-elixir-vueify');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 */

elixir(function(mix) {
    // public/css/app.css
    mix.sass('app.scss');

    // public/js/developers.js
    mix.browserify('developers.js', 'public/js/', 'resources/js');

    // public/js/spec.js
    mix.browserify('spec.js', 'public/js/', 'resources/js');

    // public/js/api.js
    mix.browserify('api.js', 'public/js/', 'resources/js');

    // public/css/lib.css
    mix.styles(
        [
            'prismjs/themes/prism.css',
            'prismjs/plugins/line-numbers/prism-line-numbers.css',
        ], 'public/css/lib.css', 'node_modules'
    );

    // public/js/lib.js
    mix.scripts(
        [
            'prismjs/prism.js',

            'prismjs/components/prism-javascript.js',
            'prismjs/components/prism-sql.js',
            'prismjs/components/prism-php.js',
            'prismjs/components/prism-python.js',

            'prismjs/plugins/line-numbers/prism-line-numbers.js',
        ], 'public/js/lib.js', 'node_modules'
    );

    mix.copy('resources/assets/brand/icon/oparl-icon.png', 'public/img/favicon.png');
    mix.copy('resources/assets/brand/wortmarke/oparl-wortmarke-rgb.svg', 'public/img/logos/oparl.svg');
    mix.copy('resources/assets/img/oparl-icon-dev-slackbot.png', 'public/img/oparl-icon-dev-slackbot.png');

    // copy source code pro font files
    mix.copy('node_modules/source-code-pro/EOT/', 'public/fonts/');
    mix.copy('node_modules/source-code-pro/OTF/', 'public/fonts/');
    mix.copy('node_modules/source-code-pro/TTF/', 'public/fonts/');
    mix.copy('node_modules/source-code-pro/WOFF/', 'public/fonts/');
    mix.copy('node_modules/source-code-pro/WOFF2/', 'public/fonts/');

    // copy source sans pro font files
    mix.copy('node_modules/source-sans-pro/EOT/', 'public/fonts/');
    mix.copy('node_modules/source-sans-pro/OTF/', 'public/fonts/');
    mix.copy('node_modules/source-sans-pro/TTF/', 'public/fonts/');
    mix.copy('node_modules/source-sans-pro/WOFF/', 'public/fonts/');
    mix.copy('node_modules/source-sans-pro/WOFF2/', 'public/fonts/');

    // copy font awesome font files
    mix.copy('node_modules/font-awesome/fonts/', 'public/fonts/');

    mix.phpUnit();
});
