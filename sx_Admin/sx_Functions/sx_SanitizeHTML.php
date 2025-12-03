<?php

/*
@DESCRIPT: Removes all Tags from a given string.
   Same as reg.Expression above, but does not uses reg.Expression
*/

function sx_RemoveAllHTML($strText)
{
    $nPos1=strpos($strText, "<", 0);
    while ($nPos1 > 0) {
        $nPos2=_instr($nPos1 + 1, $strText, ">", 0);
        if ($nPos2 > 0) {
            $strText=substr($strText, 0, $nPos1-1).substr($strText, $nPos2);
        } else {
            break;
        }
        $nPos1=strpos($strText, "<", 0);
    }
    return $strText;
}

/*
@DESCRIPT: Remove All HTML Tags Except those defined in a list.
   - For the tags contained in BLOCKTAGLIST constants this function will remove 
       everything between the start and the end tag.
   - Replaces the next function RemoveSelectedHTML()
@PARAM:  strHTML [string]: the HTML text to be cleaned
@PARAM:  preservedTags [string]: Additional Tags to be preserved separated by a (;)
   - Default preserved tags: ";BR;B;EM;I;UL;OL;LI;P;STRONG;TABLE;TD;TH;TR;"
@PARAM:  appendTags [int]: A boolean value (0 or 1).
   - True, appands the additional tags to the default ones
   - False, replaces the default tags with the additional ones
   - Adding a non-existing Tag, and setting appendTags to False, will remove all HTML Tags
@RETURN: [string] The cleaned texte
   Obs! REPLACE IN THE FUNCTION "< /" WITH "</"
*/

function sx_RemoveNotPreservedHTML($strHTML, $preservedTags, $appendTags)
{
    $DEFAULTTAGS=";BR;B;EM;I;IMG,A,UL;OL;LI;P;STRONG;TABLE;TD;TH;TR;";
    $BLOCKTAGLIST=";APPLET;EMBED;FRAMESET;HEAD;NOFRAMES;NOSCRIPT;OBJECT;SCRIPT;STYLE;";
    if (!is_numeric($appendTags)) {
        $appendTags=-100;
    } else {
        $appendTags=intval($appendTags);
    }
    if (!empty($preservedTags) && $appendTags > -100) {
        if (($appendTags) == true) {
            $DEFAULTTAGS.=$preservedTags.";";
        } else {
            $DEFAULTTAGS=";".$preservedTags.";";
        }
    }
    $strHTML.="";
    $nPos1=strpos($strHTML, "<", 0);
    while ($nPos1 > 0) {
        $iNext=1;
        $nPos2=strpos($strHTML, ">", $nPos1 + 1);
        $nPos3=strpos($strHTML, " ", $nPos1 + 1);
        if (($nPos3 > $nPos2 || $nPos3 == 0)) {
            $nPos3=$nPos2;
        }
        if ($nPos2 > 0) {
            $nPos3-=($nPos1 + 1);
            $strTemp=substr($strHTML, $nPos1, $nPos3);
            $bSearchForBlock=true;
            if (substr($strTemp, 0, 1) == "/") {
                $bSearchForBlock=false;
            }
            $strTemp=str_replace("/", "", $strTemp);
            if (strpos($DEFAULTTAGS, ";".$strTemp.";", 1) == 0) {
                if ($bSearchForBlock) {
                    if (strpos($BLOCKTAGLIST, ";".$strTemp.";", 1) > 0) {
                        $nPos2=strlen($strHTML);
                        $nPos3=strpos($strHTML, "</".$strTemp, $nPos1 + 1);
                        if ($nPos3 > 0) {
                            $nPos3=strpos($strHTML, ">", $nPos3 + 1);
                        }
                        if ($nPos3 > 0) {
                            $nPos2=$nPos3;
                        }
                    }
                }
                $strHTML=substr($strHTML, 0, $nPos1-1).substr($strHTML, $nPos2);
                $iNext=0;
            }
        } else {
            break;
        }
        $nPos1=strpos($strHTML, "<", $nPos1 + $iNext);
    }
    return $strHTML;
}

/*
  Remove Attributes from Selected Tags
  Use the Function RemoveSelectedHTML() to remove unwanted tags
	@PARAM: strHTML: string with HTML text
	@PARAM: tagList: string with one or more tags wich might include attributes to be removed
  Separate more than one Tags by (;)
  if empty, the following default list will be used: "B;BR;P;EM;OL;SPAN;TABLE;UL;LI"
*/

function sx_RemoveTagAttributes($strHTML, $tagList)
{
    $strResult = $strHTML;
    if (empty($tagList) || $tagList == "") {
        $tagList="B;BR;EM;I;OL;P;STRONG;SPAN;TABLE;TD;TH;UL;LI";
    }
    if (strpos($tagList, ";", 0) == 0) {
        $tagList.=";";
    }
    $arrTagList=explode(";", $tagList);
    for ($x=0; $x<=count($arrTagList)-1; $x++) {
        $strTag=trim($arrTagList[$x]);
        if ($strTag != "") {
            if (strpos($strResult, "<".$strTag." ", 1) > 0) {
                $arrTemp=explode("<".$strTag." ", $strResult);
                $iRows=count($arrTemp)-1;
                if ($iRows > 0) {
                    for ($i=1; $i<=$iRows; $i++) {
                        $iStart=_instr(1, $arrTemp[$i], ">", 1);
                        $sRight=substr($arrTemp[$i], -strlen($arrTemp[$i])-$iStart);
                        $arrTemp[$i]=">".$sRight;
                    }
                    $strResult=implode("<".strtolower($strTag), $arrTemp);
                }
            }
        }
    }
    return $strResult;
}
