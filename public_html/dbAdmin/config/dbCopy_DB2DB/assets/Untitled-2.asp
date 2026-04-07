<%
'If you have a look at the following quick bit of code, you can see how ADOX is used to get what you want. Run it, and you should see that one of the field/column properties is called Default. You can edit this property using ADOX.

Function a()

    Dim cn As ADODB.Connection
    Dim cat As ADOX.Catalog
    Dim tbl As ADOX.Table
    Dim prp As ADOX.Property
    Dim col As ADOX.Column


    Set cn = CurrentProject.Connection
    Set cat = New ADOX.Catalog
    Set tbl = New ADOX.Table
    Set col = New ADOX.Column
    
    Set cat.ActiveConnection = cn
    
    Set tbl = cat.Tables("tblInvoices")
    
    For Each col In tbl.Columns
        For Each prp In col.Properties
            MsgBox prp.Name & ": " & prp
        Next
    Next
    
    cn.Close
    
    Set col = Nothing
    Set tbl = Nothing
    Set prp = Nothing
    Set cat = Nothing
    Set cn = Nothing
    
End Function


'To get a column's description, the program opens the database connection and makes an ADOX catalog representing the database. It finds the table in the Tables collection, finds the column in the table's Columns collection, and looks for the Description property in the Column object's Properties collection. Note that this causees an error if the Description property is not present so the code protects itself with an On Erro statement.
 
Private Sub cmdGetDescription_Click()
Dim conn As ADODB.Connection
Dim cat As ADOX.Catalog
Dim tbl As ADOX.Table
Dim col As ADOX.Column

    ' Open the connection.
    Set conn = New ADODB.Connection
    conn.ConnectionString = _
        "Provider=Microsoft.Jet.OLEDB.4.0;" & _
        "Persist Security Info=False;" & _
        "Data Source=" & txtDatabase.Text
    conn.Open

    ' Make a catalog for the database.
    Set cat = New ADOX.Catalog
    Set cat.ActiveConnection = conn

    ' Get the table.
    Set tbl = cat.Tables(txtTableName.Text)

    ' Get the column.
    Set col = tbl.Columns(txtFieldName.Text)

    ' Get the Description property.
    On Error Resume Next
    txtDescription.Text = col.Properties("Description")
    If Err.Number <> 0 Then
        txtDescription.Text = ""
    End If

    conn.Close
End Sub
 
'To set a description, the program performs the same steps except this time it sets the column's Description property.
 
Private Sub cmdSetDescription_Click()
Dim conn As ADODB.Connection
Dim cat As ADOX.Catalog
Dim tbl As ADOX.Table
Dim col As ADOX.Column

    ' Open the connection.
    Set conn = New ADODB.Connection
    conn.ConnectionString = _
        "Provider=Microsoft.Jet.OLEDB.4.0;" & _
        "Persist Security Info=False;" & _
        "Data Source=" & txtDatabase.Text
    conn.Open

    ' Make a catalog for the database.
    Set cat = New ADOX.Catalog
    Set cat.ActiveConnection = conn

    ' Get the table.
    Set tbl = cat.Tables(txtTableName.Text)

    ' Get the column.
    Set col = tbl.Columns(txtFieldName.Text)

    ' Set the Description property.
    col.Properties("Description") = txtDescription.Text
    txtDescription.Text = ""

    conn.Close
End Sub



'1. Using ADODB.Connection and OpenSchema method
Sub ListTablesADO()
  Dim Conn As New ADODB.Connection
  Dim TablesSchema As ADODB.Recordset
  Dim ColumnsSchema As ADODB.Recordset

  'Open connection you want To get database objects
  Conn.Provider = "MSDASQL"
  Conn.Open "DSN=...;Database=...;", "UID", "PWD"
  
  'Get all database tables.
  Set TablesSchema = Conn.OpenSchema(adSchemaTables) 
  Do While Not TablesSchema.EOF
    'Get all table columns.
    Set ColumnsSchema = Conn.OpenSchema(adSchemaColumns, _
      Array(Empty, Empty, "" & TablesSchema("TABLE_NAME")))
    Do While Not ColumnsSchema.EOF
      Debug.Print TablesSchema("TABLE_NAME") & ", " & _
        ColumnsSchema("COLUMN_NAME")
      ColumnsSchema.MoveNext
    Loop
    TablesSchema.MoveNext
  Loop
End Sub

'TablesSchema fields : TABLE_CATALOG, TABLE_SCHEMA, TABLE_NAME, TABLE_TYPE, TABLE_GUID, DESCRIPTION, TABLE_PROPID, DATE_CREATED, DATE_MODIFIED
'ColumnsSchema fields : TABLE_CATALOG, TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, COLUMN_GUID, COLUMN_PROPID, ORDINAL_POSITION, COLUMN_HASDEFAULT,
'   COLUMN_DEFAULT, COLUMN_FLAGS, IS_NULLABLE, DATA_TYPE, TYPE_GUID, CHARACTER_MAXIMUM_LENGTH, CHARACTER_OCTET_LENGTH, NUMERIC_PRECISION, 
'   NUMERIC_SCALE, DATETIME_PRECISION, CHARACTER_SET_CATALOG, CHARACTER_SET_SCHEMA, CHARACTER_SET_NAME, COLLATION_CATALOG, COLLATION_SCHEMA, 
'   COLLATION_NAME, DOMAIN_CATALOG, DOMAIN_SCHEMA, DOMAIN_NAME, DESCRIPTION, SS_DATA_TYPE

'2. Using ADOX.Catalog and its collections
Sub ListTablesADOX()
  Dim Conn As New ADODB.Connection
  
  'Open connection you want To get database objects
  Conn.Provider = "MSDASQL"
  Conn.Open "DSN=...;Database=...;", "UID", "PWD"
  
  'Create catalog object
  Dim Catalog As New ADOX.Catalog
  Set Catalog.ActiveConnection = Conn
  
  'List tables And columns
  Dim Table As ADOX.Table, Column As ADOX.Column
  For Each Table In Catalog.Tables
    For Each Column In Table.Columns
      Debug.Print Table.Name & ", " & Column.Name
    Next
  Next
End Sub
'Table properties : Columns, DateCreated, DateModified, Indexes, Keys, Name, ParentCatalog, Properties, Type
'Column properties : Attributes, DefinedSize, Name, NumericScale, ParentCatalog, Precision, Properties, RelatedColumn, SortOrder, Type
%>