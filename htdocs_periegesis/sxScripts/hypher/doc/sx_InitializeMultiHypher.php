<script src="../plugins/hypher/jquery.hypher.js"></script>
<script src="../plugins/hypher/browser/la.js"></script>
<script src="../plugins/hypher/browser/en-us.js"></script>
<script src="../plugins/hypher/browser/sv.js"></script>
<script src="../plugins/hypher/browser/el-monoton.js"></script>
<script>
/* 
    the .text * is important to include all html elements, p, table, etc
    or just juse .text p
*/
jQuery(function ($) {
    sx_loadHyphers($)
});

var sx_loadHyphers = function($) {
    $('.text[lang=la] p').hyphenate('la');
    $('.text[lang=en] p').hyphenate('en-us');
    $('.text[lang=sv] p').hyphenate('sv');
    $('.text[lang=el] p').hyphenate('el');
}
</script>