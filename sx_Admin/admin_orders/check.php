<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SXCMS List of Records</title>
    <link rel="stylesheet" href="http://localhost:8000/dbAdmin/css/sxCMS.css?v=2025-02-28">
    <script src="http://localhost:8000/dbAdmin/js/jsFunctions.js?v=2025-02-28"></script>
    <script src="../js/jq/jquery.min.js"></script>
    <script src="http://localhost:8000/dbAdmin/js/jqFunctions.js?v=2025-02-28"></script>
    <script>
        jQuery(function($) {
            $("#Export").click(function() {
                $(this).closest("form")
                    .attr("action", "list_exports.php")
                    .attr("target", "_blank");
            });

        });
    </script>
</head>

<body class="body">


    <!--
===========================================================
	Main Header Links
===========================================================
-->
    <header id="header">
        <h2>Table: SHOP_ORDERS<br>List of records: <span>10</span> Updateable Mode </h2>
        <div>
            <a class="button" href="list.php?searchMode=yes">Search Mode</a>
            <a class="button" id="ShowHideImages" href="list.php?ShowImages=Yes">Show Images</a>
        </div>
        <div>
            <button class="button jqHelpButton" data-id="helpSearch">HELP</button>
        </div>

    </header>

    <!--
===========================================================
	Page Navigation
===========================================================
-->

    <div id="navBG">

        <form name="SelectClasses" id="jqSelectClasses">
        </form>
        <div class="row">
            <div>
                <form class="row flex_justify_start" method="post" name="searchForm" action="list.php?searchForm=yes">
                    <label>Page:<br>
                        <select size="1" name="page">
                            <option selected value="1">1</option>
                        </select>
                    </label>
                    <label>Size:<br>
                        <select size="1" name="PageSize">
                            <option valu="50" selected>50</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                            <option value="500">500</option>
                        </select>
                    </label>
                    <label>Date:<br>
                        <select name="SearchDate">
                            <option value="0">All Dates</option>
                            <option value="2035">Coming Dates</option>
                            <option value="1">Last Month</option>
                            <option value="3">Last Quarter</option>
                            <option value="6">Last 6 Months</option>
                            <option value="12">Last Year</option>
                            <option value="2025">Year 2025</option>
                            <option value="2024">Year 2024</option>
                        </select>
                    </label>
                    <label>Search:<br>
                        <input title="Write the ID number of a record or letters for seach in Text Fields" type="text" placeholder="ID Number or Title" name="SearchText" size="19">
                    </label>
                    <label><input class="button" type="submit" name="go" value="Search"></label>
                    <a class="button" href="list.php?RequestTable=shop_orders">Clear All</a>
                </form>
            </div>
        </div>
    </div>
    <div id="navPagingTop">
        <div class="row flex_align_center">
            <div class="row flex_justify_start">
                <h3><a href="add.php">Add a New Record</a></h3>
                <h3><a title="First Page" href="list.php?page=1">&#x276E&#x276E&#x276E&#x276E</a> | &#x276E&#x276E Page <span>1</span> of Total <span>1</span> &#x276F&#x276F | <a title="Last Page" href="list.php?page=1">&#x276F&#x276F&#x276F&#x276F</a></h3>
                <h3>Multiple Records Update</h3>
            </div>
        </div>
    </div>

    <section class="list_table">
        <form method="POST" name="multipleUpdate" action="list.php?strMultipleUpdate=yes">
            <input type="hidden" name="PKName" value="OrderID">
            <table id="TableList" class="jqTableList">
                <thead>
                    <tr>
                        <th colspan="2">
                            <div title="Order results by this field"><img class="sx_svg" src="../images/sx_svg_blue/sx_up_down.svg"></div><span>&#x25B2;</span>
                            <a title="Order the Table by this field" href="list.php?orderby=OrderID">Order ID</a>
                        </th>
                        <th>
                            <div title="Order results by this field"><img class="sx_svg" src="../images/sx_svg_blue/sx_up_down.svg"></div> <a title="Order the Table by this field" href="list.php?orderby=OrderDate">Order Date</a>
                        </th>
                        <th>
                            <div title="Order results by this field"><img class="sx_svg" src="../images/sx_svg_blue/sx_up_down.svg"></div> <a title="Order the Table by this field" href="list.php?orderby=CustomerID">Customer ID</a>
                        </th>
                        <th>
                            <div title="Order results by this field"><img class="sx_svg" src="../images/sx_svg_blue/sx_up_down.svg"></div> <a title="Order the Table by this field" href="list.php?orderby=DeliveryAddressID">Delivery Address ID</a>
                        </th>
                        <th>
                            <div title="Order results by this field"><img class="sx_svg" src="../images/sx_svg_blue/sx_up_down.svg"></div> <a title="Order the Table by this field" href="list.php?orderby=Customer">Customer</a>
                        </th>
                        <th>
                            <div title="Order results by this field"><img class="sx_svg" src="../images/sx_svg_blue/sx_up_down.svg"></div> <a title="Order the Table by this field" href="list.php?orderby=Email">Email</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=ShipDate">Ship Date</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=ShipPromiseDate">Ship Promise Date</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=Total">Total</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=PaidAmount">Paid Amount</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=SuccessfulPayment">Successful Payment</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=InProcess">In Process</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=Shipped">Shipped</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=Completed">Completed</a>
                        </th>
                        <th> <a title="Order the Table by this field" href="list.php?orderby=Cancelled">Cancelled</a>
                        </th>
                    </tr>
                    <thead>http://localhost:7000/dbAdmin/admin_orders/list.php?searchFieldName=OrderDate&searchFieldValue=2024-12-23
                    <tbody>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=10">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=10">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=10">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="10">
                            </td>
                            <td>10</td>
                            <td>2025-02-27 03:52:50</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="279" id="Total_0" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="79" id="PaidAmount_0" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box10_10" value="Yes" onchange="sxChangeRadioValue(this,'radio10_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio10_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box10_11" value="Yes" onchange="sxChangeRadioValue(this,'radio10_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio10_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box10_12" value="Yes" onchange="sxChangeRadioValue(this,'radio10_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio10_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box10_13" value="Yes" onchange="sxChangeRadioValue(this,'radio10_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio10_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box10_14" value="Yes" onchange="sxChangeRadioValue(this,'radio10_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio10_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=9">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=9">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=9">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="9">
                            </td>
                            <td>9</td>
                            <td>2025-02-27 03:31:37</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="79.5" id="Total_1" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="60" id="PaidAmount_1" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box9_10" value="Yes" onchange="sxChangeRadioValue(this,'radio9_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio9_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box9_11" value="Yes" onchange="sxChangeRadioValue(this,'radio9_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio9_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box9_12" value="Yes" onchange="sxChangeRadioValue(this,'radio9_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio9_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box9_13" value="Yes" onchange="sxChangeRadioValue(this,'radio9_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio9_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box9_14" value="Yes" onchange="sxChangeRadioValue(this,'radio9_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio9_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=8">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=8">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=8">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="8">
                            </td>
                            <td>8</td>
                            <td>2025-02-26 08:49:44</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="114" id="Total_2" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_2" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box8_10" value="Yes" onchange="sxChangeRadioValue(this,'radio8_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio8_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box8_11" value="Yes" onchange="sxChangeRadioValue(this,'radio8_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio8_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box8_12" value="Yes" onchange="sxChangeRadioValue(this,'radio8_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio8_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box8_13" value="Yes" onchange="sxChangeRadioValue(this,'radio8_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio8_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box8_14" value="Yes" onchange="sxChangeRadioValue(this,'radio8_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio8_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=7">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=7">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=7">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="7">
                            </td>
                            <td>7</td>
                            <td>2025-02-26 00:54:58</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="204" id="Total_3" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_3" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box7_10" value="Yes" onchange="sxChangeRadioValue(this,'radio7_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio7_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box7_11" value="Yes" onchange="sxChangeRadioValue(this,'radio7_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio7_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box7_12" value="Yes" onchange="sxChangeRadioValue(this,'radio7_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio7_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box7_13" value="Yes" onchange="sxChangeRadioValue(this,'radio7_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio7_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box7_14" value="Yes" onchange="sxChangeRadioValue(this,'radio7_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio7_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=6">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=6">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=6">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="6">
                            </td>
                            <td>6</td>
                            <td>2025-02-23 23:40:15</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=2">2</td>
                            <td>Σταγειρήτης Σωκράτης<br>Θεσσαλονίκη<br>2310123456</td>
                            <td><a target="_blank" href="mailto:stagiritis@gmail.com"><span title="stagiritis@gmail.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="32.5" id="Total_4" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_4" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box6_10" value="Yes" onchange="sxChangeRadioValue(this,'radio6_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio6_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box6_11" value="Yes" onchange="sxChangeRadioValue(this,'radio6_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio6_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box6_12" value="Yes" onchange="sxChangeRadioValue(this,'radio6_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio6_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box6_13" value="Yes" onchange="sxChangeRadioValue(this,'radio6_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio6_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box6_14" value="Yes" onchange="sxChangeRadioValue(this,'radio6_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio6_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=5">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=5">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=5">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="5">
                            </td>
                            <td>5</td>
                            <td>2024-12-23 17:40:51</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=4">4</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotistheodoridis@outlook.com"><span title="fotistheodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="256.5" id="Total_5" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_5" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box5_10" value="Yes" onchange="sxChangeRadioValue(this,'radio5_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio5_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box5_11" value="Yes" onchange="sxChangeRadioValue(this,'radio5_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio5_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box5_12" value="Yes" onchange="sxChangeRadioValue(this,'radio5_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio5_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box5_13" value="Yes" onchange="sxChangeRadioValue(this,'radio5_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio5_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box5_14" value="Yes" onchange="sxChangeRadioValue(this,'radio5_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio5_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=4">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=4">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=4">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="4">
                            </td>
                            <td>4</td>
                            <td>2024-12-22 17:09:38</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=3">3</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="136.5" id="Total_6" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_6" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box4_10" value="Yes" onchange="sxChangeRadioValue(this,'radio4_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio4_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box4_11" value="Yes" onchange="sxChangeRadioValue(this,'radio4_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio4_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box4_12" value="Yes" onchange="sxChangeRadioValue(this,'radio4_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio4_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box4_13" value="Yes" onchange="sxChangeRadioValue(this,'radio4_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio4_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box4_14" value="Yes" onchange="sxChangeRadioValue(this,'radio4_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio4_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=3">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=3">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=3">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="3">
                            </td>
                            <td>3</td>
                            <td>2024-11-11 13:31:30</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=0">0</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="301.65" id="Total_7" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_7" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box3_10" value="Yes" onchange="sxChangeRadioValue(this,'radio3_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio3_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box3_11" value="Yes" onchange="sxChangeRadioValue(this,'radio3_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio3_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box3_12" value="Yes" onchange="sxChangeRadioValue(this,'radio3_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio3_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box3_13" value="Yes" onchange="sxChangeRadioValue(this,'radio3_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio3_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box3_14" value="Yes" onchange="sxChangeRadioValue(this,'radio3_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio3_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=2">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=2">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=2">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="2">
                            </td>
                            <td>2</td>
                            <td>2024-11-11 09:21:45</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=1">1</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=2">2</td>
                            <td>Σταγειρήτης Σωκράτης<br>Θεσσαλονίκη<br>2310123456</td>
                            <td><a target="_blank" href="mailto:stagiritis@gmail.com"><span title="stagiritis@gmail.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="327.45" id="Total_8" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="0" id="PaidAmount_8" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box2_10" value="Yes" onchange="sxChangeRadioValue(this,'radio2_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio2_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box2_11" value="Yes" onchange="sxChangeRadioValue(this,'radio2_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio2_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box2_12" value="Yes" onchange="sxChangeRadioValue(this,'radio2_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio2_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box2_13" value="Yes" onchange="sxChangeRadioValue(this,'radio2_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio2_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box2_14" value="Yes" onchange="sxChangeRadioValue(this,'radio2_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio2_14">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a title="Newsletters" target="_blank" href="../email/default.php?tbl=shop_orders&cid=1">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_mail_open.svg" height="24"></a>
                                <a title="View Record" href="index.php?viewID=OrderID&strIDValue=1">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_search.svg" height="24"></a>
                                <a title="Edit Record" href="index.php?oid=1">
                                    <img class="sx_svg_bg" src="../images/sx_svg/sx_pencil.svg" height="24"></a>
                                <input type="hidden" name="PKValue[]" value="1">
                            </td>
                            <td>1</td>
                            <td>2024-11-10 02:05:09</td>
                            <td><a title="Vie Orders from this Customer" href="index.php?cid=2">2</td>
                            <td><a title="Show Records from this Category Only" href="list.php?searchFieldName=DeliveryAddressID&searchFieldValue=1">1</td>
                            <td>Theodoridis Fotis<br>Stockholm<br>0704382137</td>
                            <td><a target="_blank" href="mailto:fotis.theodoridis@outlook.com"><span title="fotis.theodoridis@outlook.com">Mail</span></a></td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipDate[]">
                                </div>
                            </td>
                            <td>
                                <div class="row flex_align_center">
                                    <a title="Show Records from this Date Only" href="list.php?searchFieldName=ShipPromiseDate&searchFieldValue=">[i]</a>
                                    <input size="18" type="date" value="" name="ShipPromiseDate[]">
                                </div>
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="334.45" id="Total_9" name="Total[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td>
                                <input style="text-align: right" size="4" type="text" value="200" id="PaidAmount_9" name="PaidAmount[]" onChange="IsAllNumeric(this)">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box1_10" value="Yes" onchange="sxChangeRadioValue(this,'radio1_10')">
                                <input type="hidden" value="No" checked name="SuccessfulPayment[]" id="radio1_10">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box1_11" value="Yes" onchange="sxChangeRadioValue(this,'radio1_11')">
                                <input type="hidden" value="No" checked name="InProcess[]" id="radio1_11">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box1_12" value="Yes" onchange="sxChangeRadioValue(this,'radio1_12')">
                                <input type="hidden" value="No" checked name="Shipped[]" id="radio1_12">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box1_13" value="Yes" onchange="sxChangeRadioValue(this,'radio1_13')">
                                <input type="hidden" value="No" checked name="Completed[]" id="radio1_13">
                            </td>
                            <td class="bgUpdateables">
                                <input type="checkbox" name="box1_14" value="Yes" onchange="sxChangeRadioValue(this,'radio1_14')">
                                <input type="hidden" value="No" checked name="Cancelled[]" id="radio1_14">
                            </td>
                        </tr>
                    </tbody>
            </table>
            <p id="navPagingBG"><a title="First Page" href="list.php?page=1">&#x276E&#x276E&#x276E&#x276E</a> | &#x276E&#x276E | &#x276F&#x276F | <a title="Last Page" href="list.php?page=1">&#x276F&#x276F&#x276F&#x276F</a></p>
            <p>
                <input class="button" type="submit" value="Update" name="UpdateList">
            </p>
            <input type="hidden" name="UsedUpdateableFields" value="ShipDate,ShipPromiseDate,Total,PaidAmount,SuccessfulPayment,InProcess,Shipped,Completed,Cancelled">
            <input type="hidden" name="UsedUpdateableFieldTypes" value="DATE,DATE,DOUBLE,DOUBLE,TINY,TINY,TINY,TINY,TINY">
        </form>
    </section>
    <div class="sxHelp text" id="helpSearch" style="display: none">
        <h3>Search Mode and Update Mode</h3>
        <ul>
            <li>Choose, if it is available on the Left Top of the page, between <b>Search Mode</b> and <b>Update Mode</b>.
                From the update mode you can update multiple records simultaneously.</li>
            <li>Your choice is maintained in sessions until you change it or reload
                the page from the Left Menu.</li>
        </ul>
        <h3>Search</h3>
        <ul>
            <li>Search is pursued only on visible text fields.</li>
            <li>If you enter a <strong>number</strong>, you will get the record with
                the ID equal to that
                number</li>
            <li>Your choice is maintained in sessions until you change it or reload
                the page from the Left Menu.</li>
        </ul>
        <h3>Classification and Ordering of Records</h3>
        <ul>
            <li>Click on a Field or Column Name (on the Table Headers) to <strong>Sort
                    Records</strong> according to that field.
                <ul>
                    <li>Click on the same Field Name to alternate between <strong>descending</strong> and
                        <strong>ascending</strong> order.
                    </li>
                    <li>The selected ordering is maintained as you are paging through
                        the records.</li>
                </ul>
            </li>
            <li>If <strong>Categories</strong> and <strong>Subcategories</strong> are available (<b>links</b> within the table),
                you can click on one of them to get all its records.
                <ul>
                    <li>You can sort these records in descending and ascending order by
                        clicking on the other Field Names.</li>
                    <li>Clicking on the Field Name of the selected Category or
                        Subcategory will end the classification and show all records again.</li>
                </ul>
            </li>
            <li>All sorting and classification selections are saved in sessions until you change them
                or reload the page from the Left Menu.</li>
        </ul>
        <h3>View Images</h3>
        <ul>
            <li>If records contain images, you can click on them to open them in a new window.</li>
            <li>You can also click the tab <b>Show Images</b>, on the top of the page, to se thumbnails of all images.</li>
        </ul>
        <div class="alignRight">
            <input class="button jqHelpButton" data-id="helpSearch" type="button" value="Close">
        </div>

    </div>
    <div id="imgPreview"><img src=""></div>
    <script>
        $sx('#ShowHideImages').css('display', 'none');
    </script>

    <script>
        jQuery(function($) {
            $("input[id^='PaidAmount_']").each(function() {
                var id = $(this).attr("id");
                var suffix = id.split("_")[1];
                var totalField = $("#Total_" + suffix);

                $(this).parent().append("<br><span>" + (parseFloat(totalField.val()) - (parseFloat($(this).val()) || 0) + "</span>"));
            });

            $("input[id^='PaidAmount_']").on("input", function() {
                var id = $(this).attr("id");
                var suffix = id.split("_")[1];
                var totalField = $("#Total_" + suffix);

                var initial_Totals = parseFloat(totalField.val()) || 0;
                var paidAmount = parseFloat($(this).val()) || 0;
                var newTotal = initial_Totals - paidAmount;

                $(this).parent().find('span').text(newTotal);

            });
        });
    </script>

</body>

</html>