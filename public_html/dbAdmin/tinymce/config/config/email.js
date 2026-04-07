tinymce.init({
  selector: "textarea",
  license_key: 'gpl',
  height: 740,
  width: "100%",
  resize: 'both',
  menubar: false,
  plugins:
    "advlist autolink autosave code directionality fullscreen help image importcss insertdatetime link lists nonbreaking pagebreak preview quickbars save table wordcount",
  toolbar1: "undo redo h1 h2 h3 h4 | bold italic | bullist table | link unlink openlink | image | code preview fullscreen",
  entity_encoding: "raw",
  content_css: "../tinymce/css/ps_styles.css?v=2",
  keep_styles: false,
  valid_elements:
    "p,br,br /,strong/b,em/i,u,sup,sub," +
    "img[src|target|height|width|alt]," +
    "ul,ol,li," +
    "table[id],caption,thead,tbody,tfoot,tr," +
      "th[colspan|rowspan|valign|align|width]]," +
      "td[colspan|rowspan|valign|align|width]," +
    "h1,h2,h3,h4,h5," +
    "a[href|target|title]",
  link_default_target: '_blank',
  link_list: [
    {
      title: "Link to a Text ID",
      value: "articles.php?tid=",
    },
    {
      title: "Link to a About ID",
      value: "about.php?aboutid=",
    },
    {
      title: "Link to a Conference ID",
      value: "conferences.php?confid=",
    },
    {
      title: "Link to a Product ID",
      value: "products.php?pid=",
    },
    {
      title: "Link to a Local PDF-File",
      value: "../imgPDF/",
    },
    {
      title: "Link to a PDF-File ID",
      value: "ps_PDF.php?archID=",
    },
    {
      title: "Link to a Gallery ID",
      value: "ps_gallery.php?int1=",
    },
    {
      title: "Link to a Media-File ID",
      value: "ps_media.php?archID=",
    },
    {
      title: "Link to a Book ID",
      value: "inBooks.php?bookID=",
    },
    {
      title: "External Link",
      value: "http://",
    },
    {
      title: "External Secure Link",
      value: "https://",
    }
  ],
  image_list: [
    {
      title: "From Images",
      value: "../images/",
    },
    {
      title: "From Images About",
      value: "../images/about/",
    },
    {
      title: "From Images Articles",
      value: "../images/articles/",
    },
    {
      title: "From Images Events",
      value: "../images/events/",
    },
    {
      title: "From Images Books",
      value: "../images/book/",
    },
    {
      title: "From Gallery",
      value: "../imgGallery/",
    },
  ]
});
