tinymce.init({
    selector: 'textarea',
    height: 680,
    menubar: false,
    plugins: 'advlist autolink lists link preview searchreplace visualblocks fullscreen insertdatetime table code wordcount ',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote | ' +
    'removeformat | table | code preview fullscreen',
    entity_encoding: "raw",
    content_css: '../tinymce/css/ps_styles.css',
    block_formats: "Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
    content_style: 'body { font-family:"Segoe UI",Helvetica,Arial,sans-serif; font-size:20px }',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    keep_styles: true,
    valid_elements: "aside[class],p,br,br /,strong/b,em/i,u," +
        "figure[class],figcaption[class]," +
        "img[src|alt]," +
        "intent,blockquote,ul,ol,li," +
        "table[id],caption,thead,tbody,tfoot,tr,th[colspan|rowspan],td[colspan|rowspan]," +
        "h1,h2,h3,h4,h5," +
        "a[id|href|target|name]," +
        "audio[src|controls|style]," +
        "video[src|controls|style]," +
        "source[src|type]," +
        "param[name|value]"
});