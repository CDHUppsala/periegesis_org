// External sources for IDs in texts
const wiki_pedia = 'https://en.wikipedia.org/wiki/';
const wiki_data = 'https://www.wikidata.org/wiki/';

// Globals
let update_HTML_BySection;
let section_el;

// Wait untill the documemt loads
document.addEventListener('DOMContentLoaded', () => {
    // Get section (paragraph) text for requested section ID
    // The book IDs for EN and EL are defined in the loaded HTML books
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

    }

    // Load once the English and Greek books in HTML format - and display the first section
    fetch('apps/ajax_pausanias.php', {
        method: 'POST',
        body: ''
    })
        .then(response => response.text())
        .then(data => {
            document.getElementById('HTML_BooksByParagraph').innerHTML = data;

            // Check for bookID in query string, update both Map and HTML
            const params = new URLSearchParams(window.location.search);
            const bookID = params.get('b');
            if (bookID) {
                update_Map_BySection(bookID);
                update_HTML_BySection(bookID);
            } else {
                // Display initially the first section, which is already marked in the map
                update_HTML_BySection(current_book_id);
            }
        });

    function activate_navigation() {
        document.querySelectorAll('#js_section_nav span').forEach(span => {
            span.addEventListener('click', () => {
                const book_id = span.dataset.id;
                const section_html = return_section_html(book_id);
                document.getElementById('HTML_SectionWraper').innerHTML = section_html;
                set_titles();
                activate_navigation();
                update_Map_BySection(book_id);
                add_linksToGreekPlaces();
            });
        });
    }

    function set_titles() {
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

    update_HTML_BySection = function (book_id) {
        const section_html = return_section_html(book_id);
        document.getElementById('HTML_SectionWraper').innerHTML = section_html;
        set_titles();
        activate_navigation();
        add_linksToGreekPlaces();
    };

    document.addEventListener('click', (e) => {
        if (e.target.matches('span.toggle_up, span.toggle_down')) {
            const wrapper = document.querySelector('#HTML_SectionWraper > div');
            if (wrapper) {
                //wrapper.style.display = wrapper.style.display === 'none' ? 'block' : 'none';
                wrapper.classList.toggle('hidden');
                e.target.classList.toggle('toggle_up');
                e.target.classList.toggle('toggle_down');
            }
        }
        if (e.target.matches('span.toggle_info, .close_info')) {
            const wrapper = document.querySelector('#HidenInformation > div');
            if (wrapper) {
                wrapper.classList.toggle('visible');
            }
        };
    });

});

function mark_GreekText(text, items) {
    // console.log('text: ', text)
    // console.log('items: ', items)

    function buildLink(item) {
        const url = item.Link.match(/href="([^"]+)"/)?.[1] ?? "";
        return `<a href="${url}" target="_blank" class="place-link" rel="noopener noreferrer"
                    data-lat="${item.Lat}"
                    data-lon="${item.Lng}"
                    data-name="${item.PlaceName}"
                    data-comments="${item.Comments}"
                >${item.PlaceName}</a>`;
    }

    // Already sorted, but resort ascending by StartNum, just in case
    items.sort((a, b) => a.StartNum - b.StartNum);

    let result = text;
    let lastIndex = 0;
    let lastEnd = -1;
    let list_NotFound = '';

    for (const item of items) {

        const { StartNum, EndNum, PlaceName } = item;
        // Encode Greek words in the Unicode form NFC, that not changes the length of characters
        const unicode_Result = result.normalize("NFC");
        const unicode_Name = PlaceName.normalize("NFC");


        if (StartNum < lastEnd) {
            if (lastEnd < EndNum) {
                lastEnd = EndNum;
            };

            // console.log('Skipped__: ', PlaceName)
            if (list_NotFound !== '') list_NotFound += ' | ';
            list_NotFound += buildLink(item) + ' ';
            continue;
        }
        lastEnd = EndNum;

        // Extract the name from the exact substring from original text
        const slice_Name = text.slice(StartNum, EndNum);

        // Find the next occurrence of the exact PlaceName - use unicode name
        let foundIndex = unicode_Result.indexOf(unicode_Name, lastIndex);

        // Correct errors in annotation order 
        if (foundIndex === -1 && slice_Name === unicode_Name) {
            lastIndex = StartNum;
            foundIndex = result.indexOf(slice_Name, lastIndex);
        }

        if (foundIndex === -1) {
            // console.warn("Could not find substring/PlaceName:", slice_Name + ' / ' + PlaceName);
            if (list_NotFound !== '') list_NotFound += ' | ';
            list_NotFound += buildLink(item) + ' ';
            continue;
        }

        // Build link
        const link = buildLink(item);

        // Replace in result
        result = result.slice(0, foundIndex) + link + result.slice(foundIndex + PlaceName.length);

        // Update lastIndex to continue after inserted link
        lastIndex = foundIndex + link.length;
    }
    if (list_NotFound !== '') { list_NotFound = '<span class="not_marked"><b>Not Marked</b>: ' + list_NotFound + '</span>'; }
    return result + list_NotFound;
}

var add_linksToGreekPlaces = function () {

    const prefixEnd = section_el.indexOf("</b>") + 4;
    const prefix = section_el.slice(0, prefixEnd);
    let text = section_el.slice(prefixEnd).trim();

    const annotatedGreek = mark_GreekText(text, places_Array);
    document.getElementById('SectionPolytonicFont').innerHTML = prefix + " " + annotatedGreek;
    attach_PlaceHoverEvents()

};

var attach_PlaceHoverEvents = function () {
    document.querySelectorAll('.place-link').forEach(link => {
        link.addEventListener('mouseenter', () => {
            const lat = parseFloat(link.dataset.lat);
            const lon = parseFloat(link.dataset.lon);
            const name = link.dataset.name;
            delay_Highlight(lat, lon, name, link);
        });
    });
}


// To stop the execution of multiple hoverings
let hover_Timeout = null;

function delay_Highlight(lat, lon, name, element) {
    if (hover_Timeout) clearTimeout(hover_Timeout);

    hover_Timeout = setTimeout(() => {
        // Check if the element is still being hovered
        if (element.matches(':hover')) {
            highlight_OnMap(lat, lon, name);
        }
    }, 300);
}


// Call the hovering function after 300ms
function highlight_OnMap(lat, lon, name) {
    const zoomLevel = 13; // Adjust to your preferred zoom

    // Step 1: Project the lat/lon to pixel coordinates
    const point = sectionMap.project([lat, lon], zoomLevel).subtract([0, 100]); // shift up 100px

    // Step 2: Unproject back to lat/lon
    const target = sectionMap.unproject(point, zoomLevel);

    // Step 3: Set view with zoom and animation
    sectionMap.flyTo(target, zoomLevel, {
        animate: true,
        duration: 1.2
    });

    // Optional: show a colored circle at the location
    const pulse = L.circle([lat, lon], {
        radius: 300,
        color: '#ff6600',
        fillColor: '#ffcc00',
        fillOpacity: 0.75
    }).addTo(sectionMap);

    setTimeout(() => {
        sectionMap.removeLayer(pulse);
    }, 1500);

    // Optional: show a popup at the location
    setTimeout(() => {
        L.popup()
            .setLatLng([lat, lon])
            .setContent(`<strong>${name}</strong>`)
            .openOn(sectionMap);
    }, 1600);

}
