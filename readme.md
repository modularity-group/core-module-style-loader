# core-module-style-loader 

This core-module builds on WordPress and Modularity.

Compiles and bundles all module SCSS files.

---

Version: 1.2.3

Author: Matze @ https://modularity.group

License: MIT

---

Compile all Theme 

- *module-name*/*module-name*.scss
- *module-name*/*module-name*.editor.scss

files in order 

- config-*
- wp-block-*
- feature-* 

to */wp-content/modules/* and */wp-content/themes/-theme-/* when url-parameter *?c* is set and enqueue to frontend and editor before themes style.css.

if `// generate_editor_styles=true` is found during compile in a module's scss file, this scss content will be wrapped with `.editor-style-wrapper` and saved also to `modules.editor.css` or `bundle.editor.css`.

Note: all modules of one type (f.e. config-*) are loaded in one run for both folders

---

1.2.3
- Add option to force editor-style creation of standard scss files with flag: `// generate_editor_styles=true` saved in respective file

1.2.2
- UPDATE sabberworm/php-css-parser (8.4.0)
- UPDATE scssphp/scssphp (v1.9.0)

1.2.1
- Rename theme-assets from `theme` to `bundle`

1.2.0
- update new asset structure: `/modules` and `/theme/*`  

1.1.1
- update modules and dist path

1.1.0 (Matze)
- Update scssphp 1.6.0 > 1.8.1

1.0.3
- also load core modules scss files

1.0.2
- change order of style.css and compiled styles to overwrite css variables

1.0.1
- only allow admins and developers to see the button

0.5.0
- load also *library* modules
- new core module structure
- updated scssphp from 1.5.2 > 1.6.0
