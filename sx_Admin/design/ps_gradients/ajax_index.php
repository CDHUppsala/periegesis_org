<div id="jq_root_color_gradients" class="root_color_gradients">
    <style>
        <?php
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/classes/linear.css");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/classes/circle.css");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/classes/ellipse.css");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/css/gradients.css");
        ?>
    </style>

    <div id="jq_root_variables">
        <div class="root_header" id="jq_root_header">
            <div class="root_flex_between roo_flex_nowrap root_flex_stretch">
                <h1>CSS Root Color Variables and Gradients</h1>
                <buttom id="jq_modal_open_button" title="OPEN/CLOSE fullscreen Modal Window"><img alt="Full Screen" src="images/sx_svg/sx_full_screen.svg" /></buttom>
            </div>
            <div id="jq_color_inputs_wrapper">
                <div id="jq_color_inputs">
                    <div class="root_inputs root_flex_between root_flex_end">
                        <div class="root_absolute">
                            <label>Uncheck Radios:<input type="radio" name="RootColors" value="Unchecked" checked /></label>
                        </div>
                        <div class="root_flex_column">
                            <div title="Change all occurrences of tints in gradients (white colors from 10% to 100% opacity) with a color">
                                Click a Color to replace Background and Tints [?]:</div>
                            <div class="root_flex_between">
                                <label>
                                    <span class="bg_label" title="Basic color as default Background Color for all gradients. Change it to any color, even white.">
                                        <input type="radio" name="GPColor" value="bg" checked></span> Background
                                </label>
                                <label>
                                    <span class="tint-0">
                                        <input type="radio" name="GPColor" value="tint-0"></span> tint-0
                                </label>
                                <label>
                                    <span class="tint-05">
                                        <input type="radio" name="GPColor" value="tint-05"></span> tint-05
                                </label>
                                <label>
                                    <span class="tint-20">
                                        <input type="radio" name="GPColor" value="tint-20"></span>tint-20
                                </label>
                            </div>

                            <div class="root_flex_between">
                                <label>
                                    <span class="tint-30">
                                        <input type="radio" name="GPColor" value="tint-30"></span> tint-30
                                </label>
                                <label>
                                    <span class="tint-50">
                                        <input type="radio" name="GPColor" value="tint-50"></span> tint-50
                                </label>
                                <label>
                                    <span class="tint-70">
                                        <input type="radio" name="GPColor" value="tint-70"></span> tint-70
                                </label>
                                <label>
                                    <span class="tint-80">
                                        <input type="radio" name="GPColor" value="tint-80"></span> tint-80
                                </label>
                            </div>
                        </div>
                        <div>
                            <div class="root_flex">
                                <label>
                                    <select name="Opacity" id="opacity" title="Set the opacity for colors replacing tints. Has NO effect for tint/shade/tone colors.">
                                        <option value="1">Color opacity</option>
                                        <option value="0.05">5%</option>
                                        <option value="0.1">10%</option>
                                        <option value="0.15">15%</option>
                                        <option value="0.2">20%</option>
                                        <option value="0.25">25%</option>
                                        <option value="0.3">30%</option>
                                        <option value="0.35">35%</option>
                                        <option value="0.4">40%</option>
                                        <option value="0.45">45%</option>
                                        <option value="0.5">50%</option>
                                        <option value="0.55">55%</option>
                                        <option value="0.6">60%</option>
                                        <option value="0.65">65%</option>
                                        <option value="0.7">70%</option>
                                        <option value="0.75">75%</option>
                                        <option value="0.8">80%</option>
                                        <option value="0.85">85%</option>
                                        <option value="0.9">90%</option>
                                        <option value="0.95">95%</option>
                                        <option value="1">100%</option>
                                    </select>
                                </label>
                                <label><input type="button" id="ClearRootColors" name="ClearRootColors" value="Clear Colors" title="Clears all root colors defined to replace tints"></label>
                            </div>
                        </div>
                        <div>
                            <input type="button" class="jg_scroll_top" name="ScrollTop" value="Top" title="Scroll to Top">
                            <input type="button" class="jg_hide_all" name="HideAll" value="Hide All" title="Hide all open Colors and Gradients">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="jq_InsertRootVariables">
            <div class="tabs">
                <a data-id="jq_InsertRootColor" class="selected" href="javascript:void(0)">View Colors</a>
                <a data-id="jq_InsertRootGradients" href="javascript:void(0)">View Gradients</a>
            </div>
            <div id="jq_InsertRootColor" class="root_insert_colors"></div>
            <div id="jq_InsertRootGradients" class="root_insert_gradients">
                <div id="jq_gradient_inputs_wrapper">
                    <div id="jq_gradient_inputs">
                        <div class="root_inputs root_flex_between root_flex_end">
                            <div class="root_absolute">
                                <div class="root_flex_end">
                                    <label class="change_gradients_ratio" title="Change the H/W aspect ratio of gradients">
                                        <button data-id="2">1/2</button>
                                        <button data-id="1">1/1</button>
                                    </label>
                                    <label class="change_gradients_width" title="Change the width av gradients">
                                        <button data-id="100%">100%</button>
                                        <button data-id="49%">50%</button>
                                        <button data-id="24.5%">25%</button>
                                    </label>
                                    <label title="Uncheck all radios">Uncheck:<input type="radio" name="RootColors" value="Unchecked" /></label>
                                </div>
                            </div>
                            <div>
                                <div class="gradient_inputs_grid">
                                    <label><input class="jq_show_types" type="radio" name="GradienType" data-id="jq_all" value="all" checked>All</label>
                                    <label><input class="jq_show_types" type="radio" name="GradienType" data-id="jq_ellips" value="ellipse">Ellipses</label>
                                    <label><input class="jq_show_types" type="radio" name="GradienType" data-id="jq_circle" value="circle">Circles</label>
                                    <label><input class="jq_show_types" type="radio" name="GradienType" data-id="jq_linear" value="linear">Linears</label>
                                    <label><input class="jq_show_colors" type="radio" name="GradienColors" data-id="jq_color_all" value="all" checked>All</label>
                                    <label><input class="jq_show_colors" type="radio" name="GradienColors" data-id="jq_color_2" value="C2">2 Colors</label>
                                    <label><input class="jq_show_colors" type="radio" name="GradienColors" data-id="jq_color_3" value="C3">3 Colors</label>
                                    <label><input class="jq_show_colors" type="radio" name="GradienColors" data-id="jq_color_4" value="C4">4 Colors</label>
                                </div>
                                <label>
                                    <select class="jq_show_stops">
                                        <option value="0" selected>All Stops</option>
                                        <option value="S1">1 Stop</option>
                                        <option value="S2">2 Stops</option>
                                        <option value="S3">3 Stops</option>
                                        <option value="S4">4 Stops</option>
                                        <option value="S5">5 Stops</option>
                                        <option value="S6">6 Stops</option>
                                        <option value="S8">8 Stops</option>
                                        <option value="S10">10 Stops</option>
                                        <option value="X">X Stops</option>
                                    </select>
                                </label>
                                <label>
                                    <select class="jq_show_patterns">
                                        <option value="0" selected>All Patterns</option>
                                        <option value="P1">1 Pattern</option>
                                        <option value="P2">2 Patterns</option>
                                        <option value="P3">3 Patterns</option>
                                        <option value="P4">4 Patterns</option>
                                        <option value="P5">5 Patterns</option>
                                        <option value="P6">6 Patterns</option>
                                        <option value="T">Transparent</option>
                                    </select>
                                </label>
                            </div>
                            <div>
                                <div class="root_flex_end roo_flex_nowrap">
                                    <label>
                                        Variable:<br>
                                        <select name="TintVariable" id="TintVariable" title="Replace Opacities of White by replacing this Tint Variable with another Tint Variable">
                                            <option value="-1" selected>None</option>
                                            <option value="0">tint-0</option>
                                            <option value="05">tint-05</option>
                                            <option value="10" disabled>tint-10</option>
                                            <option value="20">tint-20</option>
                                            <option value="30">tint-30</option>
                                            <option value="40" disabled>tint-40</option>
                                            <option value="50">tint-50</option>
                                            <option value="60" disabled>tint-60</option>
                                            <option value="70">tint-70</option>
                                            <option value="80">tint-80</option>
                                            <option value="90" disabled>tint-90</option>
                                            <option value="100" disabled>tint-100</option>
                                        </select>
                                    </label>
                                    <label>
                                        Replace with:<br>
                                        <select name="TintReplace" id="TintReplace">
                                            <option value="-1" selected>None</option>
                                            <option value="0">tint-0</option>
                                            <option value="05">tint-05</option>
                                            <option value="10">tint-10</option>
                                            <option value="20">tint-20</option>
                                            <option value="30">tint-30</option>
                                            <option value="40">tint-40</option>
                                            <option value="50">tint-50</option>
                                            <option value="60">tint-60</option>
                                            <option value="70">tint-70</option>
                                            <option value="80">tint-80</option>
                                            <option value="90">tint-90</option>
                                            <option value="100">tint-100</option>
                                        </select>
                                    </label>
                                    <label>
                                        <input type="button" id="ReplaceVariables" name="ReplaceVariables" value="Replace" title="Replace the default opacity of USED tint variables with the opacity of the selected tint variable">
                                        <input type="button" id="ResettVariables" name="ResettVariables" value="Reset" title="Resett default opacity of all tint variable">
                                    </label>
                                </div>
                            </div>
                            <div>
                                <input type="button" class="jg_scroll_top" name="ScrollTop" value="Top" title="Scroll to Top">
                                <input type="button" class="jg_hide_all" name="HideAll" value="Hide All" title="Hide all open Colors and Gradients">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="jq_InsertGradients"></div>

            </div>
        </div>

        <p>You must place all your :root css variables in a separate stylesheet and link to it in ADD and EDIT pages, as the <b>First</b> CSS Link.</p>
    </div>


    <script>
        <?php
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/js/jq_ajax_functions.js");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/js/global_functions.js");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/js/get_colors.js");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/js/get_gradients.js");
        require realpath(dirname($_SERVER['DOCUMENT_ROOT']) . "/sx_Admin/design/gradients/js/modal_window.js");
        ?>
    </script>
</div>