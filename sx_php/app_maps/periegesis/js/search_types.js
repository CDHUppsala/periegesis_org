let markingErrors = [];
let hints = [];
let certain_Hints = [];
let semicertain_Hints = [];
let uncertain_Hints = [];
let no_Hints = [];

let Save_Hints = false;
let Save_MarkedText = false;


function enrichGreekText(text, items, sectionID = null) {

    text = text.normalize("NFC");
    function reportError(type, detail) {
        markingErrors.push({
            sectionID,
            type,
            detail,
            greekText: text,  // add full Greek text
            items: items      // add full items array
        });
    }

    function buildLink(item) {
        let startTag = `<pl>`;
        let endTag = `</pl>`;
        const quote_Type = item.Type;
        if (quote_Type === 'PERSON') {
            startTag = `<prn>`;
            endTag = `</prn>`;
        } else if (quote_Type === 'EVENT') {
            startTag = `<evt>`;
            endTag = `</evt>`;
        }
        const url = item.Link.match(/href="([^"]+)"/)?.[1] ?? "";
        return `${startTag}<a href="${url}" target="_blank" class="place-link" rel="noopener noreferrer"
                    data-lat="${item.Lat}"
                    data-lon="${item.Lng}"
                    data-name="${item.PlaceName}"
                    data-comments="${item.Comments}"
                >${item.PlaceName}</a>${endTag}`;
    }

    // Already sorted, but resort ascending by StartNum, just in case
    items.sort((a, b) => a.StartNum - b.StartNum);

    let result = text;
    let lastIndex = 0;
    let lastEnd = -1;
    let list_NotFound = '';
    hints = [];
    let links_Length = 0;
    for (const item of items) {

        let { RowID, StartNum, EndNum, PlaceName, Type } = item;

        const unicode_Result = result.normalize("NFC");
        const Unicode_Name = PlaceName.normalize("NFC");

        const slice_Name = text.slice(StartNum, EndNum);
        let new_Start = StartNum;
        let new_End = EndNum;
        if (slice_Name !== Unicode_Name) {
            // Adjust startHint to Greek-aware boundary backward
            let startHint = StartNum;
            while (startHint > 0) {
                const prevChar = text[startHint - 1];
                if (!prevChar || prevChar.match(/[\s,;.:·!?()«»“”\[\]]/)) break;
                startHint--;
            }

            new_Start = text.indexOf(Unicode_Name, startHint);
            if (new_Start === -1) {
                let startMinus = 12;
                if (startHint - startMinus < 0) {
                    startMinus = startHint;
                }
                new_Start = text.indexOf(Unicode_Name, startHint - startMinus);
                if (new_Start === -1) {
                    new_Start = text.indexOf(Unicode_Name, 0);
                    if (new_Start > -1) {
                        new_End = new_Start + Unicode_Name.length;
                        uncertain_Hints.push(`${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => [${new_Start}-${new_End}]`);
                        hints.push(`Uncertain: ${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => [${new_Start}-${new_End}]`);
                    } else {
                        no_Hints.push(`${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => ${new_Start}`);
                        hints.push(`No Hint: ${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => ${new_Start}`);
                    }
                } else {
                    new_End = new_Start + Unicode_Name.length;
                    semicertain_Hints.push(`${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => [${new_Start}-${new_End}]`);
                    hints.push(`Semi-certain: ${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => [${new_Start}-${new_End}]`);
                }
            } else {
                new_End = new_Start + Unicode_Name.length;
                certain_Hints.push(`${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => [${new_Start}-${new_End}]`);
                hints.push(`Certain: ${Type} ${RowID}: ${sectionID} => [${StartNum}-${EndNum}] ${Unicode_Name}  = '${slice_Name}' => [${new_Start}-${new_End}]`);
            }
        }
        /*
                if (StartNum != new_Start) {
                    StartNum = new_Start;
                    EndNum = new_End;
                    lastIndex = StartNum;
                    lastEnd = EndNum;
                }
        */
        let checkNewStartEnd = false;
        let check_LastIndex = true;
        if (StartNum < lastEnd) {
            reportError(
                "overlap",
                `Skipped: ${PlaceName} (StartNum ${StartNum}, lastEnd ${lastEnd})`,
            );
            if (lastEnd < EndNum) lastEnd = EndNum;
            if (list_NotFound !== '') list_NotFound += ' | ';
            list_NotFound += buildLink(item) + ' ';
            // continue;
            check_LastIndex = false;
            checkNewStartEnd = true;
        }
        lastEnd = EndNum;


        let foundIndex = unicode_Result.indexOf(Unicode_Name, lastIndex);
        if (check_LastIndex) {

            if (foundIndex === -1) {
                let errNote = `=> Check for Spelling or Start-End errors for THIS Name`;
                if (PlaceName === slice_Name) {
                    errNote = `=> Check for Start-End errors in PREVIOUS Names`
                }
                reportError(
                    "no-match",
                    `Could not find: "${PlaceName}" => Start ${StartNum} End ${EndNum} Gives: "${slice_Name}" ${errNote}`,
                );
                if (list_NotFound !== '') list_NotFound += ' | ';
                list_NotFound += buildLink(item) + ' ';
                //continue;
                checkNewStartEnd = true;
            }
        }

        if (checkNewStartEnd) {
            let new_slice_Name = text.slice(new_Start, new_End);
            if (new_slice_Name == Unicode_Name) {
                if (lastIndex > (new_Start + links_Length)) {
                    lastIndex = (new_Start + links_Length) - 2;
                }
                foundIndex = unicode_Result.indexOf(Unicode_Name, lastIndex);

            } else {
                continue;
            }
        }

        // Build link
        const link = buildLink(item);
        links_Length += link.length
        result = result.slice(0, foundIndex) + link + result.slice(foundIndex + PlaceName.length);
        lastIndex = foundIndex + link.length;
    }

    if (hints.length) {
        reportError(
            "Hints",
            `${hints}`,
        );
    }
    hints = [];

    if (list_NotFound !== '') { list_NotFound = ' [<small><b>Not Marked</b>: ' + list_NotFound + '</small>]'; }

    return result + list_NotFound;
}

/*
    Initialize
*/

let unique_sections = [];
let loop_section = '';

object_PlacesBySection.forEach(place => {
    if (loop_section !== place.BookID) {
        unique_sections.push(place.BookID);
    }
    loop_section = place.BookID;
});

// Load once the (English and) Greek books in HTML format - and display the first section
fetch('apps/ajax_pausanias_errors.php', {
    method: 'POST',
    body: ''
})
    .then(response => response.text())
    .then(data => {
        document.getElementById('HTML_BooksByParagraph').innerHTML = data;
        //start_CheckSections();
    });


function return_section_html(book_id) {
    return document.querySelector(`#js_Pausanias_Books_el p[id="${book_id}"]`)?.innerHTML || '';
}

function get_PlacesForSection(sectionCode) {
    return object_PlacesBySection.filter(place => place.BookID === sectionCode);
}

function simplifyItem(item) {
    // Extract clean link from the HTML anchor stored in item.Link
    let link = null;
    if (item.Link) {
        const match = item.Link.match(/href="([^"]+)"/);
        link = match ? match[1] : null;
    }

    return {
        BookID: item.BookID, StartNum: item.StartNum, EndNum: item.EndNum,
        PlaceName: item.PlaceName,
        Link: link,
        Lat: item.Lat,
        Lng: item.Lng
    };
}

function start_CheckSections() {
    markingErrors = [];
    hints = [];
    for (const sectionID of unique_sections) {
        const items = get_PlacesForSection(sectionID);
        const greekText = return_section_html(sectionID);

        const prefixEnd = greekText.indexOf("</b>") + 4;
        const prefix = greekText.slice(0, prefixEnd);
        let text = greekText.slice(prefixEnd).trim();

        const sectionHTML = enrichGreekText(text, items, sectionID);
        document.getElementById('HTML_Books')
            .insertAdjacentHTML('beforeend', `<p>${prefix} ${sectionHTML}</p>`);
    }

    // done: markingErrors contains everything
    console.log("Collected errors:", markingErrors.length);
    console.log('certain_Hints', certain_Hints);
    console.log('semicertain_Hints', semicertain_Hints);
    console.log('uncertain_Hints', uncertain_Hints);
    console.log('no_Hints', no_Hints);

    certain_Hints = [];
    no_Hints = [];
    uncertain_Hints = [];
    semicertain_Hints = [];

    // ======= BUILD MARKDOWN REPORT =======
    if (Save_Hints) {

        let md = "# Pausanias Marking Errors Report\n\n";

        // Group errors by section
        const grouped = {};
        for (const err of markingErrors) {
            if (!grouped[err.sectionID]) grouped[err.sectionID] = [];
            grouped[err.sectionID].push(err);
        }

        for (const sectionID of Object.keys(grouped)) {
            const sectionErrors = grouped[sectionID];

            // All errors for this section share greekText & items, so use first entry
            const { greekText, items } = sectionErrors[0];

            md += `## Section ${sectionID}\n\n`;

            // Greek text
            md += `**Text:**\n\n${greekText}\n\n`;

            const simplified = items.map(simplifyItem);

            md += `**Items:**\n\n`;

            for (const item of simplified) {
                md += `• ${JSON.stringify(item)}\n`;
            }

            md += `\n**Errors:**\n\n`;

            let markinErrors = false;
            sectionErrors.forEach(err => {
                if (err.type !== 'Hints') {
                    md += `- **${err.type}** → ${err.detail}\n`;
                    markinErrors = true
                }
            });
            if (markinErrors === false) {
                md += `- No Marking Errors\n`;
            }

            md += `\n**Hints:**\n\n`;

            sectionErrors.forEach(err => {
                if (err.type === 'Hints') {
                    const arrHints = Array.isArray(err.detail)
                        ? err.detail
                        : typeof err.detail === 'string'
                            ? err.detail.split(',')
                            : [];

                    arrHints.forEach(hint => {
                        md += `- ${hint}\n`;
                    });
                }
            });


            md += `\n---\n\n`;
        }

        // ======= SAVE FILE =======
        const blob = new Blob([md], { type: 'text/markdown' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = "pausanias-marking-errors.md";
        a.click();

    }
    markingErrors = []; // reset, free memmory

    if (Save_MarkedText) {
        let html_text = document.getElementById('HTML_Books').innerHTML;

        const html_blob = new Blob([html_text], { type: 'text/html' });
        const html_url = URL.createObjectURL(html_blob);

        const html_a = document.createElement('a');
        html_a.href = html_url;
        html_a.download = "pausanias-marking-greek.html";
        html_a.click();

        html_text = ''
    }
};

document.getElementById("SearchErrors").addEventListener('click', function () {
    Save_Hints = document.getElementById("SaveHints").checked;
    Save_MarkedText = document.getElementById("SaveMarkedText").checked;

    document.getElementById('HTML_BooksByParagraph').style.display = 'none'
    document.getElementById('HTML_Books').innerHTML = ''

    start_CheckSections();
})
