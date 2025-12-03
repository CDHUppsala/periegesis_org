tinymce.init({
    selector: 'textarea',
    height: 560,
    menubar: false,
    plugins: 'advlist autolink lists link image preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code wordcount',
    toolbar: 'blocks undo redo bold italic | bullist numlist | table media image link | removeformat code preview fullscreen',
    entity_encoding: "raw",
    block_formats: "Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
    content_style: 'body { font-family:"Segoe UI",Helvetica,Arial,sans-serif; font-size:20px }',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    keep_styles: true,
    valid_elements: "p[style],span[style],br,br /,strong/b,em/i,u,sup,sub," +
        "figure[style],figcaption[style]," +
        "img[src|alt|style|width|height]," +
        "ul[style],ol[style],li," +
        "table[id|style|width],caption[style],thead,tbody,tfoot,tr,th[colspan|rowspan|style],td[colspan|rowspan|style|width]," +
        "h1,h2,h3,h4,h5," +
        "a[id|style|href|target|name]," +
        "audio[width|height|src|controls|style]," +
        "video[width|height|src|controls|style]," +
        "source[src|type]," +
        "param[name|value],",

    link_list: [{
            title: 'Link to a Text ID',
            value: 'articles.php?tid='
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
            value: 'ps_PDF.php?archID='
        },
        {
            title: 'Link to a Gallery ID',
            value: 'ps_gallery.php?int1='
        },
        {
            title: 'Link to a Media-File ID',
            value: 'ps_media.php?archID='
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