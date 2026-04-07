<!-- #include virtual="/dbadmin/functionsLanguage.asp" -->
<!-- #include virtual="/dbadmin/login/lockPage.asp" -->
<!-- #include virtual="/dbadmin/functionsDBConn.asp" -->
<%
response.buffer=true
if request.querystring("new") <> "" then
	Session.Contents.Remove("GroupByField")
	Session.Contents.Remove("sorting")
end if
str_Path = Request.ServerVariables("QUERY_STRING")

Dim strExport
	strExport = request.querystring("export")

Dim strStatsBy
	strStatsBy = request.querystring("by")
	if strStatsBy = "" then strStatsBy = "Product"

connOpen
arrYears = vbNullString
sql = "SELECT DISTINCT YEAR(OrderDate) "_
	&" FROM orders "_
	&" ORDER BY YEAR(OrderDate) DESC "
set rs = conn.Execute(sql)
if Not rs.eof then
	arrYears = rs.GetRows()
end if
rs.close
set rs = Nothing
%>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>STUDIO X CMS - Statistics</title>
<%if strExport = "" then %>
<link rel="stylesheet" type="text/css" href="../css/sxCMS.css">
<%end if%>
</head>
<body>
<%if strExport = "" then %>
<div id="mainHeader">
	<h2><%=getCapitals(lngSaleStatistics)%></h2>
	<div style="float: right">
		<a target="_black" href="default.asp?by=<%= strStatsBy %>&export=print"><%=lngPrintText%></a>
		<a target="_top" href="default.asp?by=<%= strStatsBy %>&export=excel"><%=lngSaveInExcel%></a>
		<a target="_top" href="default.asp?by=<%= strStatsBy %>&export=word"><%=lngSaveInWord%></a>
		<a target="_top" href="default.asp?by=<%= strStatsBy %>&export=html"><%=lngSaveInHTML%></a>
	</div>
	<div>
   		<a href="default.asp?by=Product&new=yes"><%=lngByProduct%> »</a>
	   	<a href="default.asp?by=Customer&new=yes"><%=lngByCustomer%> »</a>
	   	<a href="default.asp?by=Area&new=yes"><%=lngByArea%> »</a>
	   	<a href="default.asp?by=Date&new=yes"><%=lngByDate%> »</a>
	</div>
</div>
<%
Dim intStatYear
	intStatYear = request.form("StatYear")
	if NzInt(intStatYear) = 0 then
		intStatYear = 0
	else
		intStatYear = cint(intStatYear)
		session("StatYear") = intStatYear
	end if
	if NzInt(session("StatYear")) > 0 then
		intStatYear = session("StatYear")
	end if

Dim int_DefaultYear
	int_DefaultYear = 0
	if isArray(arrYears) then
		int_DefaultYear = arrYears(0,0)
	end if

if cint(intStatYear) = 0 AND NzInt(int_DefaultYear) > 0 then
	intStatYear = cint(int_DefaultYear)
end if
%>
<div class="floatRight" style="margin-right: 5%">
	<%if isArray(arrYears) then %>
	<form method="post" name="GetByYear" action="default.asp?<%=str_Path%>" >
		<select name="StatYear">
			<option value="1900">All Years</option>
		<%iRows = uBound(arrYears,2)
		for r = 0 to iRows
			i_Year = cint(arrYears(0,r))
			strSelected = ""
			if (r = 0 AND cint(intStatYear) = -1) OR cint(intStatYear) = i_Year then strSelected = "selected " %>
			<option <%=strSelected%>value="<%=i_Year%>"><%=i_Year%></option>
		<%Next%>
		</select> <input type="submit" name="SubmitYear" value="Select Year">
	</form>
	<%end if%>
</div>
<%end if

Dim strYearWhere, int_StatisticsYear
	strYearWhere = ""
	str_StatisticsYear = "All Years"
if cint(intStatYear) <> 1900 then
	strYearWhere = " WHERE YEAR(OrderDate) = "& intStatYear
	str_StatisticsYear = "From Year "& intStatYear
end if

dim strSumField(10,1)
dim intSumTotal(10)
dim strCountField(10,1)
dim intCountTotal(10)

if strStatsBy = "Product" then %>
<!-- #include file="statsByProduct.asp" -->
<%elseif strStatsBy = "Customer" then %>
<!-- #include file="statsByCustomer.asp" -->
<%elseif strStatsBy = "Area" then %>
<!-- #include file="statsByArea.asp" -->
<%elseif strStatsBy = "Date" then %>
<!-- #include file="statsByDate.asp" -->
<%end if

connClose
%>
</body>
</html>
<%
'====== EXPORT TEXT
if strExport = "excel" then
	Response.ContentType = "application/vnd.ms-excel"
	Response.AddHeader "Content-Disposition:", "attachment; filename="& Request.ServerVariables("HTTP_HOST") &"_statistics_"& strStatsBy &"_"& date() &".xls"
elseif strExport = "word" then
	Response.ContentType = "application/vnd.ms-word"
	Response.AddHeader "Content-Disposition:", "attachment; filename="& Request.ServerVariables("HTTP_HOST") &"_statistics_"& strStatsBy &"_"& date() &".doc"
elseif strExport = "html" then
	Response.ContentType = "text/html"
	Response.AddHeader "Content-Disposition:", "attachment; filename="& Request.ServerVariables("HTTP_HOST") &"_statistics_"& strStatsBy &"_"& date() &".html"
end If
if strExport = "print" then %>
<script>
	window.print();
</script>
<%end if%>