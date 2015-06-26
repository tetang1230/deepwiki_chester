# DeepWiki

A lightweight wiki system, based on Markdown, coded in PHP.

- **Contributor**: [Yuan Chong](http://ychong.com/)
- **License**: [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)
- **Live Demo**: [http://deepwiki.deepdevelop.com/](http://deepwiki.deepdevelop.com/)


## Installation

1. [Download the latest release of DeepWiki](https://github.com/ychongsaytc/deepwiki/releases).
2. Unarchive package.
3. Done.

## Quick Start

1. [Make a configuration file from the sample](#/configuration).
2. [Write down some Markdown files](#/documents-structure) into `deepwiki-docs/`.
3. Run it in PHP. 

## Configuration

Main configuration is placed in `deepwiki-config/config.json`, can be made from the sample file `deepwiki-config/config-sample.json`:

```json
{
	"site_name": "DeepWiki",
	"site_description": "Markdown Documents Showcase",
	"copyright": "Powered by <a href=\"https://github.com/ychongsaytc/deepwiki\" target=\"_blank\">DeepWiki</a>.",
	"theme": "default",
	"docs_path": "deepwiki-docs-example",
	"home_route": "quick-start",
	"display_chapter": false,
	"display_index": false,
	"rewrite": false,
	"footer_code": "",
	"password": "",
	"cookie_salt": "REPLACE_THIS_WITH_A_RANDOM_STRING",
	"docs": {}
}
```

Property | Description
--- | ---
`site_name` | Title of the website. Defaults to `'DeepWiki'`.
`site_description` | Short description of the website Defaults to `'Markdown Documents Showcase'`.
`copyright` | Copyright text in footer HTML format. Defaults to `'Powered by <a href="https://github.com/ychongsaytc/deepwiki" target="_blank">DeepWiki</a>.'`.
`theme` | Slug name of current theme, must be matched a directory name in `deepwiki-themes/` (see [Theme Development](#/theme-development)). Defaults to `'default'`.
`docs_path` | Directory to find document files. Defaults to `'deepwiki-docs-example'`.
`home_route` | The default route (a path to the landing document) for root page visits. Defaults to `'quick-start'`.
`display_chapter` | Display chapter number (like `1.1.a.`) before document title. Defaults to `false`.
`display_index` | Display contents index navigation (based on content outline). Defaults to `false`.
`rewrite` | Enable global URL Rewrite (see [URL Rewrite](#/url-rewrite) to enable rewrite feature for your server). Defaults to `false`.
`footer_code` | HTML code at the end of `<body>`, can be placed your Google Analytics code, ["Fork me on GitHub"](https://github.com/blog/273-github-ribbons) badge, and anything you want. Defaults to empty.
`password` | Main password to view the website, fill in this to enable site authentication. Defaults to empty.
`cookie_salt` | A random string for encrypt cookies data, important. Defaults to empty.
`docs` | Specify all document titles, filenames and slug names in an array. See [Define all documents in configuration](#/writing-documents). Defaults to empty.

### Directory Structure

```
deepwiki-docs/                   Document files in Markdown, plain text, HTML
deepwiki-assets/                 Asset files (e.g. images)
deepwiki-config/config.json      Main configuration file in JSON
deepwiki-themes/                 DeepWiki themes
               /default/         The default theme of DeepWiki
               /xxx/             A theme named xxx
deepwiki-vendor/                 The necessary components
```

## Writing Documents

All documents must be placed in DeepWiki documents directory (defaults to `deepwiki-docs/`, can be changed in [DeepWiki configuration](#/configuration)).

### Arrange your documents through file naming

Basic way to arrange your documents, will be overwrite by the definition in DeepWiki configuration (see *Define all documents in configuration* below).

#### Full functional naming example

```
1. Parent Page One [parent-1].markdown
1.1. Child Page One [child-1].markdown
1.2. Child Page Two [child-2].markdown
1.2.1. Grandchild Page One [grandchild-1].markdown
1.2.2. Grandchild Page Two [grandchild-2].markdown
1.2.3. Grandchild Page Three [grandchild-3].markdown
1.3. Child Page Two [child-3].markdown
2. Parent Page Two [parent-2].markdown
3. Parent Page Three [parent-3].markdown
```

1. **Chapter Number**: (optional) Using digits or letters ended with a point (`.`). If not set, the document will act in flat hierarchy.
2. **Document Title**: (mandatory) The title of document.
3. **Document Slug Name**: (optional) A string to be the identifier (or short name) of the document, enclosed by a pair of square brackets (`[` and `]`). Leave this blank to use sanitized document title as slug name.
4. **Document File Extension Name**: (mandatory) Possible values are: `.markdonw`, `.md`, `.mdown`, `.txt`, `.html`, etc.

#### More examples

Pure titles, without hierarchy

```
Umentia.md
Ventis.md
Boreas.md
Bracchia.md
Congestaque.md
```

Using letters as chapter, with hierarchy

```
A. Crescendo.md
B. Mundum.md
C. Fulgura.md
D. Habendum.md
D.1. Discordia.md
D.2. Instabilis.md
D.3. Erectos.md
D.3.a. Utramque.md
D.3.b. Flamma.md
E. Scythiam.md
```

### Define all documents in configuration

Sample configuration (`docs` node in [DeepWiki configuration](#/configuration)):

```json
{
	"docs": {
		"home": {
			"title": "Home",
			"file": "home.md"
		},
		"products": {
			"title": "Products",
			"file": "",
			"children": {
				"category-a": {
					"title": "Category A",
					"file": "product-category-a.md"
				},
				"category-b": {
					"title": "Category B",
					"file": "product-category-b.md"
				}
			}
		},
		"global-site": {
			"title": "Global Site",
			"file": "http://example.com/"
		}
	}
}
```

### Inner page linking and asset files linking

DeepWiki will perform transforms after document content is parsed. There are two transformers:

Type | Description
--- | ---
Inner Link | Parse the URL into a website URI.
Assets | Parse the URL into an assets path based URI.

#### Examples

Think about the website root URL is `http://example.com/path/to/wiki/`:

- `[Contact Us](#/about/contact-us)` will be parsed into:

	```html
	<a href="/path/to/wiki/about/contact-us">Contact Us</a>
	```
- `[Click to view full size](!/full-size-image.jpg)` will be parsed into:

	```html
	<a href="/path/to/wiki/deepwiki-assets/full-size-image.jpg">Click to view full size</a>
	```
- `![Website Logo](!/logo.png)` will be parsed into:

	```html
	<img src="/path/to/wiki/deepwiki-assets/logo.png" alt="Website Logo" />
	```


## Auto Deployment for Documents

If you are using Git to manage your documents, you can setup an auto deployment tool to deploy files onto DeepWiki.

[**DeepDeploy**](https://deepdeploy.com/) is such an Auto Deployment system, deploying Git repository to any server (via FTP/SFTP). It's easy to use:

1. Go to [DeepDeploy](https://deepdeploy.com/), sign in and create a project.
1. Add your repository contains document file to the project.
1. Add your DeepWiki server FTP/SFTP information to Server section, with setting Path to Deploy to the DeepWiki document files path (e.g. `/deepwiki-docs` or `/home/ubuntu/public_html/deepwiki-docs`).
1. Save the project and trigger your first auto deployment.

## Theme Development

DeepWiki allow you to custom your own template. The files structure is:

```
deepwiki-themes/xxx/             A theme named xxx
               /xxx/theme.json   Theme configuration in JSON
               /xxx/index.html   Document page template
               /xxx/404.html     Not Found (404 HTTP status) page template
               /xxx/login.html   User Login page template
```

Sample theme configuration:

```json
{
	"name": "A DeepWiki Theme",
	"assets": {
		"css": [
			"css/web.css"
		],
		"js": [
			"js/web.js"
		]
	}
}
```

Functional elements in template files:

Identifier | Description
--- | ---
`{{site_name}}` | The website name, defined in [DeepWiki configuration](#/configuration).
`{{site_description}}` | The description of website, defined in [DeepWiki configuration](#/configuration).
`{{site_uri}}` | Current website root relative url, with a slash ending.
`{{html_head}}` | Necessary general HTML tags in `<head>`.
`{{nav}}` | The navigation menu HTML, formatted in Bootstrap style.
`{{doc_title}}` | Current document title.
`{{doc_heading}}` | Current document title maybe with the chapter (defined in [DeepWiki configuration](#/configuration)).
`{{doc_content}}` | Current document content in HTML.
`{{copyright}}` | The copyright text, defined in [DeepWiki configuration](#/configuration). Must be escaped manually.
`{{body_footer}}` | The page footer HTML code, defined in [DeepWiki configuration](#/configuration).
`{{logout_link}}` | Output a logout link when needed and Logging feature is enabled.
`{{login_form}}` | The login form HTML code, formatted in Bootstrap style.


## URL Rewrite

### For Apache HTTP Server

Place the code in the `/.htaccess` file.

```
# prevent directory listing
Options -Indexes

# custom error documents
ErrorDocument 404 index.php\?p=_404
ErrorDocument 403 index.php\?p=_403

<IfModule mod_rewrite.c>
RewriteEngine on

# change / to your DeepWiki relative directory path, eg. /path/to/wiki/
RewriteBase /

# prevent illegal request
RewriteRule ^deepwiki-config/(.*)$ index.php\?p=_403 [L]
RewriteRule ^deepwiki-docs/(.*)$ index.php\?p=_403 [L]

# rewrite non-exist path to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php\?p=$1 [QSA,L]

</IfModule>
```

### For Nginx

Place the code inside the `http { ... }` node in Nginx configuration file.

```
server {

	# bind to http://example.com on port 80
	listen 80;
	server_name example.com;

	# change this to your DeepWiki root path
	root /var/www/deepwiki;

	# prevent illegal request
	location ~ /(deepwiki-config|deepwiki-docs) {
		deny all;
	}

	# rewrite non-exist path to index.php, or return HTTP 404 Not Found
	location / {
		try_files $uri $uri/ /index.php?p=$uri&$args;

		# custom error documents
		error_page 404 = /index.php\?p=_404;
		error_page 403 = /index.php\?p=_403;
	}

	# example of passing request to FastCGI
	fastcgi_param PHP_VALUE "open_basedir=$document_root:/tmp/";
	location ~ \.php$ {
		fastcgi_pass unix:/tmp/fastcgi-php.socket;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		include fastcgi_params;
	}

	# prevent from requesting Apache HTTP Server configuration files
	location ~ /\.ht {
		deny all;
	}

}
```

## Change Log

### 1.1.1 (beta2) 2014-12-08

- **NEW**: Supported to generate content outline index
- **NEW**: Automatically add anchor to content headings

### 1.1.1 (beta1) 2014-12-07

- **NEW**: Supported Responsive CSS

### 1.1.0 (beta2) 2014-12-06

- **FIXED**: Redirection bug for root visits

### 1.1.0 (beta1) 2014-12-06

- **NEW**: Allowed to define all document titles, filenames and slug names in configuration instead of using file naming
- **NEW**: Supported inner page linking and asset files linking in document content
- **NEW**: Implemented Prism to highlight code blocks in DeepWiki Default Theme
- **NEW**: Minified all assets files in DeepWiki Default Theme
- **NEW**: Included necessary components in DeepWiki Git repository for easier installation

### 1.0.0 (beta1) 2014-11-14

- **NEW**: Born

## Credits

### Thanks to

- [Parsedown](http://parsedown.org/) (implemented to DeepWiki)
- [Bootstrap](http://getbootstrap.com/) (implemented to DeepWiki Default Theme)
- [jQuery](http://jquery.com/) (implemented to DeepWiki Default Theme)
- [Prism](http://prismjs.com/) (implemented to DeepWiki Default Theme)
- [GitHub Markdown CSS](https://github.com/revolunet/sublimetext-markdown-preview) (implemented to DeepWiki Default Theme)
