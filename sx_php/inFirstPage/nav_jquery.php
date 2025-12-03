<?php
if ($radioCreateNav) { ?>
    <div id="section_anchor" class="section_anchor">
        <div id="toggle_anchor">»</div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Check if sections with anchors (titles) exist
            if ($(".jq_anchor_mark").length) {
                const $sectionAnchor = $("#section_anchor");
                const $toggleAnchor = $("#toggle_anchor");
                const navOffset = $("#nav_head").outerHeight() + $("#nav_head").offset().top;

                // Create the anchor menu dynamically
                let anchorList = "<ul>";
                $(".jq_anchor_mark").each(function() {
                    const $this = $(this);
                    anchorList += `<li><span class="scroll_to_anchor" data-anchor="${$this.attr("id")}">${$this.data("id")}</span></li>`;
                });
                anchorList += "</ul>";

                $sectionAnchor.append(anchorList);

                const sectionAnchorWidth = Math.round($sectionAnchor.outerWidth()) + 20;
                $sectionAnchor.css("width", `${sectionAnchorWidth}px`);

                // Is set in HTML, but just in case
                if ($sectionAnchor.find("ul").is(":visible")) {
                    $toggleAnchor.text("»");
                }

                // Scroll to section handler
                $(".scroll_to_anchor").on("click", function() {
                    const targetId = $(this).data("anchor");
                    const targetOffset = $(`#${targetId}`).offset().top - (navOffset + 10);

                    $("html, body").animate({
                        scrollTop: targetOffset
                    }, 400).promise().done(() => {
                        $toggleAnchor.click();
                    });
                });

                // Toggle anchor menu visibility
                $toggleAnchor.on("click", function() {
                    const $this = $(this);
                    const isExpanded = $this.text() === "»";

                    if (isExpanded) {
                        $this.text("«");
                        $this.next("ul").slideUp(300, () => {
                            $sectionAnchor.animate({
                                "width": "46px"
                            }, 300);
                        });
                    } else {
                        $this.text("»");
                        $sectionAnchor.animate({
                            "width": `${sectionAnchorWidth}px`
                        }, 300, () => {
                            $this.next("ul").slideDown(300);
                        });
                    }
                });

                // Start with the menu closed
                $toggleAnchor.click();
                $sectionAnchor.css({
                            position: "fixed",
                            top: `${navOffset}px`,
                            visibility: "visible"
                        });
                let isVisible = true;

                // Visible positioning logic
                const $parentSection = $(".section_block");
                const navTop = $parentSection.offset().top - (navOffset * 2.2);
                const navBottom = $parentSection.offset().top + $parentSection.height() - 50;

                $(window).on("scroll", function() {
                    const scrollTop = $(this).scrollTop();

                    if (!isVisible && scrollTop > navTop) {
                        $sectionAnchor.css({
                            position: "fixed",
                            top: `${navOffset}px`,
                            visibility: "visible"
                        });
                        isVisible = true;
                    }
                    if (isVisible && (scrollTop < navTop || scrollTop > navBottom)) {
                        $sectionAnchor.css({
                            visibility: "hidden"
                        });
                        isVisible = false;
                    }
                });
            }

        });
    </script>

<?php
} ?>