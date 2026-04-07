let templates = [];

tinymce.init({
  selector: "textarea",
  license_key: 'gpl',
  height: 540,
  width: "100%",
  resize: 'both',
  menubar: true,

  toolbar1: "templatesButton | undo redo h2 h3 h4 h5 blocks | bold italic subscript superscript | bullist numlist outdent indent blockquote",
  toolbar2: "table | anchor link unlink openlink | image media | visualblocks visualchars | removeformat code preview fullscreen",

  setup: (editor) => {

    // Register button immediately
    editor.ui.registry.addButton('templatesButton', {
      text: 'Templates',
      onAction: () => openTemplateDialog(editor, templates)
    });

    // Load templates asynchronously
    loadTemplates().then(t => templates = t);
  },

  plugins: "advlist anchor autolink autosave charmap code directionality fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
  entity_encoding: "raw",
  content_css: "tinymce/css/ps_styles.css?v=4",
  keep_styles: false,
  valid_styles: {
    "*": "text-align,margin,margin-left,margin-right,padding,padding-left,padding-right",
    p: "float",
    figure: "float",
    img: "float",
    table: "float",
    ul: "list-style-type",
    ol: "list-style-type",
  },
  valid_elements:
    "div, pre, section, article, code, svg, path," +
    "p[style],span[style],br,br /,strong/b,em/i,u,sup,sub," +
    "figure[style],figcaption," +
    "img[style|src|height|width|alt]," +
    "intent,blockquote[style],ul[style],ol[style],li," +
    "table[style|id],caption,thead,tbody,tfoot,tr,th[colspan|rowspan],td[colspan|rowspan]," +
    "form[id|name|action|method|target],input[type|name|value]," +
    "h1,h2,h3,h4,h5," +
    "a[id|href|target|name|title]," +
    "iframe[title|width|height|src|frameborder=0|allowfullscreen]," +
    "audio[width|height|src|controls]," +
    "video[width|height|src|controls]," +
    "source[src|type]",
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

async function loadTemplates() {
  const response = await fetch('tinymce/templates/templates.php');
  return await response.json();
}

function openTemplateDialog(editor, templates) {
  editor.windowManager.open({
    title: 'Insert Template',
    body: {
      type: 'panel',
      items: [
        {
          type: 'selectbox',
          name: 'template',
          label: 'Choose a template',
          items: templates.map(t => ({
            text: t.title,
            value: t.url
          }))
        }
      ]
    },
    buttons: [
      { type: 'cancel', text: 'Close' },
      { type: 'submit', text: 'Insert' }
    ],
    onSubmit: async (api) => {
      const data = api.getData();
      const url = data.template;

      const html = await fetch(url).then(r => r.text());
      editor.insertContent(html);

      api.close();
    }
  });
}
