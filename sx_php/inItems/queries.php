<?php
function sx_get_ItemsList()
{
    $conn = dbconn();
    $sql = "SELECT
        ItemID,
        ItemTitle" . str_LangNr . " AS ItemTitle,
        MetaMedia,
        MetaNotes
    FROM items
        WHERE Hidden = 0 
    ORDER BY Sorting DESC, ItemID ASC ";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Join all 3 related tables
 */
function sx_get_ItemElementsByItemID($id)
{
    $conn = dbconn();
    $lang_And = str_LanguageAnd;
    $lang_Mark = str_LangNr;
    $sql = "SELECT
        el.ElementID,
        el.SectionID,
        el.Title,
        el.TitlePlace,
        el.RowNumber,
        el.LinkToTable,
        el.FirstLinkPathID,
        el.FirstLinkTitle,
        el.SecondLinkPathID,
        el.SecondLinkTitle,
        el.MediaURL,
        el.ShowSliderOrGallery,
        el.ElementNotes,
        s.SectionTitle{$lang_Mark} AS SectionTitle,
        s.SectionHeaderPlace,
        s.SectionNotes{$lang_Mark} AS SectionNotes,
        t.SectionBackground,
        t.SectionGradientPath,
        t.HeaderBackground,
        t.HeaderTitleColor,
        t.HeaderNotesColor,
        t.ContentBackground,
        t.ContenGradientPath,
        t.RowBackground,
        t.ElementBackground,
        t.ElementTitleColor, 
        t.ElementNotesBackground,
        t.ElementNotesColor,
        t.ElementBorderWidth,
        t.ElementBorderColor,
        t.ElementShadow,
        t.ElementHoverShadow,
        t.ElementHoverColor,
        t.ElementImageShadow,
        t.ImageSmallHeight,
        t.ElementImageRadius
    FROM item_sections AS s
        INNER JOIN item_elements AS el
            ON el.SectionID = s.SectionID
        INNER JOIN templates AS t
            ON s.TemplateID = t.TemplateID
    WHERE (el.ItemID = ?)
        AND el.Publish = 1 {$lang_And}
        AND s.Hidden = 0
    ORDER BY s.Sorting DESC, SectionID ASC, el.RowNumber ASC, el.Sorting DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
