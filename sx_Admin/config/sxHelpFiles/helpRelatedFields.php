<div class="sxHelp text" id="ConfigRelatedFields" style="display: none">
	<h3>1. Select a Table:</h3>
	<p>Select a Table from the Selection List above to view its Fields.</p>
	<h3>2. Required Fields:</h3>
	<p>Mark the corresponding box for the required fields. In editing/adding records, required fields are marked by an asterisk and
		controlled by a Java Script form controll.</p>
	<h3>3. Select Type of Relation to View Help</h3>
	<p>For the relations of type 1, 2, 3, 10, 11 and 12, you can use a Javascript pragram which opens automatically.
		The program defines and copies in the text field the correct SQL-statement on the basis of your selections.</p>
	<section>
		<div id="tabs">
			<a data-id="layer0" class="selected" href="javascript:void(0)">1</a>
			<a data-id="layer1" href="javascript:void(0)">2</a>
			<a data-id="layer2" href="javascript:void(0)">3</a>
			<a data-id="layer3" href="javascript:void(0)">4</a>
			<a data-id="layer4" href="javascript:void(0)">5</a>
			<a data-id="layer5" href="javascript:void(0)">6-9</a>
			<a data-id="layer6" href="javascript:void(0)">10</a>
			<a data-id="layer7" href="javascript:void(0)">11</a>
			<a data-id="layer8" href="javascript:void(0)">12</a>
			<a data-id="layer9" href="javascript:void(0)">13</a>
			<a data-id="layer10" href="javascript:void(0)">40</a>
			<a data-id="layer11" href="javascript:void(0)">50</a>
		</div>

		<div class="tabLayers" id="layer0" style="DISPLAY: block">
			<b>Type 1: Replace IDs by field names</b>
			<p>Creates a drop-down menu where the ID-field values of a Related Table are visually replaced by the corresponding field name values, so you can see
				names rather than numbers.</p>
			<p>Usually used to get the ID-number and the Name of categories and subcategories. The names are shown in the drop-down menu while ID-numbers are saved as values in the current table field.
			</p>
			<ul>
				<li>Case 1: SELECT FieldID, FieldName FROM TableName
					Enter only two fields in SELECTION. </li>
			</ul>
			<p>The first field must always be an ID-number (e.g. CategoryID). The second may be the name that defines the ID field (e.g. the CategoryName). The FieldName appears in Drop Down Selection while the FieldID is saved in the current record.
			</p>
			<p>You can add a WHERE statement of any valid content as well as an ORDER BY statement:
			</p>
			<ul>
				<li>Case 2: SELECT FieldID, FieldName FROM TableName WHERE FieldName1=True AND FieldName2=""FieldValue"" ORDER BY FieldName ASC
				</li>
			</ul>
			<p>For example, you want a drop-down menu with all ID-numbers and Titles of the texts in Texts Table that are published in the first page of the site and in the central column:
			</p>
			<ul>
				<li>SELECT TextID, Title FROM Texts WHERE Publish=True AND PublishPlace=""Center"" ORDER BY Title ASC
				</li>
			</ul>
			<p>Enter double quotes (""X"") for values - except for numbers and for the Boolean: True/False.
			</p>
		</div>
		<div class="tabLayers" id="layer1" style="DISPLAY: none;">
			<b>Type 2: ...and add a new record</b>
			<p>The same as <b>Type 1</b>, although you now have the possibility to add a new record in the Related Table. Two inputs appear, a drop-down menu, as in
				<b>Type 1</b> relation, where you can select a related value, and a text input, where you can add a new record in the Related Table. You can used this relation&nbsp; for more topical
				classifications of contents.</p>
			<p><b>SELECT FieldID, FieldName FROM TableName</b></p>
			<p>Enter only two fields in SELECT. The first field must always be an ID-number. The second must be the name that defines the ID-field (e.g. ThemeID and ThemeName). New entries are
				added to the related table as FieldNames, while the new FieldID is retrieved and saved in the current table. Don&#39;t use WHERE or ORDER BY statements. </p>
			<p><b>Example:</b> Suppose, for example, that the texts in your Texts Table are classified into two categories, news and articles (in the Related Table Categories). You want also to relate the
				texts (news and articles) to some current themes of actuality, which you save in a separate table (the Related Table Themes). When visitors chose a theme, they can get a list with all related contents (news
				and articles).</p>
			<p>You can of course first add a new theme in the Themes Table and than retrieve it by defining a <b>Type 1</b> relation. But current themes comes and go, so you need a faster method, like <b>
					Typ 2</b> relation:</p>
			<p><b>SELECT ThemeID, ThemeName FROM Themes</b></p>
		</div>
		<div class="tabLayers" id="layer2" style="DISPLAY: none">
			<b>Type 3: Get distinct values</b>
			<p>Get all distinct values from a field (usually the current field). Two inputs appear when you add or edit a record - one selection input, with all distinct values
				of the field that are already saved in the table, and one text input, where new values can be added.</p>
			<p>You can use it to get
				repeated values from the current field (names of authors, newspapers, publishers or other repeated entries) both for convenience and in order to avoid spelling mistakes when rewriting them.
			</p>
			<p><b>SELECT DISTINCT FieldName FROM TableName</b></p>
			<p>You usually enter here the name of the current field and table.</p>
		</div>
		<div class="tabLayers" id="layer3" style="DISPLAY: none">
			<b>Type 4: Get radial options</b>
			<p>Defines exclusive radial values for the current field. Is useful when the alternative values are exclusive and standardized but are more than two or other
				than the binary Yes/No distinction. When adding or editing records, the exclusive values appear as Option Buttons.</p>
			<p><b>Value1, Value2, Value3, etc</b></p>
			<p>Just write the exclusive values for the current field, separated by a comma (,). Don&#39;t put a comma after the last value. The defined values will appear as Option (Radial) Buttons.</p>
			<p>
				<b>Obs!</b> The first value will be automatically checked as default, so place there the most common value. </p>
		</div>
		<div class="tabLayers" id="layer4" style="DISPLAY: none">
			<b>Type 5: Set False as default value</b>
			<p>For the binary YES/NO distinction, you can choose NO as default value. Is used to facilitate the addition of new records when NO-value is more common.
			</p>
			<p>Just check the box in order to get NO as the default value. </p>
		</div>
		<div class="tabLayers" id="layer5" style="DISPLAY: none">
			<b>Types 6-9: Get updateable records</b>
			<p>In fact, we have here WHERE statements. The fields with the
				Boolean values YES/NO can be used for multiple updates in the very List of Records. Multiple updates can
				for example be
				used when you want to change the records that are published in the first page of the site all together, without opening them separately.
				The List of Records will include only the records WHERE the value of the selected field is:
			</p>
			<ul>
				<li><b>Type 6:</b> TRUE
				</li>
				<li><b>Type 7:</b> FALSE
				</li>
				<li><b>Type 8:</b> TRUE/FALSE </li>
			</ul>
			<p>Just mark the box for the field you want to be updateable from the List of Records. Multiple choices of Boolean fields is possible.</p>
			<p><b>Type 9</b> is for Boolean fields that you want to be updateable from the List of Records but not selected. This means that they are
				available for update only if you have selected another Boolena field as updateable.</p>
			<p>You can update these fields only in updateable mode, which is automatically available when you select an updatable field.</p>
		</div>
		<div class="tabLayers" id="layer6" style="DISPLAY: none">
			<b>Type 10: Get subcategories</b>
			<p>Use this type basically if you define <b>different</b> Subcategories for your main Categories. If you don&#39;t aim to use subcategories, don&#39;t use this type.
				If you use the same subcategories for all categories, you may instead use <b> &nbsp;Type 1</b> relation. However, type 10 relation is functional, even if you use the same subcategories. More over, you can use type 10 relation
				when you have some subcategories that are common to all categories and some that are different (you just don&#39;t make any choice of category for the common subcategories). Common subcategories
				must have the value 0, which is done automatically if you define a type 1 relation between categories and subcategories in the subcategories table.</p>
			<p>The function of this type is to create information for a Java Script. When adding or editing a record, the drop-down menu of subcategories is focused automatically and shows only the options that correspond to the
				selected category (and the common subcategories, if any).
				Please use the same order of fields as in the example bellow:</p>
			<p>
				<b>SELECT CatalogID, CatalogName, CategoryID FROM Catalogs ORDER BY CategoryID</b></p>
			<p>There must be three fields in the subcategory table (in the example, the subcategory table is named Catalogs). The first field is the ID-name of the subcategory, the second is the name of the
				subcategory and the third is the ID-name of the category, which is also use for the ordering of records. You must always use the ORDER BY, followed by the ID-name of the category.
		</div>
		<div class="tabLayers" id="layer7" style="DISPLAY: none">
			<b>Type 11. Add Field Values to Related Table:</b>
			<p>Add multiple sets of field values from This Table to a corresponding set of fields in a Related Table. Only unique values are added.</p>
			<p>This can be used, for example, when, in adding new text, you want to add the first name and the last name of
				the author
				to the the corresponding fields in Authors Table. Moreover, if you in the Text Table have fields to insert more then one author (in case of collective work), you can add their names too
				(that&#39;s the meaning of &quot;multiple sets&quot;). The authors&#39; name is added only if it is new.&nbsp;</p>
			<p>You can define in each statement only one related table. The set of fields in the related and in the current table must be of the same type.</p>
			<p>Separate every set of fields in the current table by a semicolon &quot;;&quot; You <b>must</b> use a semicolon in the end of the statement, even if there is only one set. You <b>must</b> also use the
				characters {}[]. Don&#39;t include the titles <b>Related table</b> and <b>Current Table</b>.
			</p>
			<ul>
				<li><b>Related table: {SELECT Field1,Field2 FROM TblName}</b>
				</li>
				<li><b>Current Table: [Field1A, Field2A; Field1B, Field2B; Field1C, Field2C;]</b>
				</li>
			</ul>
			<p><b>Example:</b> Suppose that in the Authors Table you have two fields for the name of authors with the field name: FirstName and LastName. In the Text Table an the other side, you have fields to insert up to
				three different authors (for collective work) with the following field names: </p>
			<div style="background-color: #f8f8f8; padding: 5px; border: 1px solid #aabbcc">
				FirsName, LastName<br>
				FirstName1, LastName1<br>
				FirstName2, LastName2
			</div>
			<p>When you add a new record in the Text Table, you can add unique author names in the Authors Table with the following statement:</p>
			<div style="background-color: #f8f8f8; padding: 5px; border: 1px solid #aabbcc">
				{SELECT FirstName,LastName FROM Authors} [FirstName, LastName; FirstName1, LastName1; FirstName2, LastName2]
			</div>
		</div>

		<div class="tabLayers" id="layer8" style="DISPLAY: none">
			<b>Typy 12. Updates values in Related Table:</b>
			<p>Update a field value in a related Table with a field value from this Table. Multiple updates
				with multiple tables are possible in the same statement. This is used, for example, when you
				want to update the date field in a Related Table with the date inserted in the date field of the
				Current Table. In that way, you keep the dates synchronized. </p>

			<p>You <b>must</b> separate every update by a semicolon &quot;;&quot; and use a semicolon even in the last
				set, or even when there is only one set. You <b>must</b> also use the character {}. </p>

			<p><b>{UPDATE RelatedTableName SET FieldRelatedTbl = FieldThisTbl WHERE FieldIDRelatedTbl
					(FieldIDNameRelatedTbl); UPDATE etc;}</b> </p>

			<p>The &quot;FieldIDRelatedTbl&quot; is the ID-number in the Related Table (a Category, for example),
				which is supposed to be available in the Current Table as selection. However, if you define a
				relation of <b>Type 2</b> above, a new record is inserted in the Related Table with a value that
				is added in a <i><b>defined field</b></i>. The ID-number of this new record is yet unknown, but
				we know the name of the field. This is usually the field name that defines the records ID-number
				(e.g. CategoryName defines CategoryID, ThemeName defines ThemeID, etc.). The &quot;FieldNameRelatedTbl&quot;
				refers to this field name and is there to replace the ID number. </p>

			<p>So, whenever the Related Table to be updated is also defined as a <b>Type 2</b> relation
				(where you can add a new value for which the ID-number is unknown) you <b>must</b> use the field
				name in parenthesis (). Otherwise, you can leave it empty, but you <b>must</b> keep the
				parenthesis (). </p>

			<p><b>Example:</b> Besides other classifications, the texts in your site may also be classified
				according to some Current Themes (actualities). You can add such themes in the Themes Table from
				the Text Table, by defining a <b>Type 2</b> relation (see corresponding Tab). Suppose now that
				you want these themes to appear in your site in descending order, so most recent themes appear
				first. You have then to update the date field for each theme when a mew text is related to it.
				This is down be <b>Type 12</b> relation. You have the following field names (relevant for our
				axample):</p>

			<div style="background-color: #f8f8f8; padding: 5px; border: 1px solid #aabbcc">
				Related Table Name: Themes (ThemeID, ThemeName, LastRecordDate)<br>
				Current Table Name: Texts (PublishedDate)
			</div>
			<p>You update the date in the Related Table with the following statement (which also includes a
				second update to another Related Table):</p>
			<div style="background-color: #f8f8f8; padding: 5px; border: 1px solid #aabbcc">
				{UPDATE Themes SET LastRecordDate = PublishedDate WHERE ThemeID (ThemeName); UPDATE Folders
				SET LastRecordDate = PublishedDate WHERE FolderID (FolderName)}
			</div>
		</div>
		<div class="tabLayers" id="layer9" style="DISPLAY: none">
			<b>Type 13: Select a numeric or currency field to be updateable from list.</b>
			<p>Is used when you have numeric or currency fields in your table. This makes it possible to change the
				value of multiple records simultaineously form the list of records. Useful, for example, for
				sorting fields and for fields defining the price of products.
			</p>
			<p>This selection is available only for fields of type currency or number of type 2 (small integer) and type 4 (single) and 5 (double) - it is not
				available for long integers (type 3).</p>
			<p>You can update these fields only in updateable mode, which is automatically available when you select an updatable field.</p>
		</div>
		<div class="tabLayers" id="layer10" style="DISPLAY: none">
			<b>Type 40: Get checkbox options</b>
			<p>In contrast to radia values (type 4) you define here mutually non-exclusive checkbox
				values for the current field. It is useful when the alternative values are non-exclusive and standardized.
				When adding or editing records, the non-exclusive values appear as checkbox Buttons.</p>
			<p><b>Value1, Value2, Value3, etc</b></p>
			<p>Just write the non-exclusive values for the current field, separated by a comma (,).
				Don&#39;t put a comma after the last value.</p>
		</div>
		<div class="tabLayers" id="layer11" style="DISPLAY: none">
			<b>Type 50: Exclude not Used Fields</b>
			<p>If you have some fields in a
				table that you don&#39;t want to use for the current website, but not either
				want to delete, you can select the &quot;relation&quot; 50. These fields will then
				not appear anywhere during the content management - neither in the List
				of Records, nor in the adding or editing of records. </p>
			<p>To avoid errors you must, in the Access database, define the
				following default properties for the excluded fields: Set Required to
				No and Allow Zero Length to Yes. You can also set zero (0) as the
				default value for excluded numeric fields.
		</div>
		<div class="alignRight">
			<input class="button jqHelpButton" data-id="ConfigRelatedFields" type="button" value="Close">
		</div>

	</section>
</div>