const gulp = require('gulp');
const tailwindConfig = "tailwind.config.js";
const adminPanelCSS = "assets/src/admin-panel.css";

/**
 * Custom PurgeCSS Extractor
 * https://github.com/FullHuman/purgecss
 */
class TailwindExtractor {
  static extract(content) {
    return content.match(/[\w-/:]+(?<!:)/g) || [];
  }
}

/**
 * Task: gulp css
 */
gulp.task("css", function() {
  const atimport = require("postcss-import");
  const postcss = require("gulp-postcss");
  const tailwindcss = require("tailwindcss");
  const purgecss = require("gulp-purgecss");
  const concat = require('gulp-concat');
  const csso = require('gulp-csso');
  const sourcemaps = require('gulp-sourcemaps');
  const autoprefixer = require('gulp-autoprefixer');

  return gulp
    .src(adminPanelCSS)
    .pipe(postcss([atimport(), tailwindcss(tailwindConfig)]))
    .pipe(
      purgecss({
        content: ["**/*.php", "**/*.html", "../../**/*.md"],
        extractors: [
          {
            extractor: TailwindExtractor,
            extensions: ["html", "md", 'php']
          }
        ]
      })
    )
    .pipe(autoprefixer({
        overrideBrowserslist: [
            "last 1 version"
        ],
        cascade: false
    }))
    .pipe(csso())
    .pipe(concat('build.min.css'))
    .pipe(gulp.dest("assets/dist/css/"));
});

gulp.task('watch', function () {
    gulp.watch(["**/*.php", "**/*.html", "../../**/*.md", "assets/src/"], gulp.series('css'));
});
