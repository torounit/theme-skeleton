module.exports = (grunt) ->

  pkg = grunt.file.readJSON("package.json")

  #
  # taskをロード
  #
  for taskName of pkg.devDependencies
    grunt.loadNpmTasks taskName  if taskName.substring(0, 6) is "grunt-"

  #
  # Gruntの設定
  #
  grunt.initConfig
    #
    # 監視対象の定義とか
    #
    watch:
      options:
        livereload: true
        nospawn: true

      sass:
        files: [
          "assets/scss/**/*.sass"
          "assets/scss/**/*.scss"
        ]
        tasks: ["scsslint", "compassMultiple"]

      coffee:
        files: ["assets/coffee/*.coffee"]
        tasks: ["coffee",'concat','uglify:compress']


    #
    # scsslint
    # 要 gem scss-lint
    #
    scsslint: {
      allFiles: [
        "assets/scss/**/*.scss",
      ],
      options: {
        config: 'assets/scss/.scss-lint.yml'
      },
    }



    #
    # 普通のCompass
    #
    compress:
      dev: {}

    #
    # Compassのマルチスレッド版
    #
    compassMultiple:
      options:
        config: 'config.rb'

      common:
        options:
          sassDir: "assets/scss/"

    coffee:
      compile:
        files: [
          expand: true
          cwd: "assets/coffee"
          src: "*.coffee"
          dest: "assets/javascripts/src/"
          ext: ".js"
        ]

    #Jsの結合
    concat:
      options:
        separator: ';',
      dist:
        src: 'assets/javascripts/src/*.js',
        dest: 'assets/javascripts/all.js', #jsはすべて、all.jsに。

    #minify
    uglify:
      #compress:
        #options:
        #  sourceMap: (fileName) ->
        #    fileName.replace /\.js$/, '.js.map'
        #files: [
        #    expand: true,
        #    cwd: 'assets/javascripts/',
        #    src: ['**/*.js'],
        #    dest: 'assets/javascripts/',
        #    ext: '.min.js'
        #]
      compress:
        files:
          'assets/javascripts/all.min.js': ['assets/javascripts/all.js']
      bower:
        files:
          'assets/vendor/lib.min.js': ['assets/vendor/lib.js']

    #
    # bowerのファイルレイアウトの変更
    #
    bower:
      install:
        options:
          targetDir: 'assets/vendor'
          layout: 'byComponent'
          install: true
          verbose: false
          cleanTargetDir: true
          cleanBowerDir: false



    #
    # bowerでインストールしたものを、lib.jsにまとめる。
    # jQueryとmodernizrはCDNから呼ぶことが多いので、除去。
    #
    bower_concat:
      all:
        dest: 'assets/vendor/lib.js'
        exclude: [
            'jquery',
            'modernizr'
        ]
        bowerOptions:
          relative: false


  #
  # taskの登録
  #
  # grunt watch     - Compile Compass and CoffeeScript on Save.
  # grunt           - watch and Compile.
  # grunt bowerinit - build bowerjs.
  #
  grunt.registerTask "default", ["watch","scsslint","compassMultiple","coffee",'concat','uglify:compress']
  grunt.registerTask 'bowerinit', ['bower:install','bower_concat','uglify:bower']
