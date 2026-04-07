<h2><%=lngByDate &": " & str_StatisticsYear%></h2>
<%
'on error resume next
if session("sorting") <> "" then
	if session("sorting") = "ASC" then
		session("sorting") = "DESC"
		strSorting = "DESC"
	else
		session("sorting") = "ASC"
		strSorting = "ASC"
	end if
else
	session("sorting") = "ASC"
	strSorting = "ASC"
end if

if NzStr(strYearWhere) > 0 then
	strYearWhere = replace(strYearWhere,"WHERE ","AND ")
end if

strOrderBy = request.querystring("orderBy")
if strOrderBy <> "" then
	strOrderBy = " ORDER BY "& strOrderBy &" "& strSorting
else
	strOrderBy = ""
end if

if request.form("GroupByField") <> "" then 
   strGroupByField = request.form("GroupByField")
   session("GroupByField") = strGroupByField 
elseif session("GroupByField") <> "" and request("New") = "" then
   strGroupByField = session("GroupByField")
else
   strGroupByField = ""
   session("GroupByField") = ""
end if

if strGroupByField = "" OR strGroupByField = "Year" then 
	GroupByField = "Year(orders.orderDate) AS By_Year"
	if radioMySQLDatabase then
		strGroupBy = "By_Year"
	else
		strGroupBy = "Year(orders.orderDate)"
	end if
end if

if strGroupByField = "Month" then
	GroupByField = "Year(orders.orderDate) AS By_Year, Month(orders.orderDate) AS By_Month"
	if radioMySQLDatabase then
		strGroupBy = "By_Year, By_Month"
	else
		strGroupBy = "Year(orders.orderDate), Month(orders.orderDate)"
	end if
end if

if strGroupByField = "Quarter" then
	if radioMySQLDatabase then
		GroupByField = "Year(orders.orderDate) AS By_Year, QUARTER(orders.orderDate) AS By_Quarter"
		strGroupBy = "By_Year, By_Quarter"
	else
		GroupByField = "Year(orders.orderDate) AS By_Year, DatePart('q',orders.orderDate,2,2) AS By_Quarter"
		strGroupBy = "Year(orders.orderDate), DatePart('q', orders.orderDate, 2, 2)"
	end if
end if
if strGroupByField = "Weeck" then
	if radioMySQLDatabase then
		GroupByField = "Year(orders.orderDate) AS By_Year, YEARWEEK(orders.orderDate) AS By_Weeck"
		strGroupBy = "By_Year, By_Weeck"
	else
		GroupByField = "Year(orders.orderDate) AS By_Year, DatePart('ww',orders.orderDate,2,2) AS By_Weeck"
		strGroupBy = "Year(orders.orderDate), DatePart('ww',orders.orderDate,2,2)"
	end if
end if

sql = "SELECT DISTINCTROW "& GroupByField &", "_
	&" Count(orderID) AS Orders,  "_
 	&" Sum(DiscountExtra) AS Sum_Extra_Discount,  "_
	&" Sum(DiscountShipping) AS Sum_Shipp_Discount, "_
	&" Sum(DiscountPrices) AS Sum_Prices_Discount, "_
 	&" Sum(DiscountTotal) AS Sum_Discount,  "_
 	&" Sum(PayExpenses) AS Sum_Pay_Expenses, "_
 	&" Sum(ShipCharge) AS Sum_Shipping, "_
 	&" Sum(total) AS Sum_Total, "_
 	&" Sum(TotalVAT) AS Sum_Total_VAT "_
	&" FROM Orders "_
	&" WHERE Total > 0 "_
	& strYearWhere _
	&" GROUP BY "& strGroupBy _
	&  strOrderBy 
'Response.Write sql
set rs = Server.CreateObject("ADODB.Recordset")
rs.Open sql, conn, 3, 3
rsCount = rs.RecordCount

'== Get only the SELECT part of the sql
strSql = sql
pos = instr(strSql," FROM ")
strSql = trim(left(strSql,pos))
strSql = replace(strSql,"SELECT ","")
strSql = replace(strSql,"DISTINCTROW","")
strSql = trim(replace(strSql,"DISTINCT",""))

'== create an array with all filed names to be used for sorting
if strSql <> "*" then
	strArr = split(strSql,",")
	for i = 0 to Ubound(strArr)
		if instr(strArr(i)," AS ") > 0 then
			strTempArr = split(strArr(i)," AS ")
			if instr(strTempArr(0),"(") = 0 AND instr(strTempArr(0),")") > 0 then 
				strNew = strNew &", "& trim(strTempArr(0)) &";;"
			else
				strNew = strNew & strTempArr(0)&";;"
			end if
		else
			if instr(strArr(i),"(") = 0 AND instr(strArr(i),")") > 0 then 
				strNew = strNew &", "& trim(strArr(i)) &";;"
			elseif instr(strArr(i),"(") > 0 AND instr(strArr(i),")") = 0 then 
				strNew = strNew & strArr(i)
			else
				strNew = strNew & strArr(i) &";;"
			end if
		end if
	next
	strSql = split(strNew,";;")
end if

if (request.form("inYear") OR session("inYear") <> "") then strChecked = " checked" else strChecked = ""
if strExport = "" then  %>
<div class="coloredBG">
<form method="post" action="<%=Request.ServerVariables("PATH_INFO")%>?by=Date" name="GroupBy">
   <%=lngSelectGroupByField%>: <select name="GroupByField" size="1">
      <%if strGroupByField = "Year" then sxSelected = " selected" else sxSelected = ""%>
      <option value="Year"<%=sxSelected%>><%=lngYear%></option>
      <%if strGroupByField = "Quarter" then sxSelected = " selected" else sxSelected = ""%>
      <option value="Quarter"<%=sxSelected%>><%=lngQuarter%></option>
      <%if strGroupByField = "Month" then sxSelected = " selected" else sxSelected = ""%>
      <option value="Month"<%=sxSelected%>><%=lngMonth%></option>
      <%if strGroupByField = "Weeck" then sxSelected = " selected" else sxSelected = ""%>
      <option value="Weeck"<%=sxSelected%>><%=lngWeek%></option>
   </select>
   <input type="submit" name="Select" value="Select">
</form>
</div>
<h3>Κάνε κλικ στους τίτλους για αλλαγή ταξινόμησης</h3>
<div id="tableBG">
<%end if%>
	<table border="0" cellpadding="0" cellspacing="1">
		<tr>
<%
firstField = True
i = 0
for each x in rs.fields
	'== Define column alignment
	s_Type = x.Type
	s_Name = x.Name
	if instr(s_Name,"_") > 0 then s_Name = replace(s_Name,"_"," ")
	if s_Type = 2 OR s_Type = 3 OR s_Type = 5 OR s_Type = 6 then
		strAlign = "align=""right"""
	else 
		strAlign = "align=""left"""
	end if

	'== Set the initial field names as query strings for ordering
	if isArray(strSql) then
		strQuery = trim(strSql(i))
	else
		strQuery = s_Name
	end if
	i = i + 1

	'== Define the color of the current ordering field
	if  request.querystring("orderBy") <> "" then
		if strQuery = request.querystring("orderBy") then 
			sortColor = "sortColor" 
		else 
			sortColor = ""
		end if
	else
		if firstField then sortColor = "sortColor" else sortColor = ""
	end if

	firstField = False
	if strExport = "" then  %>
			<th <%=strAlign%>>
				<a href="<%=Request.ServerVariables("PATH_INFO")%>?by=Date&orderBy=<%=strQuery %>"><span class="<%=sortColor%>"><%=s_Name%></span></a>
			</th>
	<%else %>
			<th <%=strAlign%>>
				<span class="<%=sortColor%>"><%=s_Name%></span>
			</th>
	<%end if
next
%>
		</tr>
<%
On Error Resume Next
rs.MoveFirst
do while Not rs.eof
%>
		<tr>
<%

i = 0
ii = 0
for each x in rs.fields
	if x.type = 2 OR x.type = 3 then
		strAlign = "align=""right"""
		strValue = x.value
	elseif x.type = 5 then
		strAlign = "align=""right"""
		strValue = formatNumber(Cdbl(x.value),2)
	elseif x.type = 6 then
		strAlign = "align=""right"""
		strValue = formatNumber(x.value,2)
	else 
		strAlign = "align=""left"""
		strValue = x.value
	end if

	if instr(x.name,"Sum_") > 0 then
		strSumField(i,0) = x.name
		strSumField(i,1) = strValue
		i = i + 1
	elseif instr(x.name,"Count_") > 0 then
		strCountField(ii,0) = x.name
		strCountField(ii,1) = strValue
		ii = ii + 1
	end if
%>			
			<td <%=strAlign%>><%=strValue %></td>
<%
next
%>
		</tr>
<%
for z = 0 to i - 1
	intSumTotal(z) = intSumTotal(z) + cdbl(strSumField(z,1))
next
for y = 0 to ii - 1
	intCountTotal(y) = intCountTotal(y) + int(strCountField(y,1))
next

rs.MoveNext
loop
%>
		<tr>
<%
intSum = "&nbsp;"
for each x in rs.fields
	if instr(x.name,"Sum_") > 0 then
		for z = 0 to i - 1
			if strSumField(z,0) = x.name then
				intSum = FormatNumber(intSumTotal(z),2)
				exit for
			end if
		next
	elseif instr(x.name,"Count_") > 0 then
		for y = 0 to ii - 1
			if strCountField(y,0) = x.name then
				intSum = intCountTotal(y)
				exit for
			end if
		next
	elseif instr(x.name,"Avg_") > 0 then
		for m = 0 to i - 1
			if cstr(trim(replace(x.name,"Avg_",""))) = cstr(trim(replace(strSumField(m,0),"Sum_",""))) then
				intSum = formatNumber(cdbl(intSumTotal(m))/cdbl(intCountTotal(0)),2)
				exit for
			end if
		next
	else
		intSum = "&nbsp;"
	end if
%>			
			<th style="text-align:right"><%= intSum %></th>
<%
next
%>
		</tr>
<%

rs.close
set rs = nothing
%>
	</table>
<%if strExport = "" then %>
</div>
<%end if%>