tinymce.init({
  selector: "textarea",
  license_key: 'gpl',
  height: 580,
  width: "100%",
  resize: 'both',
  menubar: false,
  plugins:
    "advlist autolink autosave charmap code fullscreen help importcss lists save table wordcount",
  toolbar1: "h1 h2 h3 h4 h5 | bold italic | bullist numlist outdent indent | charmap table | code fullscreen",
  entity_encoding: "raw",
  content_css: "../tinymce/css/ps_styles.css?v=2",
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

