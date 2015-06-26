<?php
/**
 * DeepWiki
 *
 * @author Yuan Chong <ychongsaytc@gmail.com>
 */

// environment

date_default_timezone_set( 'UTC' );
error_reporting( 0 );

// load query
$current_path = trim( dirname( $_SERVER['PHP_SELF'] ), '/' );

if ( isset( $_GET['p'] ) )
	$query_string = trim( $_GET['p'], '/' );
else
	$query_string = '';

// constants

define( 'SITE_URI', '/' . $current_path );
define( 'APP_ROOT', __DIR__ );
define( 'CONFIG_ROOT', APP_ROOT . '/deepwiki-config' );
define( 'VENDOR_ROOT', APP_ROOT . '/deepwiki-vendor' );
define( 'THEMES_ROOT', APP_ROOT . '/deepwiki-themes' );
define( 'THEMES_ROOT_URI', rtrim( SITE_URI, '/' ) . '/deepwiki-themes' );
define( 'ASSETS_ROOT_URI', rtrim( SITE_URI, '/' ) . '/deepwiki-assets' );

define( 'LOGGING_LOGGED_IN', 11 );
define( 'LOGGING_NOT_LOGGED_IN', 12 );
define( 'LOGGING_WRONG_PASSWORD', 13 );

// functions

function dw_uri( $path = null, $absolute = false ) {
	if ( strpos( $path, '://' ) > 0 )
		return $path;
	if ( empty( $path ) )
		$uri = rtrim( SITE_URI, '/' ) . '/';
	global $config;
	if ( $config['rewrite'] )
		$uri = rtrim( SITE_URI, '/' ) . '/' . $path;
	else
		$uri = rtrim( SITE_URI, '/' ) . '/index.php?p=' . ltrim( $path, '/' );
	if ( $absolute )
		$uri = dw_translate_to_absolute_uri( $uri );
	return $uri;
}

function dw_asset_uri( $path = null, $absolute = false ) {
	global $config;
	if ( empty( $path ) )
		return null;
	else
		$uri = ASSETS_ROOT_URI . '/' . ltrim( $path, '/' );
	if ( $absolute )
		$uri = dw_translate_to_absolute_uri( $uri );
	return $uri;
}

function dw_translate_to_absolute_uri( $uri ) {
	$protocol = ( isset( $_SERVER['HTTPS'] ) && 'on' == $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
	$port = (
			isset( $_SERVER['SERVER_PORT'] ) &&
				( ( 'http' == $protocol && $_SERVER['SERVER_PORT'] != '80' ) ||
					( 'https' == $protocol && $_SERVER['SERVER_PORT'] != '443' ) )
		) ? ':' . $_SERVER['SERVER_PORT'] : null;
    
    $url = $protocol . $_SERVER['SERVER_NAME'] . $port . $uri;
    if($_['SERVER_PORT'] != '80'){
        $url = $protocol . $_SERVER['SERVER_NAME'] . ':'. $_SERVER['SERVER_PORT'] . $uri;
    }
	return $url;
}

function dw_doc_file_type( $filename ) {
	if ( strpos( $filename, '://' ) > 0 )
		return 'url';
	$extension_name = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	if ( in_array( $extension_name, array( 'markdown', 'md', 'mdml', 'mdown' ) ) )
		return 'markdown';
	if ( in_array( $extension_name, array( 'html', 'htm' ) ) )
		return 'html';
	if ( in_array( $extension_name, array( 'txt' ) ) )
		return 'plain';
	return false;
}

function dw_sanitize( $string ) {
	$output = strtolower( $string );
	$output = preg_replace( '#([^0-9a-z]+)#', '-', $output );
	$output = trim( $output, '-' );
	if ( empty( $output ) )
		return 'title';
	return $output;
}

function dw_go_home() {
    global $config;
    //print_r($_SERVER);exit;
	header( 'Location: ' . dw_uri( $config['home_route'], true ) );
	exit();
}

function dw_get_logged_in_hash() {
	global $config;
	return md5( md5( $config['password'] ) . ':' . $config['cookie_salt'] );
}

function dw_process_login() {
	setcookie( 'logging', dw_get_logged_in_hash(), time() + 86400, dw_uri() );
}

function dw_process_logout() {
	setcookie( 'logging', null, time() - 86400, dw_uri() );
}

// load configuration

$config_filename = CONFIG_ROOT . '/config.json';
if ( ! file_exists( $config_filename ) )
	$config_filename = CONFIG_ROOT . '/config-sample.json';
$config_json = file_get_contents( $config_filename );
$config = json_decode( $config_json, true );

if ( ! is_array( $config ) )
	$config = array();

// defaults

$config = array_merge( array(
	'site_name' => 'DeepWiki',
	'site_description' => 'Markdown Documents Showcase',
	'copyright' => 'Powered by <a href="http://deepwiki.deepdevelop.com/" target="_blank">DeepWiki</a>.',
	'theme' => 'default',
	'docs_path' => 'deepwiki-docs',
	'home_route' => null,
	'display_chapter' => false,
	'display_index' => false,
	'rewrite' => false,
	'footer_code' => null,
	'password' => null,
	'cookie_salt' => null,
	'docs' => null,
), $config );
// constants based on configuration

define( 'DOCS_ROOT', APP_ROOT . '/' . trim( $config['docs_path'], '/' ) );

// load theme

$theme = $config['theme'];
$theme_root = THEMES_ROOT . '/' . $theme;
$theme_root_uri = THEMES_ROOT_URI . '/' . $theme;
$theme_config_filepath = $theme_root . '/theme.json';
if ( ! file_exists( $theme_config_filepath ) ) {
	exit( 'illegal theme' );
} else {
	$theme_config = json_decode( file_get_contents( $theme_config_filepath ), true );
}

// pre-construct template parts

$parts = array(
	'{{site_name}}' => htmlspecialchars( $config['site_name'] ),
	'{{site_description}}' => htmlspecialchars( $config['site_description'] ),
	'{{site_uri}}' => dw_uri(),
	'{{html_head}}' => '',
	'{{nav}}' => '',
	'{{copyright}}' => $config['copyright'],
	'{{body_footer}}' => $config['footer_code'],
	'{{login_form}}' => '',
	'{{logout_link}}' => '',
	'{{doc_title}}' => '',
	'{{doc_heading}}' => '',
	'{{doc_content}}' => '',
	'{{doc_index}}' => '',
);

foreach ( $theme_config['assets']['css'] as $entry )
	$parts['{{html_head}}'] .= sprintf( '<link rel="stylesheet" type="text/css" href="%s" />' . PHP_EOL, $theme_root_uri . '/' . $entry );
foreach ( $theme_config['assets']['js'] as $entry )
	$parts['{{html_head}}'] .= sprintf( '<script type="text/javascript" src="%s"></script>' . PHP_EOL, $theme_root_uri . '/' . $entry );

$parts['{{login_form}}'] = '<form method="post" role="form">' .
	'<div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" /></div>' .
	'<button type="submit" class="btn btn-default">Submit</button>' .
	'</form>';

// logging

$logged = LOGGING_NOT_LOGGED_IN;
if ( ! empty( $config['password'] ) ) {
	if ( isset( $_COOKIE['logging'] ) ) {
		// has logging cookie
		$cookie_hash = $_COOKIE['logging'];
		if ( $cookie_hash === dw_get_logged_in_hash() ) {
			$logged = LOGGING_LOGGED_IN;
		}
	} elseif ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) {
		// post password
		if ( $config['password'] === $_POST['password'] ) {
			dw_process_login();
			$logged = LOGGING_LOGGED_IN;
		} else {
			$logged = LOGGING_WRONG_PASSWORD;
		}
	}
	// show logging form
	if ( LOGGING_LOGGED_IN !== $logged ) {
		// wrong password
		if ( LOGGING_WRONG_PASSWORD === $logged ) {
			$parts['{{login_form}}'] = '<div class="alert alert-danger" role="alert">Wrong password.</div>' . $parts['{{login_form}}'];
		}
		// load theme template
		$template = file_get_contents( $theme_root . '/login.html' );
		$output = str_replace( array_keys( $parts ), $parts, $template );
		// output html
		echo $output;
		exit();
	}
}

// handle request

if ( empty( $query_string ) ) {
	dw_go_home();
}

// process to logout

if ( '_logout' === $query_string ) {
	dw_process_logout();
	dw_go_home();
}

// walk all document files

$items = array();

if ( empty( $config['docs'] ) ) :

	foreach ( scandir( DOCS_ROOT ) as $filename ) {
		if ( in_array( $filename, array( '.', '..', '.gitignore' ) ) )
			continue;
		$type = dw_doc_file_type( $filename );
		if ( false === $type )
			continue;
		$filename_pure = substr( $filename, 0, strrpos( $filename, '.' ) );
		$matches = array();
		preg_match_all( '#^(([0-9a-z]+\.)+\ +)?(.+?)(\ +\[(\S+)\])?$#', $filename_pure, $matches );
		$title = $matches[3][0];
		$chapter = rtrim( $matches[1][0], ' ' );
		if ( empty( $matches[5][0] ) )
			$slug = dw_sanitize( $title );
		else
			$slug = dw_sanitize( $matches[5][0] );
		$chapter_tree = explode( '.', rtrim( $chapter, '.' ) );
		$depth = count( $chapter_tree );
		array_pop( $chapter_tree );
		if ( empty( $chapter_tree ) )
			$parent = '';
		else
			$parent = implode( '.', $chapter_tree ) . '.';
		$items[] = compact( 'title', 'slug', 'chapter', 'filename', 'type', 'depth', 'parent' );
	}

else :

	function _walk_config_docs_tree( $docs, &$items, $parent = '' ) {
		$i = 1;
		foreach ( $docs as $slug => $item ) {
			$chapter = $parent . $i . '.';
			$items[] = array(
				'title'    => $item['title'],
				'slug'     => $slug,
				'chapter'  => $chapter,
				'filename' => $item['file'],
				'type'     => dw_doc_file_type( $item['file'] ),
				'depth'    => substr_count( $parent, '.' ) + 1,
				'parent'   => $parent,
			);
			if ( isset( $item['children'] ) )
				_walk_config_docs_tree( $item['children'], $items, $chapter );
			$i ++;
		}
	}
	_walk_config_docs_tree( $config['docs'], $items );

endif;

// generate paths

foreach ( array_keys( $items ) as $k ) {
	if ( 'url' === $items[ $k ]['type'] ) {
		$items[ $k ]['path'] = $items[ $k ]['filename'];
		continue;
	}
	$path = '/' . $items[ $k ]['slug'];
	$current_pos = $items[ $k ]['parent'];
	for ( $i = $items[ $k ]['depth'] - 1; $i >= 1; $i -- ) {
		foreach ( $items as $entry ) {
			if ( $entry['depth'] === $i && $current_pos === $entry['chapter'] ) {
				$current_pos = $entry['parent'];
				$path = '/' . $entry['slug'] . $path;
				break;
			}
		}
	}
	$items[ $k ]['path'] = trim( $path, '/' );
}

// components

require_once ( VENDOR_ROOT . '/erusev/parsedown/Parsedown.php' );
require_once ( VENDOR_ROOT . '/erusev/parsedown-extra/ParsedownExtra.php' );

// compile document content

foreach ( $items as $entry ) {
	if ( $entry['path'] === $query_string ) {
		$origin = file_get_contents( DOCS_ROOT . '/' . $entry['filename'] );
		switch ( $entry['type'] ) {
			case 'markdown':
				$Parsedown = new ParsedownExtra();
				$content = $Parsedown->text( $origin );
				break;
			case 'html':
				$content = $origin;
				break;
			default:
				$content = nl2br( htmlspecialchars( $origin ) );
				break;
		}
		/** intergrate dedicated translation */
		if ( in_array( $entry['type'], array( 'markdown', 'html' ) ) ) {
			/** replace inner page link */
			$matches = array();
			preg_match_all( '#\ (href|src)="\#\/([^\"]+)"#ui', $content, $matches );
			if ( $matches[0] ) {
				foreach ( array_keys( $matches[0] ) as $i ) {
					$content = str_replace( $matches[0][ $i ], ' ' . $matches[1][ $i ] . '="' . dw_uri( $matches[2][ $i ] ) . '"', $content );
				}
			}
			/** replace asset urls */
			$matches = array();
			preg_match_all( '#\ (href|src)="\!\/([^\"]+)"#ui', $content, $matches );
			if ( $matches[0] ) {
				foreach ( array_keys( $matches[0] ) as $i ) {
					$content = str_replace( $matches[0][ $i ], ' ' . $matches[1][ $i ] . '="' . dw_asset_uri( $matches[2][ $i ] ) . '"', $content );
				}
			}
		}
		$doc = array(
			'title' => $entry['title'],
			'slug' => $entry['slug'],
			'chapter' => $entry['chapter'],
			'filename' => $entry['filename'],
			'content' => $content,
		);
		break;
	}
}

// 404

if ( ! isset( $doc ) ) {
	// load theme template
	$template = file_get_contents( $theme_root . '/404.html' );
	$output = str_replace( array_keys( $parts ), $parts, $template );
	// output html
	echo $output;
	exit();
}

// generate anchors for outline

$matches = array();
preg_match_all( '#\<h([1-6])\>([^\<]+)\<\/h([1-6])\>#ui', $doc['content'], $matches );
if ( count( $matches[0] ) ) {
	$slugs = array();
	foreach ( array_keys( $matches[2] ) as $k ) {
		$the_slug = dw_sanitize( $matches[2][ $k ] );
		if ( in_array( $the_slug, $slugs ) ) {
			$i = 2;
			while ( in_array( $the_slug . '-' . $i, $slugs ) ) {
				$i ++;
			}
			$the_slug = $the_slug . '-' . $i;
		}
		$slugs[ $k ] = $the_slug;
	}
	foreach ( array_keys( $matches[0] ) as $k ) {
		$doc['content'] = substr_replace(
			$doc['content'],
			sprintf( '<h%d id="%s">%s</h%d>',
				$matches[1][ $k ],
				$slugs[ $k ],
				$matches[2][ $k ],
				$matches[1][ $k ] ),
			strpos( $doc['content'], $matches[0][ $k ] ),
			strlen( $matches[0][ $k ] )
		);
	}
}

// construct the rest of template parts

$parts['{{doc_title}}'] = $doc['title'];
$parts['{{doc_heading}}'] = ( $config['display_chapter'] ? $doc['chapter'] . ' ' : null ) . $doc['title'];
$parts['{{doc_content}}'] = $doc['content'];

if ( LOGGING_LOGGED_IN == $logged )
	$parts['{{logout_link}}'] = sprintf( '<a href="%s">Logout</a>', dw_uri( '_logout' ) );

// generate outline

if ( $config['display_index'] ) {

	$matches = array();
	preg_match_all( '#\<h([1-6]) id=\"([^\"]+)\"\>([^\<]+)\<\/h([1-6])\>#ui', $doc['content'], $matches );

	if ( count( $matches[0] ) ) {
		$headings = array();
		foreach ( array_keys( $matches[0] ) as $k ) {
			$headings[] = array(
				'title' => $matches[3][ $k ],
				'anchor' => $matches[2][ $k ],
				'level' => intval( $matches[1][ $k ] ),
			);
		}
		$heading_index = array();
		$last_level = 0;
		$unclosed = 0;
		foreach ( $headings as $entry ) {
			if ( $entry['level'] > $last_level ) {
				$heading_index[] = '<ul>';
				$unclosed ++;
				$last_level = $entry['level'];
				$heading_index[] = '<li><a href="#' . $entry['anchor'] . '">' . $entry['title'] . '</a>';
			} elseif ( $entry['level'] < $last_level ) {
				if ( $unclosed > 0 ) {
					$heading_index[] = '</li>' . str_repeat( '</ul>', $last_level - $entry['level'] );
					$unclosed = $unclosed - ( $last_level - $entry['level'] );
				}
				$last_level = $entry['level'];
				$heading_index[] = '</li>' . '<li><a href="#' . $entry['anchor'] . '">' . $entry['title'] . '</a>';
			} else {
				$heading_index[] = '</li>' . '<li><a href="#' . $entry['anchor'] . '">' . $entry['title'] . '</a>';
			}
		}
		if ( $unclosed > 0 ) {
			$heading_index[] = '</li>' . str_repeat( '</ul>', $unclosed );
			$unclosed = 0;
		}
		$parts['{{doc_index}}'] = '<div class="content-index"><p class="content-index-title">Contents</p>' . implode( null, $heading_index ) . '</div>';
	}

}

// generate navigation menu

$parts['{{nav}}'] .= '<div class="list-group">';

$top_level_elements = array();
$children_elements  = array();
foreach ( $items as $entry ) {
	if ( empty( $entry['parent'] ) )
		$top_level_elements[] = $entry;
	else
		$children_elements[ $entry['parent'] ][] = $entry;
}

function _display_nav_item( $item, &$children_elements, &$output, &$submenu_number ) {
	global $query_string, $config;
	$item['has_children'] = isset( $children_elements[ $item['chapter'] ] );
	$item['is_current'] = 0 === strpos( $query_string, $item['path'] . '/' );
	$output .= sprintf( '<a class="%s" href="%s"%s%s%s>%s</a>',
		'list-group-item' . ( $query_string === $item['path'] ? ' active' : null ),
		( $item['has_children'] ? '#wiki-nav-' . $submenu_number : dw_uri( $item['path'] ) ),
		( $item['has_children'] ? ' data-toggle="collapse"' : null ),
		( $item['is_current'] ? ' aria-expanded="true"' : null ),
		( 'url' === $item['type'] ? ' target="_blank"' : null ),
		( $config['display_chapter'] ? $item['chapter'] . ' ' : null ) .
			$item['title'] . ( $item['has_children'] ? ' <b class="caret"></b>' : null ) );
	if ( $item['has_children'] )
		foreach( $children_elements[ $item['chapter'] ] as $entry ) {
			if ( ! isset( $new_level ) ) {
				$new_level = true;
				$output .= '<div class="submenu panel-collapse collapse' . ( $item['is_current'] ? ' in' : null ) . '" id="wiki-nav-' . $submenu_number . '">';
				$submenu_number ++;
			}
			_display_nav_item( $entry, $children_elements, $output, $submenu_number );
		}
	if ( isset( $new_level ) ) {
		$output .= '</div>';
	}
	$output .= '';
}

$output_nav = '';
$submenu_number = 1;
foreach ( $top_level_elements as $entry )
	_display_nav_item( $entry, $children_elements, $output_nav, $submenu_number );

$parts['{{nav}}'] .= $output_nav;

$parts['{{nav}}'] .= '</div>';

// load theme template

$template = file_get_contents( $theme_root . '/index.html' );
$output = str_replace( array_keys( $parts ), $parts, $template );

// output html

echo $output;
exit();

