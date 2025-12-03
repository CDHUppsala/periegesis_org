Pagination of text initially in the First Page and subsequenty according to 
different, primary and additional, classifications of texts:
-   By group, category or subcategory
-   By author, theme or date

The file "archives_TextsPagingQuery.php" contains queries and functions for 
pagination of texts according to the requested classification.

It is included in the files:
-   inText_Includes/includes_FirstPageMain.php and 
-   inText_Includes/includes_ArticlesMain.php

It is called:
-   by the file inTexts/default.php (for the First Page pagination and for pagination by the requested classification)
-   by the file archives_TextsPagingMenu.php (for the creation of the pagination menu, according to the requested classification).
    This file is included in the file inText_Includes/includes_ArticlesAside.php
