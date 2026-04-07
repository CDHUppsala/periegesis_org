tinymce.init({
  selector: "textarea",
  license_key: 'gpl',
  height: 540,
  width: "100%",
  resize: 'both',
  menubar: false,
  plugins:
    "advlist anchor autolink autosave charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table visualblocks visualchars wordcount",
  toolbar1: "undo redo h2 h3 h4 h5 blocks | bold italic subscript superscript | bullist numlist outdent indent blockquote | charmap searchreplace",
  toolbar2: "table | anchor link unlink openlink | image media  | visualblocks visualchars | codesample removeformat code preview fullscreen",
  entity_encoding: "raw",
  keep_styles: false,
    setup: function (editor) {
      editor.on('init', function () {
        editor.getBody().querySelectorAll('*').forEach(function (el) {
          // Remove all attributes from each element
          Array.from(el.attributes).forEach(function (attr) {
            el.removeAttribute(attr.name);
          });
        });
      });
    }
});

/*
toolbar: "accordion addcomment aidialog aishortcuts aligncenter alignjustify alignleft alignnone alignright | 
anchor | blockquote blocks | backcolor | bold | casechange checklist copy cut | fontfamily fontsize forecolor 
h1 h2 h3 h4 h5 h6 hr indent | italic | language | lineheight | newdocument | outdent | paste pastetext | 
print exportpdf exportword importword | redo | remove removeformat | selectall | strikethrough | styles | 
subscript superscript underline | undo | visualaid | a11ycheck advtablerownumbering revisionhistory typopgraphy 
anchor restoredraft casechange charmap checklist code codesample addcomment showcomments ltr rtl editimage fliph 
flipv imageoptions rotateleft rotateright emoticons export footnotes footnotesupdate formatpainter fullscreen 
help image insertdatetime link openlink unlink bullist numlist media mergetags mergetags_list nonbreaking 
pagebreak pageembed permanentpen preview quickimage quicklink quicktable cancel save searchreplace showcomments 
spellcheckdialog spellchecker | table tablecellprops tablecopyrow tablecutrow tabledelete tabledeletecol tabledeleterow 
tableinsertdialog tableinsertcolafter tableinsertcolbefore tableinsertrowafter tableinsertrowbefore tablemergecells 
tablepasterowafter tablepasterowbefore tableprops tablerowprops tablesplitcells tableclass tablecellclass tablecellvalign 
tablecellborderwidth tablecellborderstyle tablecaption tablecellbackgroundcolor tablecellbordercolor tablerowheader tablecolheader | 
tableofcontents tableofcontentsupdate | template typography | insertfile inserttemplate addtemplate | visualblocks visualchars | wordcount",

https://www.tiny.cloud/docs/tinymce/latest/
*/