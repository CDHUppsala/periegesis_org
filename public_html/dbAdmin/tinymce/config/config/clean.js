tinymce.init({
  selector: "textarea",
  license_key: 'gpl',
  height: 768,
  width: "100%",
  resize: 'both',
  menubar: false,
  plugins:
    "advlist anchor autolink autosave charmap code fullscreen help image importcss link lists media   preview save searchreplace table visualblocks visualchars wordcount",
  toolbar1: "undo redo h2 h3 h4 h5 | bold italic subscript superscript | bullist numlist outdent indent blockquote | charmap searchreplace",
  toolbar2: "table | anchor link unlink openlink | image media  | visualblocks visualchars | removeformat code preview fullscreen",
  entity_encoding: "raw",
  content_css: "css/ps_styles.css?v=2",
  keep_styles: false,
  valid_elements:
    "p,br,br /,strong/b,em/i,u,sup,sub," +
    "figure,figcaption," +
    "img[src|alt]," +
    "intent,blockquote,ul,ol,li," +
    "table[id],caption,thead,tbody,tfoot,tr,th[colspan|rowspan],td[colspan|rowspan]," +
    "h1,h2,h3,h4,h5," +
    "a[id|href|target|name|title]," +
    "audio[src|controls]," +
    "video[src|controls]," +
    "source[src|type]",
  link_default_target: '_blank'
});

