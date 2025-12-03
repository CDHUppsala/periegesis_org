tinymce.init({
  selector: "textarea",
  height: 540,
  width: "100%",
  menubar: false,
  plugins:
    "advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media template table code help wordcount importcss",
  toolbar1:
    "blocks formatselect | bold italic | subscript superscript | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify blockquote ",
  toolbar2:
    "undo redo | table | media image template | link anchor | removeformat | code | preview fullscreen",
  entity_encoding: "raw",
  content_css: "tinymce/css/ps_styles.css?v=2",
  block_formats: "Paragraph=p;Header 2=h2;Header 3=h3;Header 4=h4;Header 5=h5;",
  content_style:
    'body {font-family:"Segoe UI",Helvetica,Arial,sans-serif;font-size:20px}',
  keep_styles: false,
  valid_styles: {
    "*": "text-decoration,text-align,margin-left,margin-right",
    img: "float",
    table: "float",
    ul: "list-style-type",
    ol: "list-style-type",
  },

  valid_elements:
    "section[class],aside[class],p[class|style],span[style],br,br /,strong/b,em/i,u,sup,sub," +
    "figure[class],figcaption," +
    "img[src|height|width|style|alt]," +
    "intent,blockquote[style],ul[style],ol[style],li," +
    "table[id|class|style],caption,thead,tbody,tfoot,tr,th[colspan|rowspan],td[colspan|rowspan|class]," +
    "form[id|name|action|method|target],input[type|name|value]," +
    "h1,h2[style],h3[style],h4[style],h5," +
    "a[id|class|href|target|name|title]," +
    "iframe[title|class|width|height|src|frameborder=0|allowfullscreen]," +
    "object[class|classid|width|height|src|codebase|type]," +
    "embed[width|height|name|src|wmode|pluginspage|type]," +
    "audio[width|height|src|controls]," +
    "video[width|height|src|controls]," +
    "source[src|type]," +
    "param[name|value]," +
    "script",

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
    },
  ],
  image_list: [
    {
      title: "From Images",
      value: "../images/",
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
  ],
  media_list: [
    {
      title: "From Media",
      value: "../imgMedia/",
    },
  ],
  templates: "tinymce/templates/templates.php"
});
