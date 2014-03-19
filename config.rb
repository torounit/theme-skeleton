css_dir = "assets/stylesheets"
sass_dir = "assets/scss"
images_dir = "assets/images"
javascripts_dir = "assets/javascripts"
enable_sourcemaps = true
sass_options = { :sourcemap => true }
output_style = (environment == :production) ? :compressed : :expanded
line_comments = (environment == :production) ? :false : :true
relative_assets = true