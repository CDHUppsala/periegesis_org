tinymce.init({
    selector: 'textarea',
    license_key: 'gpl',
    height: 500,
    menubar: false,
    plugins: "autoresize advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount ",
    toolbar: "undo redo | bold italic | bullist numlist outdent indent | removeformat code fullscreen",
    content_style: 'body { font-family:"Segoe UI",Helvetica,Arial,sans-serif; font-size:18px }',
    entity_encoding: "raw",
    autoresize_bottom_margin: 50,
    min_height: 400,
    max_height: 580

});