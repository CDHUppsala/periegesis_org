<?php
if ($radioCreateNav) { ?>
    <div id="section_anchor" class="section_anchor">
        <div id="toggle_anchor">«</div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            if ($(".jq_anchor_mark").length) {
                const $sectionAnchor = $("#section_anchor");
                const $toggleAnchor = $("#toggle_anchor");
                const navOffset = $("#nav").outerHeight() + $("#nav").offset().top;

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

                if ($sectionAnchor.find("ul").is(":visible")) {
                    $toggleAnchor.text("«");
                }

                // Scroll to section handler
                $(".scroll_to_anchor").on("click", function() {
                    const targetId = $(this).data("anchor");
                    const targetOffset = $(`#${targetId}`).offset().top - (navOffset + 10);

                    $("html, body").animate({ scrollTop: targetOffset }, 400).promise().done(() => {
                        $toggleAnchor.click();
                    });
                });

                // Toggle anchor menu visibility
                $toggleAnchor.on("click", function() {
                    const $this = $(this);
                    const isCollapsed = $this.text() === "«";

                    if (isCollapsed) {
                        $this.text("»");
                        $this.next("ul").slideUp(300, () => {
                            $sectionAnchor.animate({ "width": "46px" }, 300);
                        });
                    } else {
                        $this.text("«");
                        $sectionAnchor.animate({ "width": `${sectionAnchorWidth}px` }, 300, () => {
                            $this.next("ul").slideDown(300);
                        });
                    }
                });

                // Start with the menu closed
                $toggleAnchor.click();

                // Fixed positioning logic
                let isFixed = false;
                const $parentSection = $(".section_block");
                const navTop = $parentSection.offset().top - (navOffset * 2.2);
                const navBottom = $parentSection.offset().top + $parentSection.height() - 50;

                $(window).on("scroll", function() {
                    const scrollTop = $(this).scrollTop();

                    if (!isFixed && scrollTop > navTop) {
                        $sectionAnchor.css({
                            position: "fixed",
                            top: `${navOffset}px`,
                            display: "block",
                            visibility: "visible"
                        });
                        isFixed = true;
                    }
                    if (isFixed && (scrollTop < navTop || scrollTop > navBottom)) {
                        $sectionAnchor.css({
                            position: "absolute",
                            top: "1px",
                            display: "none"
                        });
                        isFixed = false;
                    }
                });
            }
        });
    </script>

<?php
} ?>