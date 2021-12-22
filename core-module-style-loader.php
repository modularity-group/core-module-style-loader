<?php defined("ABSPATH") or die;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;

if(!defined("DIST_URL")){
  define("DIST_URL", get_stylesheet_directory_uri());
}
if(!defined("DIST_PATH")){
  define("DIST_PATH", get_stylesheet_directory());
}

$cssFileModules = MODULES_DIR . "/modules.css";
$cssEditorFileModules = MODULES_DIR . "/modules.editor.css";
$cssFileTheme = DIST_PATH . "/bundle.css";
$cssEditorFileTheme = DIST_PATH . "/bundle.editor.css";

if( isset($_GET['c']) || isset($_GET['compile']) || !file_exists($cssFileModules) || !file_exists($cssEditorFileModules)|| !file_exists($cssFileTheme) || !file_exists($cssEditorFileTheme) ){
  // if (!is_dir(DIST_PATH)) {
  //   mkdir(DIST_PATH);
  // }

  require_once dirname( __FILE__ ).'/vendor/autoload.php';

  $compiler = new Compiler();
  $compiler->setOutputStyle( OutputStyle::COMPRESSED ); // this is not working. WHY?
  $themeModulesFolder = get_stylesheet_directory();

  $cssContentModules = '';
  $cssEditorContentModules = '';
  $cssContentTheme = '';
  $cssEditorContentTheme = '';
  $compilerError = false;

  foreach (array("core", "config", "wp-block", "feature") as $prefix) {
    foreach (glob(MODULES_DIR."/*") as $libraryModule) {
      $basename = basename($libraryModule);
      if (substr($basename, 0, strlen($prefix)) === $prefix) {
        $scssFile = MODULES_DIR."/$basename/$basename.scss";
        $compiler->addImportPath( MODULES_DIR."/$basename/" );
        $scssContent = trim(@file_get_contents( $scssFile ));
        if($scssContent){
          try {
            $cssContentModules .= $compiler->compile( $scssContent );
            if(strpos($scssContent,'generate_editor_styles=true')){
              $cssEditorContentModules .= $compiler->compile( '.editor-styles-wrapper {'.$scssContent.'}' );
            }
          } catch(Exception $e1) {
            echo 'Compiler error in '.$scssFile.':<br>' .$e1->getMessage();
            $compilerError = true;
          }
        }
        $scssEditorFile = MODULES_DIR."/$basename/$basename.editor.scss";
        $compiler->addImportPath( MODULES_DIR."/$basename/" );
        $scssEditorContent = trim(@file_get_contents( $scssEditorFile ));
        if($scssEditorContent){
          try {
            $cssEditorContentModules .= $compiler->compile( $scssEditorContent );
          } catch(Exception $e2) {
            echo 'Compiler error in '.$scssEditorFile.':<br>' .$e2->getMessage();
            $compilerError = true;
          }
        }
      }
    }
    foreach (glob("{$themeModulesFolder}/*") as $themeModule) {
      $basename = basename($themeModule);
      if (substr($basename, 0, strlen($prefix)) === $prefix) {
        $scssFile = "{$themeModulesFolder}/$basename/$basename.scss";
        $compiler->addImportPath( "{$themeModulesFolder}/$basename/" );
        $scssContent = trim(@file_get_contents( $scssFile ));
        if($scssContent){
          try {
            $cssContentTheme .= $compiler->compile( $scssContent );
            if(strpos($scssContent,'generate_editor_styles=true')){
              $cssEditorContentTheme .= $compiler->compile( '.editor-styles-wrapper {'.$scssContent.'}' );
            }
          } catch(Exception $e1) {
            echo 'Compiler error in '.$scssFile.':<br>' .$e1->getMessage();
            $compilerError = true;
          }
        }
        $scssEditorFile = "{$themeModulesFolder}/$basename/$basename.editor.scss";
        $compiler->addImportPath( "{$themeModulesFolder}/$basename/" );
        $scssEditorContent = trim(@file_get_contents( $scssEditorFile ));
        if($scssEditorContent){
          try {
            $cssEditorContentTheme .= $compiler->compile( $scssEditorContent );
          } catch(Exception $e2) {
            echo 'Compiler error in '.$scssEditorFile.':<br>' .$e2->getMessage();
            $compilerError = true;
          }
        }
      }
    }
  }

  if($compilerError == false){
    $cssContentModules = str_replace( '@charset "UTF-8";','',$cssContentModules ); // fix this wih settings somehow
    $autoprefixer = new Autoprefixer( $cssContentModules );
    $prefixedCssContent = $autoprefixer->compile();
    file_put_contents( $cssFileModules, $prefixedCssContent );

    $cssEditorContentModules = str_replace( '@charset "UTF-8";','',$cssEditorContentModules ); // fix this wih settings somehow
    $autoprefixerEditor = new Autoprefixer( $cssEditorContentModules );
    $prefixedCssEditorContent = $autoprefixerEditor->compile();
    file_put_contents( $cssEditorFileModules, $prefixedCssEditorContent );

    $cssContentTheme = str_replace( '@charset "UTF-8";','',$cssContentTheme ); // fix this wih settings somehow
    $autoprefixer = new Autoprefixer( $cssContentTheme );
    $prefixedCssContent = $autoprefixer->compile();
    file_put_contents( $cssFileTheme, $prefixedCssContent );

    $cssEditorContentTheme = str_replace( '@charset "UTF-8";','',$cssEditorContentTheme ); // fix this wih settings somehow
    $autoprefixerEditor = new Autoprefixer( $cssEditorContentTheme );
    $prefixedCssEditorContent = $autoprefixerEditor->compile();
    file_put_contents( $cssEditorFileTheme, $prefixedCssEditorContent );
  } else {
    die('<br>Nothing compiled because of errors!');
  }
}

add_action( 'wp_enqueue_scripts', function(){
  wp_enqueue_style(
    'core-module-style-loader-theme-style',
    get_stylesheet_directory_uri() . '/style.css',
    array('core-module-style-loader-theme'),
    filemtime( get_stylesheet_directory() . '/style.css' ),
    'all'
  );
  wp_enqueue_style(
    'core-module-style-loader-modules',
    MODULES_PATH . "/modules.css",
    array(),
    filemtime( MODULES_DIR . "/modules.css" ),
    'all'
  );
  wp_enqueue_style(
    'core-module-style-loader-theme',
    DIST_URL . "/bundle.css",
    array('core-module-style-loader-modules'),
    filemtime( DIST_PATH . "/bundle.css" ),
    'all'
  );
}, 20 );

add_action( 'enqueue_block_editor_assets', function(){
  wp_enqueue_style(
    'theme-style',
    get_stylesheet_directory_uri() . '/style.css',
    array('theme-editor-styles'),
    filemtime( get_stylesheet_directory() . '/style.css' ),
    'all'
  );
  wp_enqueue_style(
    'modules-editor-styles',
    MODULES_PATH . "/modules.editor.css",
    array(),
    filemtime( MODULES_DIR . "/modules.editor.css" ),
    'all'
  );
  wp_enqueue_style(
    'theme-editor-styles',
    DIST_URL . "/bundle.editor.css",
    array('modules-editor-styles'),
    filemtime( DIST_PATH . "/bundle.editor.css" ),
    'all'
  );
}, 20 );

add_action('admin_bar_menu', function( $wp_admin_bar ) {
  if(current_user_can('administrator') || current_user_can('developer')){
    if(is_admin()){
      $baseurl = get_bloginfo( 'url' );
    } else {
      $baseurl = $_SERVER['REQUEST_URI'];
    }
    $compileUrl = add_query_arg( 'c', '', $baseurl );
    $args = array(
        'id' => 'style-loader',
        'title' => 'Compile Theme',
        'href' => $compileUrl,
        'meta' => array(
          'class' => 'style-loader',
          'title' => 'Compile Theme'
        )
    );
    $wp_admin_bar->add_node($args);
  }
}, 999);
