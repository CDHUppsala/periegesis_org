tinymce.init({
    selector: 'textarea',
    height: 500,
    plugins: 'autoresize advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table emoticons help ',
    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons | help',
    menu: {
        favs: {
            title: 'My Favorites',
            items: 'code visualaid | searchreplace | emoticons'
        }
    },
    menubar: 'favs file edit view insert format tools table help',
    toolbar_sticky: true,
    block_formats: "Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
    content_style:
      'body {font-family:"Segoe UI",Helvetica,Arial,sans-serif;font-size:20px}',
      content_css: 'tinymce/css/content.css'
});