<h2><%=lngByCustomer &": " & str_StatisticsYear%></h2>
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

strOrderBy = request.querystring("orderBy")
if strOrderBy <> "" then
	strOrderBy = " ORDER BY "& strOrderBy &" "& strSorting
else
	strOrderBy = ""
end if

sql = "SELECT "_
    &" o.CustomerID AS CustomerID, "_
    &" c.CompanyName AS Company, "_
    &" c.LastName AS Name, "_
    &" c.Country AS Country, "_
    &" c.City AS City, "_
    &" COUNT(o.OrderID) AS Orders, "_
    &" SUM(o.DiscountPrices) AS Sum_Price_Discount, "_
    &" SUM(o.DiscountExtra) AS Sum_Extra_Discount, "_
    &" SUM(o.DiscountTotal) AS Sum_Discount, "_
    &" SUM(o.DiscountShipping) AS Sum_Shipp_Discount, "_
    &" SUM(o.PayExpenses) AS Sum_Pay_Expenses, "_
    &" SUM(o.ShipCharge) AS Sum_Shipp_Charge, "_
    &" SUM(o.Total) AS Sum_Total, "_
    &" SUM(o.TotalVAT) AS Sum_Total_VAT "_
	&" FROM (orders AS o "_
    	&" INNER JOIN customers AS c ON ((o.CustomerID = c.CustomerID))) "_
		& strYearWhere _ 
	&" GROUP BY o.CustomerID , c.CompanyName , c.LastName , c.Country , c.City "_
	&  strOrderBy
'Response.Write sql
Set rs = Server.CreateObject("ADODB.Recordset")
rs.CursorLocation = 3
rs.Open sql, Conn
Set rs.activeconnection = nothing
rsCount = rs.RecordCount

'== Get only the SELECT part of the sql
strSql = sql
pos = instr(strSql," FROM ")
strSql = trim(left(strSql,pos))
strSql = replace(strSql,"SELECT ","")

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
if strExport = "" then  %>
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
	s_Type = x.type
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
				<a href="<%=Request.ServerVariables("PATH_INFO")%>?by=Customer&orderBy=<%=strQuery %>"><span class="<%=sortColor%>"><%=s_Name%></span></a>
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
	s_Type = x.type
	if s_Type = 2 OR s_Type = 3 then
		strAlign = "align=""right"""
		strValue = x.value
	elseif s_Type = 5 then
		strAlign = "align=""right"""
		strValue = formatNumber(Cdbl(x.value),2)
	elseif s_Type = 6 then
		strAlign = "align=""right"""
		strValue = formatNumber(x.value,2)
	else 
		strAlign = "align=""left"""
		strValue = x.value
	end if
	s_Name = x.name
	if instr(s_Name,"Sum_") > 0 then
		strSumField(i,0) = s_Name
		strSumField(i,1) = strValue
		i = i + 1
	elseif instr(s_Name,"Count_") > 0 then
		strCountField(ii,0) = s_Name
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
	s_Name = x.name
	if instr(s_Name,"Sum_") > 0 then
		for z = 0 to i - 1
			if strSumField(z,0) = s_Name then
				intSum = FormatNumber(intSumTotal(z),2)
				exit for
			end if
		next
	elseif instr(s_Name,"Count_") > 0 then
		for y = 0 to ii - 1
			if strCountField(y,0) = s_Name then
				intSum = intCountTotal(y)
				exit for
			end if
		next
	elseif instr(s_Name,"Avg_") > 0 then
		for m = 0 to i - 1
			if cstr(trim(replace(s_Name,"Avg_",""))) = cstr(trim(replace(strSumField(m,0),"Sum_",""))) then
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