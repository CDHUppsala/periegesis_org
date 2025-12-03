tinymce.init({
    selector: 'textarea',
    height: 540,
    width:'100%',
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media template table paste code help wordcount importcss'
    ],
    toolbar1: 'formatselect | bold italic | subscript superscript | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify blockquote ',
    toolbar2: 'undo redo | table | media image template | link anchor | removeformat | code | preview fullscreen ',
    entity_encoding: "raw",
    content_css: 'tinymce/css/ps_styles.css?v=2',
    block_formats: "Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
    content_style: 'body {font-family:"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Open Sans","Helvetica Neue",sans-serif;font-size:20px}',
    keep_styles: false,
    valid_styles: {
        "*": "text-decoration,text-align,margin-left,margin-right",
        "img": "float",
        "table": "float",
        "ul": "list-style-type",
        "ol": "list-style-type"
    },

    valid_elements: "section[class],aside[class],p[class|style],span[style],br,br /,strong/b,em/i,u,sup,sub," +
        "figure[class],figcaption," +
        "img[src|border|height|width|style|alt]," +
        "intent,blockquote[style],ul[style],ol[style],li," +
        "table[id|class|style],caption,thead,tbody,tfoot,tr,th[colspan|rowspan],td[colspan|rowspan|class]," +
        "form[id|name|action|method|target],input[type|name|value]," +
        "h1,h2[style],h3[style],h4[style],h5," +
        "a[id|class|href|target|name]," +
        "iframe[title|class|width|height|src|frameborder=0|allowfullscreen]," +
        "object[class|classid|width|height|src|codebase|type]," +
        "embed[width|height|name|src|wmode|pluginspage|type]," +
        "audio[width|height|src|controls]," +
        "video[width|height|src|controls]," +
        "source[src|type]," +
        "param[name|value]," +
        "script",

    link_list: [{
            title: 'Link to a Text ID',
            value: 'texts.php?tid='
        },
        {
            title: 'Link to a Article ID',
            value: 'articles.php?aid='
        },
        {
            title: 'Link to a Item ID',
            value: 'items.php?itemid='
        },
        {
            title: 'Link to a About ID',
            value: 'about.php?aboutid='
        },
        {
            title: 'Link to a Conference ID',
            value: 'conferences.php?confid='
        },
        {
            title: 'Link to a Product ID',
            value: 'products.php?pid='
        },
        {
            title: 'Link to a PDF-File ID',
            value: 'sxPlug_PDF.php?archID='
        },
        {
            title: 'Link to a Gallery ID',
            value: 'sxPlug_Gallery.php?int1='
        },
        {
            title: 'Link to a Media-File ID',
            value: 'sxPlug_Media.php?archID='
        },
        {
            title: 'Link to a Book ID',
            value: 'inBooks.php?bookID='
        },
        {
            title: 'External Link',
            value: 'http://'
        },
        {
            title: 'External Secure Link',
            value: 'https://'
        }
    ],
    image_list: [{
            "title": 'From Images',
            "value": '../images/'
        },
        {
            "title": 'From Images Events',
            "value": '../images/events/'
        },
        {
            "title": 'From Images Books',
            "value": '../images/book/'
        },
        {
            "title": 'From Gallery',
            "value": '../imgGallery/'
        }
    ],
    media_list: [{
        title: 'From Media',
        'value': '../imgMedia/'
    }],
    templates: [
        {title: "Register to Participate in Event", description: "Write in the Link the Event ID to open the Registration Form", url: "templates/event_registration.htm"},
		{title: "Image-Caption Center", description: "Image with Caption - Text is placed before and after the image", url: "tinymce/templates/figure_center.htm?v=2"},
		{title: "Image-Caption Left", description: "Image with Caption - Text is placed on the Right of the image", url: "tinymce/templates/figure_left.htm?v=2"},
		{title: "Image-Caption Right", description: "Image with Caption - Text is placed on the Left of the image", url: "tinymce/templates/figure_right.htm?v=2"},
		{title: "Images in Table Top", description: "Images in First row, Text in Next", url: "tinymce/templates/image_in_table_top.htm?v=2"},
		{title: "Images in Table Left", description: "Images with same width on the Left", url: "tinymce/templates/image_in_table_left.htm?v=2"},
		{title: "Images in Table Right", description: "Images with same width on the Left", url: "tinymce/templates/image_in_table_right.htm?v=2"},
		{title: "Background Center", description: "Centered Text in gray background color", url: "tinymce/templates/bg_center.htm?v=2"},
		{title: "Background Left", description: "Left Text in gray background color", url: "tinymce/templates/bg_left.htm?v=2"},
		{title: "Background Right", description: "Right Text in gray background color", url: "tinymce/templates/bg_right.htm?v=2"},
		{title: "Table - No Headers", description: "Table with 3 Columns", url: "tinymce/templates/table_NoHeaders.htm?v=2"},
		{title: "Table - Top Headers", description: "Table with Headers on Top", url: "tinymce/templates/table_HeadTop.htm?v=2"},
		{title: "Table - Left Headers", description: "Table with Headers on Left", url: "tinymce/templates/table_HeadLeft.htm?v=2"},
		{title: "YouTube Embed Center", description: "Change the crs of the embeded URL to YouTube", url: "tinymce/templates/object_youtube.htm?v=2"},
		{title: "YouTube Embed Left", description: "Change the crs of the embeded URL to YouTube", url: "tinymce/templates/object_youtube_left.htm?v=2"},
		{title: "Video MP4/OGG Center", description: "Change the Source (crs) - media type .mp4 and .ogg", url: "tinymce/templates/object_video.htm?v=2"},
		{title: "Video MP4/OGG Left", description: "Change the Source (crs) - media type .mp4 and .ogg", url: "tinymce/templates/object_video_left.htm?v=2"},
		{title: "Audio-Music MP3/OGG Center", description: "Change the Source (crs) - media type .mp3, .mpeg and .ogg", url: "tinymce/templates/object_audio.htm?v=2"},
		{title: "Audio-Music MP3/OGG Left", description: "Change the Source (crs) - media type .mp3, .mpeg and .ogg", url: "tinymce/templates/object_audio_left.htm?v=2"},
		{title: "Poems marked by Rows", description: "Poems where every 5th Number of Rows, marked as strong, excedes to the left", url: "tinymce/templates/poem_numbers.htm?v=2"}
	]
    /*
    templates: [{
        title: 'Centered Media',
        description: 'Widescreen Image at the Center of text with description',
        content: '<table class="image_center"><tr><td><img src="../images/sx/w.jpg"></td></tr><tr><td>Change the Media above and the text here!</td></tr></table>'
    },
    {
        title: 'Left Media',
        description: 'Vertical Image at the Left of text with description',
        content: '<table class="image_left"><tr><td><img src="../images/sx/v.jpg"></td></tr><tr><td>Change the Media above and the text here!</td></tr></table>'
    },
    {
        title: 'Right Media',
        description: 'Vertical Image at the Right of text with description',
        content: '<table class="image_right"><tr><td><img src="../images/sx/v.jpg"></td></tr><tr><td>Change the Media above and the text here!</td></tr></table>'
    }]
    */
});