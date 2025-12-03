<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Studiox X Content Management System - Main Page Help Information</title>
	<link rel="stylesheet" href="css/sxCMS.css?v=1">
	<script type="text/javascript" src="../../sxScripts/jq/jquery.min.js"></script>
	<script>
		var $sx_help = jQuery.noConflict();
		$sx_help(window).on("load", function() {
			$sx_help("#nav a").click(function() {
				var $this = $sx_help(this);
				var $thisID = $this.attr("data-id");
				if ($this.attr("class") != "selected") {
					$this.addClass("selected")
						.siblings().removeClass("selected");
					$sx_help("#layer" + $thisID).show(500)
						.siblings(".js_help_main").hide(500);
				}
			});
			if (location.search != "") {
				var tabNumber = (location.search).replace(/\?intTab=/, "");
				tabNumber = (isNaN(tabNumber)) ? 0 : tabNumber
				$sx_help("#nav a[data-id=" + tabNumber + "]").click();
			}
		});
	</script>
</head>

<body class="body">

	<header id="header">
		<h2>Public Sphere CMS<br>Help Information for Table Groups</h2>
	</header>
	<nav id="nav">
		<h4>Help by Group</h4>
		<a data-id="0" class="selected" href="javascript:void(0)">Setup</a>
		<a data-id="1" href="javascript:void(0)">Articles</a>
		<a data-id="2" href="javascript:void(0)">About</a>
		<a data-id="3" href="javascript:void(0)">Promotion</a>
		<a data-id="4" href="javascript:void(0)">Reports</a>
		<a data-id="5" href="javascript:void(0)">Communication</a>
		<a data-id="6" href="javascript:void(0)">Advertises</a>
		<a data-id="7" href="javascript:void(0)">Library</a>
		<a data-id="8" href="javascript:void(0)">Gallery</a>
		<a data-id="9" href="javascript:void(0)">Folder Gallery</a>
		<a data-id="10" href="javascript:void(0)">Multimedia</a>
		<a data-id="11" href="javascript:void(0)">PDF-Archives</a>
		<a data-id="12" href="javascript:void(0)">Links</a>
		<a data-id="13" href="javascript:void(0)">Initial Settings</a>
		<a data-id="14" href="javascript:void(0)">Tools</a>
	</nav>
	<div id="layer0" class="maxWidth js_help_main" style="display: block">
		<div class="text">
			<h1><strong>Application: Public Sphere</strong></h1>
			<h2>General Setup Information</h2>
			<h3><strong>1. Navigation among Tables and Table Groups</strong><strong>:</strong></h3>
			<ol>
				<li>In the <strong>left column</strong> you have a list of all Database <strong>Tables</strong>, ordered into <strong>Table Groups</strong>.</li>
				<li>Click on the <strong>Name</strong> or <strong>Image</strong> (+) of a Group to display all the Tables it contains.</li>
				<ul>
					<li>If you click on the <strong>Name</strong>, help information about the group and its tables will appear in this column.</li>
					<li>If you click on the <strong>Image</strong> (+), the tables will appear without changing the content of this column.</li>
				</ul>
			</ol>
			<h3>2. Basic<strong> Setup</strong></h3>
			<p>Click on the Table Group <strong>Initial Settings</strong>:</p>
			<ol>
				<li>Open the Table <strong>Admin Login</strong> and add the administrators of the Site and their Administrative Right Levels.
					<ul>
						<li>Level 2 limits the access to some tables and the possibility to delete and update records for som other tables.</li>
						<li>Use the tab <strong>Hash Password</strong> to hash new passwords.</li>
					</ul>
				</li>
				<li>Open the Table <strong>Languages</strong> and define the languages of the Site. You can define Max 3 languages.</li>
				<li>Open the Table <strong>Site Setup</strong> and for every language enter the information about your site.</li>
				<li>Open then each one of the three <strong>Site Configuration</strong> Tables in order to configure the <strong>Basic</strong> functions of the Site, the <strong>Text</strong> functions of the Site and the <strong>Additional Applications</strong> that you like to use in your Site, among the available ones.
					<ul>
						<li>Some of the Additional Applications might not be available in the current design of the site.</li>
					</ul>
				</li>
			</ol>
			<h3>3. Text<strong> management system (TMS)</strong></h3>
			<p>The program offers four multilingual <strong>Text Management System (TMS)</strong>. You can combine and use two or three of them simultaneously, for different purposes, or you can select only one of them.</p>
			<ol>
				<li>A <strong>Simple TMS</strong>, called <strong>About</strong>, with a <strong>definite</strong> (although <strong>unlimited</strong>) number of texts which are <strong>not changing frequently</strong> and which appear as links on a menu list.
					<ul>
						<li>This TMS can be used for the presentation of the site or to provide information of any kind that is not changing frequently.</li>
						<li>Texts can be classified into max two levels of classification, into Groups and Categories.</li>
					</ul>
				</li>
				<li>An <strong>Advanced TMS</strong>, called <strong>Texts</strong>, which can be used by individuals and organizations for a <strong>continuous flow</strong> of an unlimited number of texts.
					<ul>
						<li>This TMS can be designed in different forms, as a <strong>Blog</strong>, as a complex <strong>Source of Information</strong> provided by public and private organizations, and as electronic <strong>Newspaper</strong>.</li>
						<li>Texts can be <strong>Classified</strong> into three Levels of Classification. Separately, they can also be classified into unlimited <strong>Themes</strong> and Groups of Themes. They can also be related to each other, if they deal with a common subject that might not be classified as a Theme.</li>
						<li>You can add <strong>Comment</strong> function separately for each text.</li>
					</ul>
				</li>
				<li>A <strong>Medium TMS</strong>, called <strong>Articles</strong>, that can be used both for texts, as in the above two cases, but also for the presentation of products and services.</li>
				<li>A <strong>Multi-section TMS</strong>, called <strong>Posts</strong>, where you can create, in one and the some page, multiple <strong>sections</strong>, each one with multiple <strong>items</strong> and a <strong>unique design</strong> (based on templates). </li>
			</ol>
			<h3>4. Additional applications - settings and functions</h3>
			<p>Depending on the design, some <strong>Additional Applications</strong> might be installed for use, such as Members Area, Various Login Systems, Photo Gallery, Multimedia, PDF-Archive, Gallop and Surveys, FAQ, Reports, Events Calendar, Forum, Links, Library, Conferences, Courses with student registration, etc.</p>
			<ul>
				<li>You can dynamically define the use of some of these applications from the Table <strong>Site Apps Configuration </strong>in the Table Group <strong>Initial Settings</strong>.</li>
				<li>Additional Applications usually have their own <strong>Setup Table</strong> for detail configuration of their respective functions.</li>
			</ul>
			<h3>5. Upload Multimedia, Images and PDF-Files in Separate Default Folders.</h3>
			<p>In many Tables there are <strong>Input Fields </strong>where you can add links to <strong>Multimedia, Images and PDF Files</strong>.</p>
			<ol>
				<li>You can link to files that you have uploaded to your <strong>Web Server</strong> or to external multimedia pages (e.g. <strong>YouTube</strong>).</li>
				<li>The program automatically detects the <strong>URL Source</strong> and the<strong> Type</strong> of the file and opens the corresponding application.</li>
			</ol>
			<p>All files must first be <strong>uploaded</strong> to the Web Server (see Help for Table Group <strong>Tools</strong>). Depending on the <strong>File Type</strong>, you upload files to three <strong>Default</strong> or <strong>Parent Folders</strong>:</p>
			<ol>
				<li>Upload <strong>Video</strong> or <strong>Multimedia</strong> Files (.mp3, mp4, .webM, and .ogg) to the default media folder <strong>imgMedia/</strong> and write in the input field only the <strong>name</strong> of the file: e.g. <strong>video.pm4</strong>.</li>
				<ul>
					<li>The program automatically searches for multimedia files in the default folder <strong>imgMedia/</strong>.</li>
				</ul>
				<li>Upload <strong>Image</strong> and <strong>photo</strong> Files (jpeg, jpg, gif, png and svg) to the default image folder <strong>images/</strong> and write in the input field only the <strong>name</strong> of the file: e.g. <strong>image.jpg</strong>.
					<ul>
						<li>The program automatically searches for all images in the default folder <strong>images/</strong>.</li>
					</ul>
				</li>
				<li>Upload <strong>PDF</strong> Files (and also WORD and EXCEL Files) to the default PDF folder <strong>imgPDF/</strong> and write in the input field only the <strong>name</strong> of the file: e.g. <strong>fileName.pdf</strong>.
					<ul>
						<li>The program automatically searches for all the PDF-Files in the default folder <strong>imgPDF/</strong>.</li>
					</ul>
				</li>
				<li>For the best organization of your files, you can <strong>create subfolders</strong> within each one of the above default folder - or <strong>Parent Folders</strong>.
					<ul>
						<li>For example: imgMedia/subfolder1/, images/subfolder1/, imgPDF/subfolder1/, etc.</li>
						<li>In that case you must also write the <strong>name of the subfolder</strong> in the input field: subfolder1/video.pm4, subfolder1/image.jpg, subfolder1/fileName.pdf, etc.</li>
						<li>It is a <strong>good practice</strong> to upload the most of files in subfolders, according to their content: you find them easier and you don’t need to download multiple files when you search for one of them in the <strong>Folder Navigation System</strong> (see bellow).</li>
					</ul>
				</li>
			</ol>
			<h3>6. Image Sizes - Resize, Crop and Upload images</h3>
			<p>You can <strong>Resize, Crop </strong>and<strong> Upload</strong> Images from the link <strong>Upload Images</strong> in the Table Group <strong>Tools</strong>.</p>
			<p>Two dimensions of images are important for a consistent design of a site:</p>
			<ol>
				<li>The <strong>quality</strong> of an images, which roughly depends on the size of its <strong>Height</strong> and <strong>Width</strong> in pixels and the richness of its colors.</li>
				<ul>
					<li>The dilemma here is that the better quality the bigger the file and the more bytes to download for the visitor.</li>
				</ul>
				<li>The <strong>Height/Width Ratio</strong> of an image:</li>
				<ul>
					<li>The width of images is automatically extended in all places of the site to cover the width of the surrounding container. </li>
					<li>Consequently, an image with a <strong>Height/Width</strong> 500px/1000px take the <strong>same place</strong> in the site as an image with 200px/400px, as both have the Ratio 0,5.</li>
				</ul>
			</ol>
			<p>For the <strong>consistency</strong> of the site’s design you might use the following convention for images:</p>
			<ol>
				<li>For <strong>horizontal</strong>, <strong>wide screen</strong> images, crop the images consistently to a ratio of 0,5 of even 0,4 with a medium quality and a minimum H/W sizes of 500px/1000px or 400px/1000px.</li>
				<ul>
					<li>For slider images or other important images, you can increase the size of H/W but keep a constant ratio (e.g. 800/1600).</li>
				</ul>
				<li>For <strong>vertical</strong> images, crop the images <strong>consistently</strong> to a ratio of 1,25 or 1,5 with medium quality and minimum H/W sizes of 500/400 or 600/400.</li>
			</ol>
			<h3>7. Create and Delete Subfolders</h3>
			<p>For all default folders (images/, imgMedia/, imgPDF/, imgGallery/) you can create unlimited Subfolders within which you can create Sub-Subfolders, etc.</p>
			<ol>
				<li>You Create and Delete subfolders from Group <strong>Tools</strong>, by clicking on one of the three functions for <strong>Uploading Files</strong> (see below)</li>
				<li>Just click on the Tabs <strong>Create Subfolder</strong> or <strong>Delete Subfolder</strong> on the top of the page.</li>
				<li>A subfolder must be empty in order to be <strong>deleted</strong>.</li>
			</ol>
			<h3>8. Upload, View and Delete Files</h3>
			<p>You can use three different function for uploading files, all found in the Group <strong>Tools</strong>:</p>
			<ol>
				<li>Use the <strong>Upload Files</strong> to upload any <strong>Allowed File Type</strong> up to 10mb.</li>
				<li>Use the <strong>Upload Images</strong> to Resize or Crop one or more images before uploading.</li>
				<li>Use the <strong>Upload Big Files</strong> mainly to upload video files bigger then 10mb, up to 1000mb.</li>
			</ol>
			<p>You can open any folder and subfolder and view its files either as a list of names or as images, for image files.</p>
			<ol>
				<li>Click on any of the above three different function for uploading files, all found in the Group <strong>Tools</strong>.</li>
				<li>Click then on the Tabs <strong>View Folder Files</strong> or <strong>View Images</strong>, on the top of the page.</li>
				<li>You can in both cases mark multiple files and than <strong>delete</strong> them.</li>
				<li> You <strong>cannot</strong> change the name of a file, you can delete it and upload a file with correct name.</li>
			</ol>
			<h3>9. Folder Navigation System: Copy the Correct Subfolder and Name of Files</h3>
			<p>Every time you add a new record to any table, a Tab naming <strong>Copy Image Names</strong> appears on the top of the page. </p>
			<ol>
				<li>Click on the tab <strong>Copy Image Names</strong> to open a <strong>Folder Navigation System</strong>, which include a list of all folders and subfolders available in the server.</li>
				<li>Select on of these folders to get a list of all including files. Image files are presented as small images.</li>
				<li>Read the instructions to copy the correct link and name of the files to corresponding input fields of the open form.</li>
				<li>Basically, you <strong>Mark</strong> one or more files and then <strong>Double Click</strong> on the corresponding input field to automatically copy the correct link to them.</li>
				<li>So, you <strong>never need</strong> to write manually the links to files!</li>
			</ol>
			<p> </p>
		</div>
	</div>
	<div id="layer1" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Articles</h1>
		<div class="text">
			<p>Articles can be used both for texts and for the presentation of products and services.</p>
			<p>An article is divided into two <strong>multimedia</strong> sections and three <strong>text</strong> sections in the following order:</p>
			<ol>
				<li><strong>Multimedia</strong>, that can include one or more files of any type that is readable by Browsers: images, videos, soundtracks, and presentation (like PowerPoint) which are in the local server or in external sources (like YouTube).
					<ul>
						<li>If the multiple files are <strong>images</strong>, in this first section, you have the option to display them either in a manual <strong>slider</strong> or as <strong>gallery</strong>.</li>
						<li>Multiple videos and sound appear as cards side by side or under each other in tables and mobiles.</li>
					</ul>
				</li>
				<li><strong>Text</strong>, that can concern any theme or describe the above media (usually with gray background).</li>
				<li><strong>Multimedia</strong>, as above, although particularly aimed for videos.</li>
				<li><strong>Text</strong>, that can concern any theme or describe the above media (usually with gray background).</li>
				<li><strong>The main Text </strong>of the article (usually with white/transparent background)</li>
			</ol>
		</div>
		<h1>List of Tables in the Table Group: Articles</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=articles">Articles</a>
				</td>
				<td valign="top" width="100%">
					<p>Articles can be used both for <strong>texts</strong> and for the presentation of <strong>products</strong> and <strong>services</strong>.</p>
					<p>An article is divided into two <strong>multimedia</strong> sections and three <strong>text</strong> sections in the following order:</p>
					<ol>
						<li><strong>Multimedia</strong>, that can include one or more files of any type that is readable by Browsers: images, videos, soundtracks, and presentation (like PowerPoint) which are in the local server or in external sources (like YouTube).
							<ul>
								<li>If multiple files are <strong>images</strong>, in this first section, you have the option to display them either in a manual <strong>slider</strong> or as <strong>gallery</strong>.</li>
								<li>Multiple videos and sound appear as <strong>cards</strong> side by side or under each other in tables and mobiles.</li>
							</ul>
						</li>
						<li><strong>Text</strong>, that can concern any theme or describe the above media (usually with gray background).</li>
						<li><strong>Multimedia</strong>, as above, although particularly aimed for videos.</li>
						<li><strong>Text</strong>, that can concern any theme or describe the above media (usually with gray background).</li>
						<li><strong>The main Text<span> </span></strong>of the article (usually with white/transparent background)</li>
					</ol>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=article_groups">Article Groups</a>
				</td>
				<td valign="top" width="100%">
					<p>Necessary classification of Articles into Groups.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=article_categories">Article Categories</a>
				</td>
				<td valign="top" width="100%">
					<p>Optional classification of Articles <em>into</em> Categories, which are classifications of Groups. So every Category must belong to a Group.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer2" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: About</h1>
		<div class="text">
			<h2>Simple Text Management System</h2>
			<h3>About: Describe your Website</h3>
			<p>Basically you use this Text Management System (TMS) for a <strong>finite</strong> number of texts that describe your website or your organization. Usually, these text are not changed frequently but must be constantly visible and easily accessible for the visitors.</p>
			<p>However, you can use this TMS to provide extensive information to your visitors about any topic related to your site, with an <strong>unlimited</strong> number of constant texts.</p>
			<ul>
				<li>Texts can be <strong>classified</strong> into two levels, into Groups and Categories.</li>
				<li>Links to texts are <strong>All Visible</strong> in an <strong>Accordion Menu</strong>, organized by Groups and Categories.</li>
			</ul>
			<p>Selected texts can also appear:</p>
			<ul>
				<li>in a <strong>Drop-Down Menu</strong> within the <strong>Main (Top) Navigation</strong> of the site, or</li>
				<li>in a <strong>List</strong> within the <strong>Footer</strong> of the site.</li>
			</ul>
		</div>
		<h1>List of Tables in the Table Group: About</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=about">About</a>
				</td>
				<td valign="top" width="100%">
					<p><strong>About</strong> table is to be used for texts (called <strong>About Texts</strong>) that describe your site or your organization - or texts providing information about any other topic.</p>
					<ul>
						<li>Use this table for text that <strong>not change frequently</strong> but will be accessible by visitors independently of their publication date.</li>
						<li>You can enter here a <strong>finite</strong>, although unlimited, number of texts.</li>
						<li>Links to all texts appear on the <strong>Navigation Menu</strong>, optionally classified in Groups and Categories.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=about_categories">About Categories</a>
				</td>
				<td valign="top" width="100%">
					<p>The table for the <strong>secondary</strong> classification of About Texts.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=about_groups">About Groups</a>
				</td>
				<td valign="top" width="100%">
					<p>The table for the <strong>primary</strong> classification of About Texts.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer3" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Promotion</h1>
		<div class="text">
			<p><strong>Promotion</strong><span> </span>is used in the<span> </span><strong>First Page</strong><span> </span>of the site to inform the visitor about the content of the site, with internal <strong>Links</strong> to pages that provide more detail information.</p>
			<ul>
				<li><strong>Promotion</strong> is organized into <strong>Sections</strong>, each one concerning a particular type of information about the content of the site. You define a section by a Title and, optionally, by a short Text.
					<ul>
						<li>You can place the title and the short text on the top, the left or the right side of the section.</li>
					</ul>
				</li>
				<li>Every <strong>Section</strong> can contain one or more <strong>Elements</strong> that are grouped and sorted in <strong>Rows</strong>.
					<ul>
						<li>You can create uncountable rows with max 4 elements per row.</li>
						<li>The place of the rows and their elements is depending of the place of the section title.</li>
					</ul>
				</li>
				<li>Every <strong>Element</strong> can include a Title, an Image or Video, a Short Description and max 2 Links to internal or external sources.</li>
			</ul>
		</div>
		<h1>List of Tables in the Table Group: Promotion</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=promotion_elements">Promotion Elements</a>
				</td>
				<td valign="top" width="100%">
					<p>Every <strong>Section</strong> can contain one or more <strong>Elements</strong> that are grouped and sorted in <strong>Rows</strong>.</p>
					<ul>
						<li>You can create uncountable rows with max 4 elements per row.</li>
						<li>The place of the rows and their elements is depending of the place of the section title.</li>
					</ul>
					<p>Every <strong>Element</strong> can include a Title, an Image or Video, a Short Description and max 2 Links to internal or external sources.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=promotion_sections">Promotion Sections</a>
				</td>
				<td valign="top" width="100%">
					<p><strong>Promotion</strong> is organized into <strong>Sections</strong>, each one concerning a particular type of information about the content of the site. You define a section by a Title and, optionally, by a short Text.</p>
					<ul>
						<li>You can place the title and the short text on the top, the left or the right side of the section.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=templates">Templates</a>
				</td>
				<td valign="top" width="100%">
					<p>You use <strong>Templates</strong> to give the different <strong>Promotions Sections</strong> a unique design.</p>
					<p>You can create uncountable <strong>Design Templates</strong> and give them a <strong>Unique Name</strong>.</p>
					<ul>
						<li>You can then use for every particular Promotion Section the template you like by selecting its unique name.</li>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer4" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Reports</h1>
		<div class="text">
			<h2>HTML Reports</h2>
			<p>The <strong>Application Reports </strong>is basically a System for publishing electronic <strong>HTML-Books</strong> and is integrated with the design of the website.</p>
			<ul>
				<li>It is originally developed to <strong>Report</strong> in a chronological order current <strong>Research Results</strong> from an ongoing <strong>Research Project.</strong></li>
			</ul>
			<p>The basic ideas are the following:</p>
			<ul>
				<li>The visitor opens a <strong>Research Project</strong>, which you define as the title of a <strong>Book</strong>.</li>
				<li>A <strong>Navigation Menu</strong> appears as a <strong>Table of Contents</strong> that contains the <strong>Chapters </strong>and eventually the <strong>Subchapters</strong> of the Book.</li>
				<li>The final links in the Table of Contents open the <strong>Texts</strong> of the book as on or more <strong>Sections</strong> of:
					<ul>
						<li>a <strong>Chapter</strong>, if the chapter does not contain Subchapters, or</li>
						<li>a <strong>Subchapter</strong>, if a chapter contains Subchapters.</li>
					</ul>
				</li>
				<li>So, the text that you enter each time might be a Section of a Chapter or Subchapter.</li>
			</ul>
			<p>Every <strong>Text</strong> can contain:</p>
			<ul>
				<li>On the <strong>Top</strong> of the text: an image, a multimedia or multiple images in a slider (just add multiple images in the corresponding field and separate them with a semicolon(;)).</li>
				<li>On the <strong>Right</strong> of the text: multiple images, the one under the other, with a common describing text (just add multiple images in the corresponding field and separate them with a semicolon (;)).</li>
			</ul>
			<p>For every Project (or Book) you can enter multiple images (maps, schedules, etc.) that appear constantly on the Right Column of the site, under the Table of Contents, when the visitor opens a Report. </p>
		</div>
		<h1>List of Tables in the Table Group: Reports</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=reports">Reports</a>
				</td>
				<td valign="top" width="100%">
					<p>For every Research Project or e-Book, you enter here its various <strong>texts</strong>, classified into Chapters and occasionally into Subchapters.</p>
					<ul>
						<li>Please follow the <strong>Help</strong> provided in relevant Input Fields.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=report_projects">Report Projects</a>
				</td>
				<td valign="top" width="100%">
					<p>Enter here the <strong>name</strong> of every Research Project, which will function as the <strong>Title</strong> of the Report or electronic Book.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=report_setup">Report Setup</a>
				</td>
				<td valign="top" width="100%">
					<p><strong>Initial settings</strong> for the Application Reports.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer5" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Communication</h1>
		<div class="text">
			<h2>Communication</h2>
			<p>This group includes basically two tables, a table with subscriptions to the site’s <strong>Newsletters</strong> and a table with links to <strong>Social Media</strong> where the site has an active presence. Depending on the design, the group can also include a table with registered <strong>Users</strong> of the site.</p>
			<p>The group also includes a function for <strong>Sending Multiple Mails</strong>, basically for sending Newsletters and, eventually, information to Users.</p>
			<p>Moreover, depending on the additional applications that are used by the site, the function for <strong>Sending Multiple Mails</strong> can use tables with <strong>registered participants</strong> of other activities provided by the site, such as Forum Members, Conference Participants, Students, Staff members, etc.</p>
		</div>
		<h1>List of Tables in the Table Group: Communication</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=newsletters">News Letters</a>
				</td>
				<td valign="top" width="100%">
					<p>Website visitors who register to get Newsletters are automatically added to this table, with their email address and optionally their name. For multilingual sites, the current site language is also automatically added to the this Table. Thereby, you can send newsletters in different languages.</p>
					<p>You specify the use of this function from the <strong>Site Apps Configuration </strong>Table in the <strong>Initial Settings</strong> group .</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=newsletter_groups">News Letter Groups</a>
				</td>
				<td valign="top" width="100%">
					<p>Optional classification of the Subscribers to Newsletter in order to sent different emails to different groups. For the current version of the program, subscribers must be manually classified into groups by the Site Administrator.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=social_media">Social Media</a>
				</td>
				<td valign="top" width="100%">
					<p>You define here links to Social Media (Facebook, Twitter, etc.) where your website has an active account with relevant information. In other words, you <strong>send</strong> your visitors to Social Media.</p>
					<p>The function of displaying links to Social Media at the top of each text is different: from there, the visitor links an article to his/her own account on a Social Media.</p>
					<p>You specify the use of both functions from the <strong>Site Apps Configuration </strong>Table in the <strong>Initial Settings</strong> group.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer6" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Advertises</h1>
		<div class="text">
			<h2>Advertising Functions</h2>
			<p>The term <strong>Advertising</strong> (ads) is purely technical. Tables for advertising can be used for any kind of information.</p>
			<p>From the table <strong>Site Basic Configuration</strong>, in the Group <strong>Initial Settings</strong>, you can define if you will use advertises.</p>
			<p>Advertises can contain text and images or videos. </p>
			<ul>
				<li>Multiple images entered in an Input Field and separated by a semicolon transform an advertise to a <strong>slider</strong>.</li>
				<li>Depending on their place, ads can appear as single Cards, Multiple Cards, deployed side by side, or as Single or Multiple Sliders.</li>
				<li>Ads can link to external web pages, which automatically open in a separate window, or to internal pages, which automatically open in the same window.</li>
			</ul>
			<p>There are four tables used for advertising, with different functions and purposes:</p>
			<ol>
				<li><strong>Advertises Logo:</strong> for ads that appear on the Right of the Logotype, on the top of the site.
					<ul>
						<li>Basically, used for short ads or for actual messages to the visitor.</li>
						<li>You can show here only one advertise each time.</li>
					</ul>
				</li>
				<li><strong>Advertises Header</strong>: for a limited number of advertises on the <strong>Top</strong> of the <strong>Main Body</strong>, under the <strong>Header</strong> of the Site and above Texts.</li>
				<li><strong>Advertises Main:</strong> for multiple forms of ads in the <strong>Main (Middle) Body</strong> of the Site, depending on the design.
					<ul>
						<li>Usually, ads appear on the Top and Bottom of the Right Column and within Texts, depending on the design.</li>
						<li>Ads can be defined to appear only in the First Page, in the Articles Page, in the About Page or in All pages.</li>
						<li>You can also have different ads for different classifications of texts, both for About Texts and Main Texts.</li>
					</ul>
				</li>
				<li><strong>Advertises Footer:</strong> for ads that appear on the <strong>Bottom</strong> of the <strong>Main Body</strong>, above the <strong>Footer</strong> of the Site.
					<ul>
						<li>Basically, these table is used to deploy multiple ads that appear as Cards, side by side, or as Sliders.</li>
					</ul>
				</li>
			</ol>
		</div>
		<h1>List of Tables in the Table Group: Advertises</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=advertises_main">Advertises Main</a>
				</td>
				<td valign="top" width="100%">
					<p>You enter here Advertises (ads) for the various columns of the <strong>main body</strong> of the website, depending on the current design. Advertises can include:</p>
					<ul>
						<li>A text with a title and an image or multimedia.</li>
						<li>If you enter multiple images in an input field, separated by semicolon (;), advertises appear automatically in a <strong>Slider</strong>.</li>
					</ul>
					<p>The various positions of the ads are <strong>predefined</strong> and depend on the current design of the website. The most common <strong>default settings</strong> are the following:</p>
					<ul>
						<li>In the <strong>right</strong> column, they appear at the <strong>top</strong> and <strong>bottom</strong> of the column and are identified by the options:
							<ul>
								<li><strong><em>Top</em></strong> and<strong><em> Bottom</em></strong><em> .</em></li>
								<li><strong><em>TopSlider</em></strong> and<strong><em> BottomSlider</em></strong> .</li>
							</ul>
						</li>
						<li>For all ads, you can also specify whether they will appear on the first page, on all pages, or in specific text groups.
							<ul>
								<li>This way you can have <strong>different ads</strong> on the first and other pages as well as in different text groups.</li>
							</ul>
						</li>
						<li>All photos that will appear in the same Slider (TopSlider or BottomSlider), should have the same <strong>Height/Width ratio</strong> .
							<ul>
								<li>The <strong>first</strong> photo of the Slider defines the height of the Slider.</li>
							</ul>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=advertises_footer">Advertises Footer</a>
				</td>
				<td valign="top" width="100%">
					<p>You enter here <strong>Multiple</strong> Advertises that will appear above the <strong>footer</strong> of the website, in the first page and in any other page, depending on the design.</p>
					<p>You can select between two <strong>alternative forms</strong> of appearance:</p>
					<ol>
						<li>As a <strong>Slider</strong>, where each page of the slider can contain multiple ads, side by side.
							<ul>
								<li>Just enter multiple ads and select for each one of the option <em><strong>FooterCycler</strong></em>.</li>
							</ul>
						</li>
						<li>As <strong>Cards</strong>, displaying multiple advertises all together, next to each other and in different rows.
							<ul>
								<li>You can create two separate sets of Cart Advertises: <em><strong>Footer</strong></em> and <em><strong>FooterMore</strong>.</em></li>
								<li>Just enter multiple ads and select for each one of them the option <em><strong>Footer</strong></em> or <em><strong>FooterMore</strong></em>.</li>
							</ul>
						</li>
					</ol>
					<p>You can create a common <strong>title</strong> and <strong>description</strong> separately for each one of these three options (FooterCycler, Footer and FooterMore):</p>
					<ul>
						<li>Just enter a new advertise, select one of the above three options and mark it as <em><strong>Common Title</strong></em>.</li>
						<li>The title and the text of this advertise will then appear as the title and description of the corresponding set of advertises.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=advertises_logo">Advertises Logo</a>
				</td>
				<td valign="top" width="100%">
					<p>You can enter here only one active advertise that will appear on the <strong>Header</strong> of Website, beneath the <strong>Logo</strong> of the site, depending on the design, used usually for actual information or information about the identity of the site.</p>
					<p>You can also enter here an advertise - or rather an <strong>actual message</strong> - that opens as a popup <strong>dialog box</strong> when the visitor opens the site for <strong>the first time</strong>.</p>
					<ul>
						<li>The dialog box ask the visitor to confirm the reception of the message by clicking on the OK button, whereby a  <strong>cookie</strong> is added that ensures that the message will NOT appear again.</li>
					</ul>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer7" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Library</h1>
		<div class="text">
			<h2>Library</h2>
			<p>The Application <strong>Library</strong> is an advanced Library System that can be integrated with any site. Books can be classified:</p>
			<ol>
				<li>into tree levels, by <strong>Groups</strong>, <strong>Categories</strong> and <strong>Subcategories</strong></li>
				<li>by their <strong>place</strong> in the library or the <strong>location</strong> of multiple libraries,</li>
				<li>by their <strong>publisher</strong>,</li>
				<li>by their <strong>year</strong> of publication</li>
				<li>by their writers or <strong>authors</strong></li>
			</ol>
			<p>You can <strong>search</strong> books by all the above classifications and by their Title and Subtitle.</p>
			<p>You can include <strong>multiple authors</strong> for every book, as the table of books and the table of authors are separated from each other and connected by a book-to-authors table that defines the relationship between every book and its authors.</p>
			<p>You can also, for every book, include <strong>links</strong> to external sources (bookshops or published PDF-versions of books) or to the Application <strong>PDF-Archives</strong> of your site.</p>
			<p>Optionally, you can also include <strong>reviews</strong> and <strong>ratings</strong> for every book.</p>
		</div>
		<h1>List of Tables in the Table Group: Library</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=books">Books</a>
				</td>
				<td valign="top" width="100%">
					<p>The Library table that includes all books.</p>
					<p>When <strong>adding new books</strong>, use the function <strong>Load Authors</strong>, which appears on the Top-Right sida of the page, to <strong>add new</strong> authors and to <strong>link</strong> each book to one or <strong>more</strong> authors.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_groups">Book Groups</a>
				</td>
				<td valign="top" width="100%">
					<p>Primary and <strong>neccessary</strong> classification of books into groups.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_categories">Book Categories</a>
				</td>
				<td valign="top" width="100%">
					<p>Secondary and <strong>optional</strong> classification of books into categories - subdivisions of groups.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_subcategories">Book Subcategories</a>
				</td>
				<td valign="top" width="100%">
					<p>Tertiary and <strong>optional</strong> classification of books into subcategories - subdivisions of categories.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_place">Book Place</a>
				</td>
				<td valign="top" width="100%">
					<p>You can use this table in two alternative ways:</p>
					<ol>
						<li>If books are in different libraries, list here the libraries or their location.</li>
						<li>If all books are in the same Library, you can list here the classification of the library places or shelfs.</li>
						<li>You might also combine these two alternatives</li>
					</ol>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_setup">Book Setup</a>
				</td>
				<td valign="top" width="100%">
					<p>Initial <strong>Settings </strong>for the Application Library.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_authors">Book Authors</a>
				</td>
				<td valign="top" width="100%">
					<p>List of authors with their bibliography.</p>
					<p>You can add new authors directly from this table.</p>
					<p>You can also add new authors when adding a new book:</p>
					<ul>
						<li>Click on the <strong>Load Authors</strong> button, which appears on the Top-Right of the page.</li>
						<li>Use the function that opens on the right side both to add new authors and to connect a new book to one or more authors.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=book_to_authors">Book To Authors</a>
				</td>
				<td valign="top" width="100%">
					<p>The table that <strong>connects</strong> each book to one or more authors.</p>
					<p>Basically, you don’t need to edit this table. The connections are added automatically when adding a new book by using the function <strong>Load Authors</strong>, which appears on the Top-Right side of the page (see above).</p>
					<p>You edit this table carefully only to correct entry errors.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer8" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Gallery</h1>
		<div class="text">
			<h2>Photo Gallery</h2>
			<p><strong>Photo Gallery</strong> is independent applications that always open in a separate window.</p>
			<ul>
				<li>You upload all your Gallery Photos to the default Galllery Folder <strong>imgGalllery/</strong> or to any of the <strong>subfolders</strong> that you can create in it (see <strong>Site Setup</strong> information about the details).</li>
			</ul>
			<p>You can classify your photos into two levels:</p>
			<ol>
				<li>Galleries</li>
				<li>Groups (or Categories) of Galleries.</li>
			</ol>
			<p>The basic <strong>advantage</strong> of this application is that you can provide detail Information for every particular photo.</p>
			<p>The basic <strong>disadvantage</strong> is that you must enter every photo as a separate entry in the table for Gallery Photos.</p>
			<p>In the Main and About <strong>Texts</strong> of the website you can enter a link to a gallery by entering the ID Number of that gallery.</p>
		</div>
		<h1>List of Tables in the Table Group: Gallery</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=gallery_photos">Gallery Photos</a>
				</td>
				<td valign="top" width="100%">
					<p>Here you enter photos and specify the gallery and optionally the group to which they belong.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=galleries">Galleries</a>
				</td>
				<td valign="top" width="100%">
					<p><span>You sort your photos </span><strong><span>necessary</span></strong><span> in various Galleries. </span><strong><span>Optionally</span></strong><span> , galleries can be grouped, creating two levels of classification.</span></p>
					<p><span>Here you can enter a new one or change the name of an old gallery. The visitor can see the photos by selecting a gallery.</span></p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=gallery_groups">Gallery Groups</a>
				</td>
				<td valign="top" width="100%">
					<p><span>Galleries can </span><strong><span>optionally</span></strong><span> be grouped. Here you can enter a new one or change the name of an old group.</span></p>
					<p><span>The visitor can open a group and then one of the galleries it includes.</span></p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=gallery_setup">Gallery Setup</a>
				</td>
				<td valign="top" width="100%">
					<p>Primary settings regarding the use of the Gallery</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer9" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Folder Gallery</h1>
		<div class="text">
			<h2>Photo Gallery by Folder</h2>
			<p><strong>Photo Gallery by Folder</strong> is independent applications that can be either <strong>integrated</strong> with the design of the website or open in a <strong>separate</strong> window.</p>
			<p>You can classify your photos into two levels:</p>
			<ol>
				<li>Galleries</li>
				<li>Groups (or Categories) of Galleries.</li>
			</ol>
			<p>The basic <strong>advantage</strong> of this application is its <strong>simplicity</strong>:</p>
			<ul>
				<li>You can create a photo gallery very <strong>fast</strong>, simply by <strong>connecting</strong> a <strong>Gallery</strong> to a <strong>Folder</strong>: all photos that you upload to that folder will automatically be displayed  as photos of the corresponding gallery.</li>
				<li>You upload all your Gallery Photos to <strong>subfolders</strong> that you can create within the default Galllery Folder <strong>imgGalllery/</strong> (see <strong>Site Setup</strong> information about the details).</li>
			</ul>
			<p>The basic <strong>disadvantage</strong> is that you cannot provide any detail information about a particular photo - except the information you can include in its name:</p>
			<ul>
				<li>The <strong>Name</strong> of the photo, without its extension, appears as <strong>Title</strong> of the photo.</li>
				<li>If you separate the words of the name by the characters _ or -, without spaces, the program will replace these characters <strong>with spaces</strong> and display the name as title:
					<ul>
						<li><em>This_is-the_name-of_the-Phote-2010.jpg</em> will be transformed to: <strong><em>This is the name of the photo 2010</em>.</strong></li>
					</ul>
				</li>
			</ul>
			<p>In the Main and About <strong>Texts</strong> of the website you can enter a link to a gallery by entering the ID Number of that gallery.</p>
		</div>
		<h1>List of Tables in the Table Group: Folder Gallery</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=folder_galleries">Folder Galleries</a>
				</td>
				<td valign="top" width="100%">
					<p>Enter here the <strong>name</strong> of the gallery and the <strong>folder</strong> that contains the photos.</p>
					<ul>
						<li>The folder must be a <strong>subfolder</strong> of the default gallery folder: <strong>imgGallery/</strong></li>
						<li>You just write the name of the folder: all phots in that folder will appear in the gallery.</li>
					</ul>
					<p>You cannot <strong>describe</strong> the content of a photo, but you can give <strong>meaningful</strong> names to photos, which will be displayed as <strong>titles</strong>:</p>
					<ul>
						<li>Separate the words of the photo name with the characters _ or - without spaces.</li>
						<li>The program will replace these characters with spaces, remove the file extension of the photo and display a title.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=folder_gallery_groups">Folder Gallery Groups</a>
				</td>
				<td valign="top" width="100%">
					<p>Optional classification of folder galleries into groups.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=folder_gallery_setup">Folder Gallery Setup</a>
				</td>
				<td valign="top" width="100%">
					<p>Initial Settings for the folder gallery.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer10" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Multimedia</h1>
		<div class="text">
			<h2>Multimedia Gallery</h2>
			<p><strong>Multimedia Gallery</strong> is an independent applications that always opens in a separate window.</p>
			<ul>
				<li>You upload all your Media Files to the default Media Folder <strong>imgMedia/</strong> or to any of the <strong>subfolders</strong> that you can create in it (see <strong>Site Setup</strong> information about the details).</li>
				<li>However, you can also insert links to <strong>external</strong> media files, such as, for example, <strong>YouTube</strong>.</li>
			</ul>
			<p>The application works much like the Photo Gallery but opens Media Files (<strong>videos</strong> and <strong>music</strong>) instead of photos.</p>
			<ul>
				<li>You can <strong>Classify</strong> Media Files into two levels: Groups and Categories.</li>
				<li>Links to Media Files appear in <strong>Accordion Menus </strong>which can be ordered by Classification, by Year of Publication or by both Classification and Year of Publication.</li>
				<li>All Text Tables include <strong>input fields</strong> where you can directly enter the ID Number of a Media File which automatically creates a link to open that file in <strong>Multimedia Gallery</strong>.</li>
			</ul>
		</div>
		<h1>List of Tables in the Table Group: Multimedia</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=media_archives">Media Archives</a>
				</td>
				<td valign="top" width="100%">
					<p>You enter here information about each Multimedia file and the <strong>link</strong> to it, which might be of two different kinds:</p>
					<ul>
						<li>For <strong>internal</strong> files (uploaded to the server):
							<ul>
								<li>Uploade all media files to the default media folder: <strong>imgMedia/</strong> or to subfolders that you can create within this default folder.</li>
								<li>Link to an internal media file just by writingthe subfolder name, if any, and the name of the file: <em><strong>subFolderName/fileName</strong></em>.</li>
							</ul>
						</li>
						<li>For <strong>external</strong> file (e.g from the YouTube), write the full URL to it.<br />
							<ul>
								<li>Please notice that you have to write only the <strong>pure URL</strong>, ususally a text within quatation merks after the attribute <strong>href</strong> (href="<em><strong>copy the text here</strong></em>").</li>
								<li>Do not enter the <strong>iFrame</strong> often provided by external sources.</li>
							</ul>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=media_categories">Media Categories</a>
				</td>
				<td valign="top" width="100%">
					<p>Optional classification of multimedia into categories.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=media_setup">Media Setup</a>
				</td>
				<td valign="top" width="100%">
					<p><strong>Initial Settings</strong> for the multimedia application.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer11" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: PDF-Archives</h1>
		<div class="text">
			<h2>PDF-Archives Gallery</h2>
			<p><strong>PDF-Archives Gallery</strong> is and independent applications that opens always in a separate window.</p>
			<ul>
				<li>You upload all your PDF-files to the default PDF-Folder <strong>imgPDF/</strong> or to any of the <strong>subfolders</strong> that you can create in it (see <strong>Site Setup</strong> information about details).</li>
			</ul>
			<p>The PDF-Archives Application is a complete system for the classification and reading of PDF-Files. It works much like the Photo Gallery but opens PDF Files instead of photos.</p>
			<ul>
				<li>You can <strong>Classify</strong> PDF Files into three levels: Groups, Categories and Subcategories.</li>
				<li>Links to PDF-Files appear in <strong>accordion menus</strong> which can be ordered by Classification, by Year of Publication or by both Classification and Year of Publication.</li>
				<li>All Text Tables and many other tables include <strong>input fields</strong> where you can directly write the ID Number of a PDF-File in the PDF-Archives Application. This creates automatically a <strong>link</strong> that opens that file in the PDF-Archives Application.</li>
			</ul>
			<p>The PDF-Archives Application can also be converted to an online <strong>Library</strong>, for books or texts in PDF format. It can also be converted to a <strong>Journal</strong>, showing the PDF Files of a Journal issue.</p>
		</div>
		<h1>List of Tables in the Table Group: PDF-Archives</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=pdf_archives">PDF Archives</a>
				</td>
				<td valign="top" width="100%">
					<p>You enter here information about each PDF-File and the name of the file, for the creation of the link to it.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=pdf_groups">PPDF Groups</a>
				</td>
				<td valign="top" width="100%">
					<p>Primary and <strong>necessary</strong> classification of PDF files into groups. Each group can <strong>optionally</strong> contain multiple categories.</p>
					<p>It is possible, if you do not want to specify Groups and Categories, for the files to be sorted by years and months, according to the date of their publication or entry in the database.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=pdf_categories">PPDF Categories</a>
				</td>
				<td valign="top" width="100%">
					<p><strong>Optional</strong> sorting of PDF files into Categories, which are a subdivision of Groups.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=pdf_setup">PPDF Setup</a>
				</td>
				<td valign="top" width="100%">
					<p><strong>Initial Settings</strong> for the PDF-Archives application.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer12" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Links</h1>
		<div class="text">
			<p>The <strong>Links Application</strong> is a separate system that can be integrated with any design of the site and contain the following functions:</p>
			<ol>
				<li>You can create <strong>Lists of Links</strong> to websites that you want to recommend to your visitors, classified into <strong>Categories</strong>. All links open in a <strong>New Window</strong>.</li>
				<li>You can define your <strong>Favorite Links</strong>, which appear when the visitor opens the First Page of links.</li>
				<li>Optionally you can also use the <strong>Logotype</strong> of the favorite website as Link.</li>
				<li>Depending on the design of your website, your favorites may also appear anywhere in the First Page of your site.</li>
			</ol>
		</div>
		<h1>List of Tables in the Table Group: Links</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=link_categories">Link_categories</a>
				</td>
				<td valign="top" width="100%">
					<p>Enter the different categories of websites.</p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=links">Links</a>
				</td>
				<td valign="top" width="100%">
					<p>Enter here the full URL address (eg https:://www.efsyn.gr) and the name of the site.</p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer13" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Table Group: Initial Settings</h1>
		<div class="text">
			<h2>Initial Site Configuration</h2>
			<p>You use this group of Tables to <strong>Setup</strong> your website, define its <strong>Adminstrators</strong>, <strong>Configure</strong> some of its main <strong>functions</strong> and select some <strong>Additional Application</strong>.</p>
		</div>
		<h1>List of Tables in the Table Group: Initial Settings</h1>
		<table class="td_borders">
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=site_setup">Site Setup</a>
				</td>
				<td valign="top" width="100%">
					<p><span>Here you enter all the information related to the identity of the website (logos, addresses, manager, etc.).</span></p>
					<p><span>This information is automatically displayed on the website and is essential to the operation of the website (eg sending a message).</span></p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=site_config_basic">Site Basic Configuration</a>
				</td>
				<td valign="top" width="100%">
					<p><span>Here you select the basic </span><strong><span>functions</span></strong><span> of the website and the </span><strong><span>additional applications</span></strong><span> you want to use.</span></p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=site_config_apps">Site Apps Configuration</a>
				</td>
				<td valign="top" width="100%">
					No help available </td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=site_config_texts">Site Text Configuration</a>
				</td>
				<td valign="top" width="100%">
					<p><span>Here you select the basic </span><strong><span>functions</span></strong><span> of the text management system. Some of the features depend on the design of the website and may not be available at this time.</span></p>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=languages">Languages</a>
				</td>
				<td valign="top" width="100%">
					<p>Εδώ προσδιορίζεις τις γλώσσες της ιστοσελίδας.</p>
					<p>Η πρωταρχική γλώσσα πρέπει να έχει τον ΑΑ (<strong>Αύξοντα Αριθμό</strong>) ίσο με 1, η δεύτερη γλώσσα τον ΑΑ 2, κλπ.</p>
					<p>Αν η ιστοσελίδα είναι <strong>μονογλωσσική</strong>, εισήγαγε τα στοιχεία της πρωταρχικής (και μοναδικής) γλώσσας για να διευκολύνεις κάποιες λειτουργίες της ιστοσελίδας, αλλά δεν χρειάζεται στους διάφορους πίνακες να επιλέγεις την μοναδική γλώσσα.</p>
					<ul>
						<li>Το ΑΑ της γλώσσας στους πίνακες αυτούς τίθεται στο 0, και έχει την ίδια λειτουργία με το ΑΑ 1.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=admin_login">Admin Login</a>
				</td>
				<td valign="top" width="100%">
					<p><span>This is where the webmasters are introduced. For the security of your website:</span></p>
					<ul>
						<li><span>He entered a </span><strong><span>strong</span></strong><span> password using the Greek and Latin alphabets and numbers (not symbols).</span></li>
						<li><span>For greater security, if you manage your website from a </span><strong><span>specified IP address</span></strong><span> , an <strong>IP address</strong> can also be added to the table, so that access can only be granted with the correct passwords from that IP address.</span></li>
					</ul>
					<p><span>For security reasons, you can choose between two </span><strong><span>management levels</span></strong><span> :</span></p>
					<ul>
						<li><strong><span>Complete</span></strong><span> (1), where there is no restriction, and</span></li>
						<li><strong><span>Partial</span></strong><span> (2), where a restriction concerns access to this table and deletion of records.</span></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td valign="top" nowrap>
					<a href="list.php?RequestTable=admin_logs">Admin Logs</a>
				</td>
				<td valign="top" width="100%">
					<p><span>Website </span><strong><span>admin</span></strong><span> statistics .</span></p>
					<p><span>Here you can </span><strong><span>check</span></strong><span> if </span><strong><span>strangers</span></strong><span> tried to enter your website management, the (wrong) passwords they used and the IP address from which they logged in.</span></p>
					<p><span>The system records the wrong but not the correct codes.</span></p>
				</td>
			</tr>
		</table>
	</div>
	<div id="layer14" class="maxWidth js_help_main" style="display: none">
		<h1>Information for the Group: Tools</h1>
		<div class="text">
			<p>You find here some of the basic <strong>Tools</strong> of the<strong> Public Sphere CMS</strong>.</p>
			<h3>Upload Files</h3>
			<p>Files are uploaded to predefined <strong>default</strong> folders (images/, imgMedia/, imgPDF/, imgGallery/) and to the <strong>Subfolders</strong> that you can create within them.</p>
			<p>You can use three different function for uploading files:</p>
			<ol>
				<li>Use the <strong>Upload Files</strong> to upload any <strong>Allowed File Type</strong> up to 10mb.</li>
				<li>Use the <strong>Upload Images</strong> to Resize or Crop images before uploading.</li>
				<li>Use the <strong>Upload Big Files</strong> mainly to upload video files bigger then 10mb, up to 1000mb.</li>
			</ol>
			<h3>Create and Delete Subfolders</h3>
			<p>For all default folders (images/, imgMedia/, imgPDF/, imgGallery/) you can create unlimited Subfolders within which you can create Sub-Subfolders, etc.</p>
			<ol>
				<li>Click on one of the three functions for <strong>Uploading Files</strong>.</li>
				<li>Click then on the Tabs <strong>Create Subfolders</strong> or <strong>Delete Subfolders</strong> on the top of the page and follow the instruction.</li>
				<li>A subfolder must be empty in order to be deleted.</li>
			</ol>
			<h3>View and Delete Files</h3>
			<p>You can open any folder and subfolder and view its files either as a <strong>List of Names</strong> or as a <strong>List of Images</strong>, for image files.</p>
			<ol>
				<li>Click on any of the above three different function for uploading files.</li>
				<li>Click then on the Tabs <strong>View Folder Files</strong> or <strong>View Images</strong>, on the top of the page.</li>
				<li>You can in both cases <strong>mark</strong> multiple files and then <strong>delete</strong> them.</li>
				<li> You <strong>cannot</strong> change the name of a file.</li>
			</ol>
			<h3><strong>Default Folders for Files that you Link to in Texts</strong></h3>
			<p>In many tables, including Text Tables, when you add a New Record you can add links to files by entering in <strong>Input Fields</strong> the <strong>Subfolder Name</strong>, if any, and the <strong>File Name</strong>.</p>
			<ul>
				<li>Upload <strong>photos </strong>and<strong> images</strong> to the default folder <strong>images/</strong> or to one of its <strong>subfolders</strong>. </li>
				<ul>
					<li>When adding text, <strong>enter </strong><strong>only</strong> the subfolder name, if any, and the name of the photo in the input field: <strong>subfolder/image.jpg</strong> or <strong>image.jpg</strong>. </li>
					<li>Do never write the name of the <strong>default</strong> folder, as it is added automatically to the link.</li>
				</ul>
				<li>Upload <strong>multimedia</strong> (video and music) to the default folder <strong>imgMedia/</strong> or to one of its <strong>subfolders</strong>. </li>
				<ul>
					<li>When adding text, <strong>enter only</strong> the subfolder name, if any, and the media name in the input field: <strong>subfolder/video.pm4</strong> or <strong>video.pm4</strong>. </li>
					<li>Do never write the name of the <strong>default</strong> folder, as it is added automatically to the link.</li>
				</ul>
				<li>Upload <strong>PDF-Files</strong> to the default folder <strong>imgPDF/</strong> or to one of its <strong>subfolders</strong>. 
					<ul>
						<li>When adding text, <strong>enter only</strong> the subfolder name, if any, and the media name in the input field: <strong>subfolder/text.pdf</strong> or <strong>text.pdf</strong>. </li>
						<li>Do never write the name of the <strong>default</strong> folder, as it is added automatically to the link.</li>
					</ul>
				</li>
			</ul>
			<h3><strong>Default Folders for Additional Applications</strong></h3>
			<p>If your site contains some <strong>Additional Applications</strong>, they might have their own <strong>Default Folder</strong>. </p>
			<ul>
				<li>Upload photos for the <strong>Photo Gallery Application</strong> to the default folder <strong>imgGallery/</strong> or to one of its subfolders.</li>
				<li>Upload PDF files for the <strong>PDF-Archives Application</strong> to the default folder <strong>imgPFD/</strong> or to one of its subfolders.</li>
				<li>Upload Media files for the <strong>Multimedia Application</strong> to the default folder <strong>imgMedia/</strong> or to one of its subfolders.</li>
			</ul>
			<p>In all cases, you enter the link to files in <strong>Input Fields</strong> in the same way as with other files:</p>
			<ul>
				<li>By <strong>excluding</strong> the default folder.</li>
				<li>By <strong>enter only</strong> the subfolder name, if any, and the name of the file.</li>
			</ul>
			<h3><strong>Other Tools</strong></h3>
			<ol>
				<li><strong>Clear text</strong>: Clears formatted texts from all codes (converts them to TEXT format). Always use it when attaching formatted texts to avoid spoiling the website.</li>
				<li><strong>Save text formatting</strong>: Use it when you want to maintain the basic text formatting. The program clears all unnecessary codes.</li>
				<li><strong>View Statistics:</strong> You can view statistics about site visits and visits of other main pages of your site. Numbers are <strong>approximate</strong>, as the system cannot exclude visits from search engines. </li>
				<li><strong>Creating Sitemaps</strong>: For search engines, you can create <strong>Sitemaps</strong> for the central content of your site. You update them each time you add a new record. Click on the link for details.</li>
				<li><strong>Backup Database</strong>: You can <strong>Backup</strong> and then <strong>Restore</strong> the entire database or some of its tables or table groups. The new backup base is written on the old one.</li>
				<li><strong>Hash Passwords</strong>: Use this tool to hash passwords and get tokens that are needed when you manually add administrators and memberships in different table.</li>
			</ol>
		</div>
	</div>
</body>

</html>