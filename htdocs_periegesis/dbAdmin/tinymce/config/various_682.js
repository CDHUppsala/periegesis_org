tinymce.init({
  selector: 'textarea', // change this according to your HTML
  toolbar: 'language',
  content_langs: [
    { title: 'English', code: 'en' },
    { title: 'Spanish', code: 'es' },
    { title: 'French', code: 'fr' },
    { title: 'German', code: 'de' },
    { title: 'Portuguese', code: 'pt' },
    { title: 'Chinese', code: 'zh' }
  ]
});
/*
base_url
This option lets you specify the base URL for TinyMCE. This is useful if you want to load TinyMCE from one location and things like the theme and plugins from another.

By default, the base_url is the directory containing TinyMCE javascript file (such as tinymce.min.js).

Type: String

Example: Using base_url
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  base_url: '/my/tinymce/dir'
});

/*
cache_suffix
This option lets you add a custom cache buster URL part at the end of each request tinymce makes to load CSS, scripts, etc. Just add the query string part you want to append to each URL request, for example “?v=4.1.6”.

Type: String

Example: Using cache_suffix
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  cache_suffix: '?v=4.1.6'
});

/*
external_plugins
This option allows you to specify a URL based location of plugins outside of the normal TinyMCE plugins directory.

TinyMCE will attempt to load these as per regular plugins when starting up. This option is useful when loading TinyMCE from a CDN or when you want to have the TinyMCE directory separate from your custom plugins.

This value should be set as a JavaScript object that contains a property for each TinyMCE plugin to be loaded. This property should be named after the plugin and should have a value that contains the location that the plugin that will be loaded from.

The URLs provided can be:

Absolute URLs: Including the protocol, such as https://www.example.com/plugin.min.js.
Relative to the root directory of the web-server: Including the leading “/” to indicate that it is relative to the web-server root, such as /plugin.min.js.
Relative to the TinyMCE base_url: A relative path without the leading “/”, such as ../../myplugins/plugin.min.js. By default, the base_url is the directory containing TinyMCE javascript file (such as tinymce.min.js). For information on the base_url option, see: Integration and setup options - base_url.
Type: Object

Example: Using external_plugins
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  external_plugins: {
    'testing': 'http://www.testing.com/plugin.min.js',
    'maths': 'http://www.maths.com/plugin.min.js'
  }
});

/*
This plugin automatically resizes the editor to the content inside it. 
It is typically used to prevent the editor from expanding infinitely as a user types into the editable area. 
For example, by giving the max_height option a value the editor will stop resizing when the set value is reached.
Basic setup
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  plugins: 'autoresize'
});

tinymce.init({
    selector: 'textarea',  // change this value according to your HTML
    plugins: 'autoresize',
    autoresize_bottom_margin: 50
  });

  tinymce.init({
    selector: 'textarea',  // change this value according to your HTML
    plugins: 'autoresize',
    autoresize_overflow_padding: 50
  });


  /*
  max_height
The max_height option has two kinds of behaviors depending on the state of the autoresize plugin:

autoresize OFF (Default) : Without the autoresize plugin, this option allows you to set the maximum height that a user can stretch the entire TinyMCE interface (by grabbing the dragable area in the bottom right of the editor interface).

autoresize ON : With the autoresize plugin, this option sets the maximum height the editor can automatically expand to.

Note: If you set the option resize to false the resize handle will be disabled and a user will not be able to resize the editor (by manual dragging). Note that resize defaults to false when the autoresize plugin is enabled.
Type: Number

Example: Using max_height
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  max_height: 500
});


/*
=======================================================
*/


/*Example grouped toolbar */
tinymce.init({
    selector: 'textarea',  // change this value according to your HTML
    toolbar: 'undo redo | styleselect | bold italic | link image'
  });

/*Example: Adding toolbar group labels*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  toolbar: [
    {
      name: 'history', items: [ 'undo', 'redo' ]
    },
    {
      name: 'styles', items: [ 'styleselect' ]
    },
    {
      name: 'formatting', items: [ 'bold', 'italic']
    },
    {
      name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ]
    },
    {
      name: 'indentation', items: [ 'outdent', 'indent' ]
    }
  ]
});

/*Example: Adding multiple toolbars*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  toolbar: [
    'undo redo | styleselect | bold italic | link image',
    'alignleft aligncenter alignright'
  ]
});

/*Example: Using toolbar(n) */
tinymce.init({
  selector: 'textarea',  // change this value according to your html
  toolbar1: 'undo redo | styleselect | bold italic | link image',
  toolbar2: 'alignleft aligncenter alignright'
});

/*
======================================
*/

/*
Settings fo Toolbar Mode
The toolbar mode is specified in the tinymce.init. There are four toolbar modes:

Floating (default)
Sliding
Scrolling
Wrap
*/
tinymce.init({
    selector: 'textarea',
    toolbar_mode: 'floating'
});

/*
toolbar_groups
Note: This feature is only available for TinyMCE 5.2 and later.

The toolbar_groups option creates a toolbar button that displays a collection of other toolbar buttons as a pop-up toolbar. The style of toolbar shown is based on the current toolbar mode. For example, if toolbar_mode is set to floating, the toolbar group pop-up will appear in a floating shelf.

Note: The toolbar_groups feature is only supported when using the floating toolbar mode. If the toolbar_groups option is used with other toolbar modes, the toolbar group button will not be displayed and a warning message will be printed in the console.

This option accepts an object, mapping the button name to the group configuration. For details on configuring toolbar groups, see: group toolbar button configuration.

Type: Object

Example: toolbar_groups
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  toolbar: 'formatting | alignleft aligncenter alignright',
  toolbar_groups: {
    formatting: {
      icon: 'bold',
      tooltip: 'Formatting',
      items: 'bold italic underline | superscript subscript'
    }
  }
});

/*
===============================================
*/
/*
width
Set the width of the editor.

Note: TinyMCE sets the width in pixels if a number is provided. However, if TinyMCE is provided a string it assumes the value is valid CSS and simply sets the editor’s width as the string value. This allows for alternate units such as %, em and vh.

Type: Number or String

Example: Using width
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  width : 300
});


/*
====================================================
*/

/*
mplates
This option lets you specify a predefined list of templates to be inserted by the user into the editable area. 
It is structured as an array with each item having a title, description and content/url.

If this option is a string then it will be requested as a URL that should produce a JSON output in the same format the option accepts.

Each item in the list can either be inline using a content property or a whole file using the url property.

Example using templates object
*/
tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  plugins: 'template',
  menubar: 'insert',
  toolbar: 'template',
  templates: [
    {title: 'Some title 1', description: 'Some desc 1', content: 'My content'},
    {title: 'Some title 2', description: 'Some desc 2', url: 'development.html'}
  ]
});

/*
Example using templates URL */

tinymce.init({
  selector: 'textarea',  // change this value according to your HTML
  plugins: 'template',
  menubar: 'insert',
  toolbar: 'template',
  templates: '/dir/templates.php'
});

/*
Example JSON output of templates.php
*/
[
  {"title": "Some title 1", "description": "Some desc 1", "content": "My content"},
  {"title": "Some title 2", "description": "Some desc 2", "url": "development.html"}
]


tinymce.init({
    selector: 'textarea#template',
    height: 600,
    plugins: 'template',
    menubar: 'insert',
    toolbar: 'template',
    template_mdate_format: '%m/%d/%Y : %H:%M',
    template_replace_values: {
      username: 'Jack Black',
      staffid: '991234',
      inboth_username: 'Famous Person',
      inboth_staffid: '2213',
    },
    template_preview_replace_values: {
      preview_username: 'Jack Black',
      preview_staffid: '991234',
      inboth_username: 'Famous Person',
      inboth_staffid: '2213',
    },
    templates : [
      {
        title: 'Date modified example',
        description: 'Adds a timestamp indicating the last time the document modified.',
        content: '<p>Last Modified: <time class="mdate">This will be replaced with the date modified.</time></p>'
      },
      {
        title: 'Replace values example',
        description: 'These values will be replaced when the template is inserted into the editor content.',
        content: '<p>Name: {$username}, StaffID: {$staffid}</p>'
      },
      {
        title: 'Replace values preview example',
        description: 'These values are replaced in the preview, but not when inserted into the editor content.',
        content: '<p>Name: {$preview_username}, StaffID: {$preview_staffid}</p>'
      },
      {
        title: 'Replace values preview and content example',
        description: 'These values are replaced in the preview, and in the content.',
        content: '<p>Name: {$inboth_username}, StaffID: {$inboth_staffid}</p>'
      }
    ],
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
  });
  
  
