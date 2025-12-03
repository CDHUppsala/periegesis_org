tinymce.init({
    selector: 'textarea',
    height: 600,
    width: '100%',
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
    ],
    toolbar: 'undo redo | formatselect | ' +
        'bold italic | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent blockquote | ' +
        'removeformat | help' +
        'table | media image | link anchor | code preview fullscreen',
    entity_encoding: "raw",
    block_formats: "Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
    content_style: 'body {font-family:"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Open Sans","Helvetica Neue",sans-serif;font-size:20px}',
    keep_styles: false

});