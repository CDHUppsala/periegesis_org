tinymce.init({
    selector: 'textarea',
    height: 280,
    width: '100%',
    menubar: false,
    plugins: [
        'advlist autolink lists link print preview',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime paste code help wordcount'
    ],
    toolbar: 'undo redo | bold italic | bullist numlist | removeformat | code preview fullscreen ',
    entity_encoding: "raw",
    content_style: 'body {font-family:"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Open Sans","Helvetica Neue",sans-serif;font-size:20px}',
    keep_styles: false,

    valid_elements: "p[style],span[style],br,br /,strong/b,em/i,u"
});