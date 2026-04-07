function sx_listMyFunctions(select_id) {
    var i = 1;
    var el = document.getElementById(select_id);
    for (var x in window) {
        if (window.hasOwnProperty(x) && typeof window[x] === 'function') {
            var f = '<option value="' + i + '">' + x + '</option>';
            el.insertAdjacentHTML("afterend", f);
            i++;
        }
    }
}