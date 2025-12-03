; function load_modal_html(bookID) {
    const wiki_pedia = 'https://en.wikipedia.org/wiki/';
    const wiki_data = 'https://www.wikidata.org/wiki/';

    const hideen_html = document.getElementById('js_Load_Hidden_HTML');
    const modal_data = document.getElementById('js_Modal_Data');

    if (hideen_html.innerHTML === '') {
        // Load once the English and Greek books in HTML format - and display the first section
        fetch('apps/ajax_pausanias.php', {
            method: 'POST',
            body: ''
        })
            .then(response => response.text())
            .then(data => {
                hideen_html.innerHTML = data;
                if (bookID) {
                    update_HTML_BySection(bookID);
                }
            });
    } else {
        if (bookID) {
            update_HTML_BySection(bookID);
        }
    }

    update_HTML_BySection = function (book_id) {
        const section_html = return_section_html(book_id);
        modal_data.innerHTML = section_html;
        set_link_titles();
        activate_navigation();
    };

    function return_section_html(book_id) {
        const section_en = document.querySelector(`#js_Pausanias_Books_en p[id="${book_id}"]`)?.innerHTML || '';
        section_el = document.querySelector(`#js_Pausanias_Books_el p[id="${book_id}"]`)?.innerHTML.trim() || '';
        const prev = document.querySelector(`#js_Pausanias_Books_en p[id="${book_id}"]`)?.previousElementSibling;
        const next = document.querySelector(`#js_Pausanias_Books_en p[id="${book_id}"]`)?.nextElementSibling;
        const prev_book_id = prev?.id || book_id;
        const next_book_id = next?.id || book_id;

        const arr_book = book_id.split('.');
        const book_title = `<h4>Book: ${arr_book[0]}, Chapter: ${arr_book[1]}, Section: ${arr_book[2]}</h4>`;
        const title_en = document.querySelector('#js_Pausanias_Books_en h2')?.textContent || '';
        const title_el = document.querySelector('#js_Pausanias_Books_el h2')?.textContent || '';

        return `
      <div>
        <h3>${title_en} - ${title_el}</h3>
        <div class="section_nav_wrapper">
          ${book_title}
          <div style="color:blue; cursor:pointer" id="js_section_nav">
            <span class="js_previous" data-id="${prev_book_id}">Previous</span> |
            <span class="js_next" data-id="${next_book_id}">Next</span>
          </div>
        </div>
        <div class="section_html_content">
          <p id="SectionDefaultFont">${section_en}</p>
          <p id="SectionPolytonicFont">${section_el}</p>
        </div>
      </div>
    `;

    };

    function set_link_titles() {
        document.querySelectorAll('#SectionDefaultFont pl, #SectionDefaultFont prn').forEach(el => {

            // If element <a> exists -> do NOTHING at all
            if (el.querySelector('a')) {
                return;
            }

            // add Title to Places/Persons
            const currTitle = el.getAttribute('title');
            let site = 'WIKIDATA';
            let strID = el.id;
            if (el.dataset.wiki !== undefined) {
                site = 'WIKIPEDIA';
                strID = el.dataset.wiki;
            }
            const label = el.tagName === 'PL' ? 'PLACE' : 'PERSON';
            el.setAttribute('title', `${label}: ${currTitle} (Open in ${site}, ID: ${strID})`);

            // Add link to Places/Persons
            el.addEventListener('click', () => {
                const url = el.dataset.wiki ? wiki_pedia : wiki_data;
                const sufix = el.dataset.wiki || el.id;
                const win = window.open(url + sufix, '_blank');
                if (win) {
                    win.focus();
                } else {
                    alert('Please allow popups for this website');
                }
            });
        });
    }

    function activate_navigation() {
        document.querySelectorAll('#js_section_nav span').forEach(span => {
            span.addEventListener('click', () => {
                const book_id = span.dataset.id;
                const section_html = return_section_html(book_id);
                modal_data.innerHTML = section_html;
                set_link_titles();
                activate_navigation();
            });
        });
    }

    const modal_window = document.getElementById('js_Modal_Window');
    modal_window.style.display = 'block';
    document.getElementById('js_Modal_Close').addEventListener('click', () => {
        modal_data.innerHTML = '';
        modal_window.style.display = 'none';
    });

}