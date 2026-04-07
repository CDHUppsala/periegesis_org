<h2><%=lngByProduct &": " & str_StatisticsYear%></h2>
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
    &" ordereditems.ProductID AS Product_ID, "_
    &" products.ProductName AS Product_Name, "_
    &" COUNT(ordereditems.OrderID) AS Orders, "_
    &" SUM(ordereditems.Quantity) AS Items, "_
    &" ((100 / (100 - ordereditems.DiscountRate)) * ordereditems.OrderedPrice) AS Initial_Price, "_
	&" ordereditems.OrderedPrice AS Ordered_Price, "_
    &" ordereditems.DiscountRate AS Discount_Rate, "_
    &" (((100 / (100 - ordereditems.DiscountRate)) * ordereditems.OrderedPrice) - ordereditems.OrderedPrice) AS Discount, "_
    &" ((((100 / (100 - ordereditems.DiscountRate)) * ordereditems.OrderedPrice) - ordereditems.OrderedPrice) * SUM(ordereditems.Quantity)) AS Sum_Discount, "_
    &" (ordereditems.OrderedPrice * SUM(ordereditems.Quantity)) AS Sum_Sales "_
	&" FROM (ordereditems "_
		&" INNER JOIN products ON ordereditems.ProductID = products.ProductID) "_
		&" INNER JOIN orders ON ordereditems.OrderID = orders.OrderID "_
	& strYearWhere _
	&" GROUP BY ordereditems.ProductID , products.ProductName , ordereditems.OrderedPrice, ordereditems.DiscountRate "_
	& strOrderBy
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
	<table>
		<tr>
<%
firstField = True
i = 0
for each x in rs.fields
	'== Define column alignment
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
				<a href="<%=Request.ServerVariables("PATH_INFO")%>?by=Product&orderBy=<%=strQuery %>"><span class="<%=sortColor%>"><%=s_Name%></span></a>
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
	if x.Type = 2 OR x.Type = 3 then
		strAlign = "class=""alignRight"""
		strValue = (x.value)
	elseif x.Type = 5 then
		strAlign = "class=""alignRight"""
		strValue = formatNumber(Cdbl(x.value),2)
	elseif x.Type = 6 then
		strAlign = "class=""alignRight"""
		strValue = formatNumber(x.value,2)
	else 
		strAlign = "class=""alignLeft"""
		strValue = (x.value)
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
			<td <%=strAlign%>><%=(strValue) %></td>
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
			<th style="text-align: right"><%= intSum %></th>
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