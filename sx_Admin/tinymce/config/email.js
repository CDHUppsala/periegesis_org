tinymce.init({
    selector: 'textarea',
    height: 500,
    menubar: false,
    plugins: [
        'advlist autolink lists link image print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code wordcount'
    ],
    toolbar: 'undo redo | formatselect | ' +
        'bold italic | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent blockquote | ' +
        'removeformat | table | media image | link anchor | code preview fullscreen',
    entity_encoding: "raw",
    block_formats: "Paragraph=p;Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
    content_style: 'body {font-family:"Segoe UI",Roboto,Oxygen,Ubuntu,Arial,"Open Sans","Helvetica Neue",sans-serif;font-size:20px}',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    keep_styles: true,
    valid_elements: "p[style],span[style],br,br /,strong/b,em/i,u,sup,sub," +
        "figure[style],figcaption[style]," +
        "img[src|alt|style|width|height]," +
        "intent,blockquote,ul[style],ol[style],li," +
        "table[id|style|width],caption[style],thead,tbody,tfoot,tr,th[colspan|rowspan|style],td[colspan|rowspan|style|width]," +
        "h1,h2,h3,h4,h5," +
        "a[id|style|href|target|name]," +
        "audio[width|height|src|controls|style]," +
        "video[width|height|src|controls|style]," +
        "source[src|type]," +
        "param[name|value],",

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
    }]
});