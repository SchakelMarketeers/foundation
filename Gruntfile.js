/* jshint strict: false */
/**
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

module.exports = function(grunt) {

    // Fix promises
    require('es6-promise').polyfill();

    var banner = {
        header: [
            'Part of Schakel Marketeers Foundation',
            'Developed with ❤ by Schakel Marketeers in Zwolle'
        ],
        devWarning: [
            'DEVELOPMENT BUILD',
            'NOT FOR PRODUCTION!'
        ],
        author: [
            '@author Roelof Roos <roelof@schakelmarketeers.nl>',
            '@author Joram Schrijver <joram@schakelmarketeers.nl>',
            '@license AGPL-v3',
        ],
        meta: [
            '@see https://github.com/SchakelMarketeers/foundation'
        ],
    };

    var constructBanner = function(data) {
        var bnr = [];

        // Add jshint ignore
        bnr.push('// jshint ignore: start');

        // Start license block
        bnr.push('/**!');

        var prefix = ' * ';

        data.forEach(function(v) {
            if (!v || !banner[v]) {
                return;
            }

            banner[v].forEach(function(val) {
                bnr.push(prefix + val);
            });
            bnr.push(prefix);
        });
        bnr.pop();

        bnr.push(' */');

        return bnr.join('\n') + '\n';
    };

    var prodBanner = constructBanner([
        'header',
        'author',
        'meta'
    ]);
    var devBanner = constructBanner([
        'header',
        'devWarning',
        'author',
        'meta'
    ]);

    var libBanner = constructBanner(['header', 'meta']);

    var files = {
        'js-static': {
            'assets/live/vendor.min.js': [
                'bower_components/jquery/dist/jquery.js',
                'bower_components/bootstrap/js/bootstrap.js',
                'bower_components/slick-carousel/slick/slick.js',
                'bower_components/sprintf/src/sprintf.js'
            ]
        },
        js: {
            'assets/live/script.min.js': [
                'assets/js/*.js'
            ]
        },
        jshint: [
            'Gruntfile.js',
            'assets/js/*.js'
        ],
        sass: {
            'assets/live/style.min.css': 'assets/sass/style.scss'
        },
        sasslint: [
            'assets/sass/*.scss'
        ],
        img: [{
            expand: true,
            cwd: 'assets/img/',
            src: ['*.{png,jpg,gif}'],
            dest: 'assets/live/'
        }],
        copy: [{
            expand: true,
            cwd: 'bower_components/font-awesome/fonts',
            src: 'fontawesome-webfont.*',
            dest: 'assets/live/'
        }],
        watch: {
            'js-static': [],
            js: 'assets/js/*.js',
            sass: 'assets/sass/*.scss',
            img: 'assets/img/*.{png,jpg,gif}'
        },
        cssplugins: {},
        clean: [
            'assets/*.{jpg,jpeg,png}',
            'assets/live/*.{jpg,jpeg,png}',
            '!assets/live/{vendor,script}.min.js',
            '!assets/live/style.min.css'
        ]
    };

    for (var x in files.sass) {
        if (files.sass.hasOwnProperty(x)) {
            files.cssplugins[x] = x;
        }
    }

    var _pushToJsStatic = function(file) {
        files.watch['js-static'].push(file);
    };

    for (x in files['js-static']) {
        if (files['js-static'].hasOwnProperty(x)) {
            if (files['js-static'][x].forEach) {
                files['js-static'][x].forEach(_pushToJsStatic);
            } else {
                files.watch['js-static'].push(files['js-static'][x]);
            }
        }
    }

    var MozJpeg = require('imagemin-mozjpeg');
    var AutoPrefixer = require('autoprefixer');
    var CleanCss = require('clean-css');

    var pluginInstances = {
        mozjpeg: MozJpeg(),
        autoprefixer: AutoPrefixer({
            browsers: 'last 2 versions'
        }),
        cleancss: CleanCss({
            keepSpecialComments: '1',
            processImport: true,
            mediaMerging: true,
            compatibility: '*',
            processImportFrom: ['local']
        })
    };

    var plugins = {
        img: [pluginInstances.mozjpeg],
        cssProd: [pluginInstances.autoprefixer, pluginInstances.cleancss],
        cssDev: [pluginInstances.autoprefixer]
    };

    var translatePath = function(path) {
        if (process.platform === 'win32') {
            return path.replace(/\//g, '\\');
        }
        return path;
    };

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        banner: {
            prod: prodBanner,
            dev: devBanner
        },

        // Start of JS minifier
        uglify: {
            static: {
                options: {
                    mangle: true,
                    compress: {
                        warnings: false
                    },
                    ASCIIOnly: false,
                    preserveComments: 'some',
                    banner: libBanner,
                    quoteStyle: 1
                },
                files: files['js-static']
            },
            prod: {
                options: {
                    mangle: true,
                    compress: true,
                    ASCIIOnly: false,
                    preserveComments: 'some',
                    banner: prodBanner,
                    quoteStyle: 1
                },
                files: files.js
            },
            dev: {
                options: {
                    mangle: false,
                    compress: false,
                    ASCIIOnly: false,
                    preserveComments: 'some',
                    banner: devBanner,
                    quoteStyle: 1
                },
                files: files.js
            }
        },

        // JS Linter
        jshint: {
            files: files.jshint,
            options: {
                jshintrc: '.jshintrc'
            }
        },

        // JS Linter
        jscs: {
            files: files.jshint,
            options: {
                config: '.jscsrc'
            }
        },

        // Sass linting
        sasslint: {
            options: {
                configFile: '.sass-lint.yml',
            },
            files: files.sasslint
        },

        // Start of LESS linter and compiler
        sass: {
            prod: {
                options: {
                    indentWidth: 4,
                    banner: prodBanner,
                    outputStyle: 'nested',
                    roundingPrecision: 4,
                    keepSpecialComments: 0,
                    processImportFrom: ['local']
                },
                files: files.sass
            },
            dev: {
                options: {
                    indentWidth: 4,
                    banner: devBanner,
                    outputStyle: 'nested',
                    roundingPrecision: -1,
                    sourceComments: true
                },
                files: files.sass
            }
        },

        // Postprocess CSS
        postcss: {
            dev: {
                files: files.cssplugins,
                options: {
                    map: {
                        inline: false,
                        annotation: 'assets/live/maps'
                    },
                    processors: plugins.cssDev
                },
            },
            prod: {
                files: files.cssplugins,
                options: {
                    map: false,
                    processors: plugins.cssProd
                }
            }
        },

        // Start of image minifier
        imagemin: {
            static: {
                options: {
                    optimizationLevel: 7,
                    use: plugins.img
                },
                files: files.img
            }
        },

        // Copy webfonts
        copy: {
            static: {
                files: files.copy
            }
        },

        // Remove old/stale files
        clean: {
            static: files.clean
        },

        // Run PHP validators
        shell: {
            phpunit: {
                command: [
                    translatePath('src/vendor/bin/phpunit'),
                    '--config=phpunit.xml.dist'
                ].join(' ')
            },
            phpcpd: {
                command: [
                    translatePath('src/vendor/bin/phpcpd'),
                    '-n',
                    'src/core',
                    'src/model',
                    'src/controller',
                ].join(' ')
            },
            phpdepend: {
                command: [
                    translatePath('src/vendor/bin/pdepend'),
                    '--jdepend-chart=build/depend/jdepend.svg',
                    '--overview-pyramid=build/depend/pyramid.svg',
                    'src/core,src/model,src/controller'
                ].join(' ')
            },
            phploc: {
                command: [
                    translatePath('src/vendor/bin/phploc'),
                    '--log-csv build/loc/php-loc.csv',
                    '--log-xml build/loc/php-loc.xml',
                    '--count-tests',
                    '--no-interaction',
                    'src/core',
                    'src/model',
                    'src/controller',
                    'tests'
                ].join(' ')
            },
            phpdoc: {
                command: [
                    'mkdir -p build/docs &&',
                    'mkdir -p build/doc-tmp &&',
                    'phpdocumentor project:run',
                    '--defaultpackagename "Auto Siero"',
                    '--config phpdocumentor.dist.xml',
                    '&& rm -rf build/doc-tmp'
                ].join(' ')
            },
            twiglint: {
                command: [
                    translatePath('src/vendor/bin/twig-lint lint'),
                    'src/view'
                ].join(' ')
            }
        },

        // Watch config
        watch: {
            sass: {
                files: files.watch.sass,
                tasks: ['test-css', 'dev-css'],
                options: {
                    interrupt: true
                }
            },
            js: {
                files: files.watch.js,
                tasks: ['dev-js'],
                options: {
                    interrupt: true
                }
            },
            'js-static': {
                files: files.watch['js-static'],
                tasks: ['statics'],
                options: {
                    interrupt: true
                }
            },
            imagemin: {
                files: files.watch.img,
                tasks: ['statics'],
                options: {
                    interrupt: true
                }
            }
        }
    });

    // Load all used tasks
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-jscs');
    grunt.loadNpmTasks('grunt-newer');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-sass-lint');
    grunt.loadNpmTasks('grunt-shell');

    // Live server functionality
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Start registering tasks
    //
    // Verifies JS is valid and standards are honored
    grunt.registerTask(
        'test-js',
        'Tests if the Javascript files meet the standards and follow the ' +
        'style guides',
        [
            'jshint',
            'jscs'
        ]
    );

    // Verfies Sass files are valid
    grunt.registerTask(
        'test-css',
        'verifies if the Sass files meet the required standards',
        [
            'sasslint'
        ]
    );

    // Verfies Twig templates / files are valid
    grunt.registerTask(
        'test-twig',
        'verifies if the Twig files are valid, according to strict standards',
        [
           'shell:twiglint'
        ]
    );

    grunt.registerTask(
        'mkdir-phpdepend',
        'Create the build/depend folder',
        function() {
            grunt.file.mkdir('build/depend');
        }
    );

    grunt.registerTask(
        'mkdir-phploc',
        'Create the build/loc folder',
        function() {
            grunt.file.mkdir('build/loc');
        }
    );

    // Runs all PHP tests and reports
    grunt.registerTask(
        'test-php',
        'Runs all PHP tests, does not create documentation (use ' +
        'test-php-with-docs)',
        [
            'shell:phpunit',
            'shell:phpcpd',
            'mkdir-phpdepend',
            'shell:phpdepend',
            'mkdir-phploc',
            'shell:phploc'
        ]
    );

    // Runs all PHP tests and reports, and generates documentation
    grunt.registerTask(
        'test-php-with-docs',
        'Runs all PHP tests and creates documentation',
        [
            'test-php',
            'shell:phpdoc'
        ]
    );

    // Runs all the aforementioned tests in one go. This command is also run
    // before running `dev` or `prod`.
    grunt.registerTask(
        'test',
        'Runs all tests concerning Javascript, Sass, Twig and PHP',
        [
            'test-js',
            'test-css',
            'test-php',
            'test-twig'
        ]
    );

    grunt.registerTask(
        'dev-js',
        'Compiles only the Javascript assets (development)',
        [
            'uglify:dev'
        ]
    );

    grunt.registerTask(
        'dev-css',
        'Compiles only the Sass assets (development)',
        [
            'sass:dev',
            'postcss:dev'
        ]
    );

    grunt.registerTask(
        'statics',
        'Compiles the static assets (images)',
        [
            'newer:uglify:static',
            'newer:imagemin:static',
            'newer:copy'
        ]
    );

    grunt.registerTask(
        'dev',
        'Compiles assets for development',
        [
            'clean',
            'test',
            'statics',
            'dev-js',
            'dev-css',
        ]
    );

    grunt.registerTask(
        'prod',
        'Compiles assets for production',
        [
            'clean',
            'test',
            'statics',
            'uglify:prod',
            'sass:prod',
            'postcss:prod'
        ]
    );

    grunt.registerTask(
        'default',
        'Identical to `dev`, compiles assets for development',
        [
            'dev'
        ]
    );
};
