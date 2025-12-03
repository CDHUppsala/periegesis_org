tinymce.init({
    selector: 'textarea',
    height: 740,
    width: '100%',
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
    ],
    toolbar1: 'undo redo | formatselect | ' +
        'bold italic underline backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent blockquote | ' +
        'removeformat | help',
    toolbar2: 'table | media image link anchor | charmap | code preview fullscreen',
    entity_encoding: "raw",
    content_style: 'body {font-family:"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Open Sans","Helvetica Neue",sans-serif;font-size:20px}',
    keep_styles: false,

    valid_elements: "p[style],span[style],br,br /,strong/b,em/i,u,sup,sub," +
        "figure[style],figcaption[style]," +
        "img[src|alt|style]," +
        "intent,blockquote,ul[style],ol[style],li," +
        "table[id|style],caption[style],thead,tbody,tfoot,tr,th[colspan|rowspan|style],td[colspan|rowspan|style]," +
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