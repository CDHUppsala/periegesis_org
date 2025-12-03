function openCenteredWindow(strURL, winName, w, h) {
    if (w == 0 || w == '') {
        w = (screen.width - 160);
        var winLeft = 0;
    } else {
        var winLeft = Math.round((screen.width - w) / 2);
    }
    if (h == 0 || h == '') {
        h = (screen.height - 165);
        var winTop = 0;
    } else {
        var winTop = Math.round(((screen.height - 200) - h) / 2);
    }
    winProperties = 'height=' + h + ',width=' + w + ',top=' + winTop + ',left=' + winLeft + ',location=1,status=0,toolbar=yes,menubar=yes,scrollbars=yes,resizable=yes';
    window.open(strURL, winName, winProperties);
    return false;
};

/**
 * Changes the color of a text counter box when max length is exceeded.
 * onFocus
 * @param {*} frm The Form Name
 * @param {*} fld The Field Name (textarea)
 * @param {*} i Max text length allowed
 */
function countEntries(frm, fld, i) {
    var f = document.forms[frm];
    var l = f.elements[fld].value.length;
    f.entered.value = l;
    f.entered.style.backgroundColor = (l >= i) ? '#ff0000' : '#ffffff';
    setTimeout("countEntries('" + frm + "','" + fld + "'," + i + ")", 0);
};


function sx_getCSSRootVariables() {
    /**
     * The :root must be in the first Stylesheet as the first Rule
     */
    var declaration = document.styleSheets[0].cssRules[0];
    var allVar = declaration.style.cssText.split(";");

    var result = {}
    for (var i = 0; i < allVar.length; i++) {
        var a = allVar[i].split(':');
        if (a[0] !== "")
            result[a[0].trim()] = a[1].trim();
    }
    return result;
}

/**
 * Validate Forms
 */

// For Surveys
var radioSelection = "";

function radio() {
    if (radioSelection == "") {
        alert(lngYouMustMakeAChoice);
        return false;
    } else {
        return true;
    }
};

//	Blog and Forum - Validate comments/new article
function validateForum(intMax) {
    var iMax = intMax;
    if (iMax == null || iMax == "") {
        iMax = 2000;
    };
    x = document.forumArticles;
    strSend0 = x.Email.value;
    strSend1 = strSend0.indexOf("@");
    strSend2 = strSend0.indexOf(".");
    strNameF = x.FirstName.value;
    strNameL = x.LastName.value;
    strTitle = x.Title.value;
    strMsg = x.TextBody.value;
    submitOK = true;
    strAlert = "";
    if (strNameF.length < 1 || strNameL.length < 1 || strSend0.length < 1 || strTitle.length < 5 || strMsg.length < 5) {
        strAlert = lngCompleteAllAsteriskFields + "\n";
        submitOK = false;
    }
    if ((strSend1 == -1 || strSend2 == -1 || strSend0.length < 8) && strAlert == '') {
        strAlert = strAlert + lngWriteCorrectEmail + "\n";
        submitOK = false;
    }
    if ((strMsg.length > iMax) && strAlert == '') {
        strAlert = strAlert + lngMsgContains + " " + strMsg.length + " " + lngOfMaxCharactersAllowed + " " + iMax;
        submitOK = false;
    }
    if (submitOK == false) {
        alert(strAlert);
        return false;
    }
}

function sxPreloadSliderImages(imageUrls) {
    if (Array.isArray(imageUrls)) {
        imageUrls.forEach(function (url) {
            var newImg = new Image();
            newImg.src = url;
        });
    }
}

/**
 * ==============================================
 * From Shopping
 * ==============================================
 */


function openPopWin(theURL,winName,W,H) {
	var intSW=screen.width;
	var intSH=screen.height;
	var winTop=(intSH-H)/2;
	if(H==0){
		winTop=0;
		H=intSH;
	}
	var winLeft=(intSW-W)/2;
	if(W==0){
		winLeft=0;
		W=intSW;
	}
	features = 'height='+H+',width='+W+',top='+winTop+',left='+winLeft+',toolbar=0,location=0,status=0,menubar=0,scrollbars=0,resizable=0'
	window.open(theURL,winName,features);
}

var checkboxSelection = "";
function checkbox()
{
	if (checkboxSelection == "")
	{
		alert("Πρέπει να σημειώσεις τουλάχιστον ένα κουτάκι.");
		return false;
	}else {
		return true;
  	}
}

function formSubmit(myForm) {
	document.getElementById(myForm).submit()
}

//SX enables and disables submit form
function enableSubmit(conditionID,effectID) {
	if (document.getElementById(conditionID).checked==true) {
		document.getElementById(effectID).disabled=false;
		document.getElementById(effectID).title='';
		document.getElementById(effectID).className='';
	}else{
		document.getElementById(effectID).disabled=true;
		document.getElementById(effectID).className='disabled';
	}
}

//SX enables and disables two groups av radio inputs
function enable_disable_radio(first,second) {
	var i = 0;
    document.getElementById(first).checked=true;
	while (document.getElementById(first+i) != null)
	{
		document.getElementById(first+i).disabled=false;
		i++;
	}
	i = 0;
    document.getElementById(second).checked=false;
	while (document.getElementById(second+i) != null)
	{
		document.getElementById(second+i).disabled=true;
		i++;
	}
}
