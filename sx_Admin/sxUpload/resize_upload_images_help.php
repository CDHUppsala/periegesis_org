<section class="maxWidth sxHelpToggle jqHelpToggle">
    <h3>Help for Resizing, Cropping and Uploading Images</h3>
    <ul>
        <li>
            <h4>You can select multiple images to Resize, Crop and Upload to the Server</h4>
            <ul>
                <li>Use the <b>Browse</b> Files or the <b>Drop</b> Files method to select one or more image files.</li>
                <li>Select only files with the extension: .jpg, .jpeg, .webp, .png and .svg. SVG files are converted to PNG. All other file formats can be uploaded from the <b>Upload Files</b>.</li>
                <li>Select separately Horizontal and Vertical images, as resizing and cropping functions are applied in differnt ways.</li>
                <li>You cannot add additional files: every new selection removes the previous one.</li>
            </ul>
        </li>
        <li>
            <h4>Resize images by setting Max Width <span class="auto-style1">or</span> Max Height</h4>
            <ul>
                <li>You use Max Width <span class="auto-style1">or</span> Max Height when <strong>Resizing</strong> big images and when <strong>Cropping</strong> images. Which of these two values will be used by the program depends on the image's <strong>Height/Width Ratio</strong> - or on the orientation of the image (<strong>Horizontal</strong> or <strong>Vertical</strong>):
                    <ul>
                        <li>If <strong>Height &lt; Width</strong> (Horizontal images), then <strong>Max Width</strong> will be used. - and the Height will be set automatically to preserve the initial W/H ratio of the image</li>
                        <li>If <strong>Height &gt; Width</strong> (Vertical images), then <strong>Max Height</strong> will be used - and the Width will be set automatically to preserve the initial W/H ratio of the image.</li>
                    </ul>
                </li>
                <li>If the original Width or Height is smaller than the selected, the original image (width and height) will be kept.</li>
                <li>Resize images according to the following rules
                    <ul>
                        <li>Use Horizontal, <b>High Width</b> (and low Height) for images that will appear on the <b>top</b> of texts or in <b>sliders</b>: 800, 1000, 1200 pixels.
                            <ul>
                                <li>Eventually, use higher widths (1400, 1600 or 1800) only for <b>Galleries</b>.</li>
                                <li><strong>Widescreen</strong> images (with H/W ratio &lt;= 0.5625) are best for this use. Preferably, use the ratio 0.5 (e.g. height 500px width 1000px)</li>
                            </ul>
                        </li>
                        <li>Use Vertical, <b>Low Width</b> (and high Height) for images that will appear on the <b>left</b> or on the <b>right</b> of texts, eventually also in <b>advertises</b>: 300, 400, 600 pixels.</li>
                        <li>Please notice that even if images take a smaller place in the site, they must be big enough if the visitor can open them separately in a light box or in galleries.</li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <h4>Reduce the Quality of images</h4>
            <ul>
                <li>You can radically reduce the <b>Loading</b> Size (in Bytes) of Big Images by reducing their <b>Quality</b>.
                    You reduce the number of <b>colors</b> or the variations of similar colors which are not visible for humans in the sizes used in Web Sites.</li>
                <li>Heigh resolution images should not be greater than 200 000 bytes (200kb).</li>
            </ul>
        </li>
        <li>
            <h4>Crop images to give them the same Height/Width Aspect Ratio.</h4>
            <ul>
                <li>Images with the same Height/Width Aspect Ratio take the same space in web pages, for example, 400/800 pixels and 500/1000 pixels.</li>
                <li>Crop images according to the following rules:
                    <ul>
                        <li>Use <b>Low Aspect Ratio</b> (Widescreen) for images that will appear on the top of texts or in sliders: 0.4, 0.5, 0.565 (9/16) or 0.6).</li>
                        <li>Use <b>High Aspect Ratio</b> for images that will appear on the left or the right of texts or in advertises: 0.75, 1 or 1.5.</li>
                    </ul>
                </li>
                <li>You can also define the <b>Position</b> of Cropping within the image, depending on the <b>Aspect Ratio</b> of Height/Width you select, according to:
                    <ul>
                        <li>The <b>Horizontal Axis</b>: Left (L), Center (C) or Right (R).</li>
                        <li>The <b>Vertical Axis</b>: Top (T), Middle (M) or Bottom (B).</li>
                    </ul>
                </li>

                <li>Different <b>Image Shapes</b> are Cropped differently:
                    <ul>
                        <li><b>Horizontal</b> or <b>Widescreen</b> images can be <b>simultaneously</b> cropped both Horizontally (Left, Center, Right) and Vertically (Top, Middle, Bottom).</li>
                        <li><b>Verical</b> or <b>Portrate</b> images can be cropped:
                            <ul>
                                <li><b>Either Vertically</b> (Top, Middle, Bottom), when the <b>Total Width</b> of the image is included in the crop area (in that case check the <b>Desired Box</b> at the Vertical Center: CT, CM or CB).</li>
                                <li><b>Or Horizontall</b> (Left, Center, Right), when the <b>Total Hight</b> is included in the crop area (in that case check <b>Eny Box</b> at the Vertical Left, Center or Right).</li>
                            </ul>
                        </li>
                        <li><b>Do Not Crop</b> simultaneously multiple images that have radically different <b>shapes</b>: Crop Widescreen and Portrate images separately.</li>
                    </ul>
                </li>

                <li>Please notice that the final size of the cropped image also depends on the Max Height value - see the last point.</li>
            </ul>
        </li>

        <li>
            <h4>Increase or Decrease Cropping manually.</h4>
            <ul>
                <li>The <b>Increase Top</b> or <b>Decrease Bottom</b> is used for <b>Vertical</b> cropping and is in effect with all crop positions except for <b>Middle Cropping</b> (LM, CM, RM).
                    If Middle Cropping does not give you the desired image, you can try with the following alternatives (also in combination with <b>Horizontal</b> cropping - see bellow):
                    <ul>
                        <li>Select a <b>Top Cropping</b> (LT, CT, RT) and <b>Increase</b> the number of pixels from which Top cropping will start (from the defaul 0 + the number you write).</li>
                        <li>Select a <b>Bottom Cropping</b> (LB, CB, RB) and <b>Decrease</b> the number of pixeles from which Bottom cropping will start (from the auto-calculated value - the number you write).</li>
                        <li>In fact, you can get the same effect from either alternative - so you can use only one of them.</li>
                        <li>Please check if the number you write gets you out of the Canvas (<b>black</b> areas on the Top or Bottom).</li>
                    </ul>
                </li>
                <li>The <b>Increase Left</b> or <b>Decrease Right</b> is used for <b>Horizontal</b> cropping and is in effect with all crop positions except for <b>Center Cropping</b> (CT, CM, CB).
                    If Center Cropping does not give you the desired image, you can try with the following alternatives (also in combination with <b>Vertical</b> cropping - see above):
                    <ul>
                        <li>Select a <b>Left Cropping</b> (LT, LM, LB) and <b>Increase</b> the number of pixels from which Left cropping will start (from the defaul 0 + the number you write).</li>
                        <li>Select a <b>Right Cropping</b> (RT, RM, RB) and <b>Decrease</b> the number of pixeles from which Right cropping will start (from the auto-calculated value - the number you write).</li>
                        <li>In fact, you can get the same effect from either alternative - so you can use only one of them.</li>
                        <li>Please check if the number you write gets you out of the Canvas (<b>black</b> areas on the Left or Right).</li>
                    </ul>
                </li>
                <li>Please notice that the final size of the cropped image also depends on the Max Height value - see next point.</li>
            </ul>
        </li>

        <li>
            <h4>The use of Max Height and Max Width when Cropping images.</h4>
            <ul>
                <li>Images should not be higher than the available window screen: visitors <b>should not scroll</b> to see an image.
                    Therefore, the default Max Height of images is set to 600 pixels, although you can change it.</li>
                <li>If the height of an image is higher than 600 pixels (or any selected default value), when you <b>Crop</b> the image, the Height will be set to 600 pixels
                    and the Width will be automatically calculated to preserve the predefined aspect ratio.
                    So, the final width of the image might differ from (be less than) the defined one.
                </li>
                <li>For example, if you set the Max Height to 600:<ul>
                        <li>If you crop a big image with Aspect Ration 1.5, it will always give you an image with 400 pixels width and 600 pixels (Max) height,
                            even if you set the Max Width to 800, 1000 or 1200 pixels.</li>
                        <li>However, a Max Width of 300 pixels will give you an image with 300 pixels width and 450 pixels height.</li>
                        <li>In the same way, the biggest rectangular image you can get (Aspect Ration = 1) is 600X600 pixels.</li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</section>