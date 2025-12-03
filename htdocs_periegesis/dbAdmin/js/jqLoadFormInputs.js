function sx_LoadDistinctWhere(targetField,targetValue,grandField,parentField,relationSQL) {

    var grandValue = $sx('select[name="' + grandField + '"]').val();
    var parentValue = "";
    if (parentField !== "") {
        var parentValue = $sx('select[name="' + parentField + '"]').val();
    }
    var arrData = {
        target_Value: targetValue,
        grand_Value: grandValue,
        parent_Value: parentValue,
        relation_SQL: relationSQL
    }
    //alert('targetField: '+ targetField +' = targetValue: '+ targetValue +' \n grandValue: '+ grandValue +' \n parentValue: '+ parentValue +' \n '+ relationSQL);

    $sx.ajax({
        url: "ajax_LoadFormInputs.php",
        type: "POST",
        cache: false,
        data: arrData,
        scriptCharset: "utf-8",
        success: function (result) {
            $sx('select[name="' + targetField + '"]').html(result);
        },
        error: function (xhr, status, error) {
            alert(status +'\n'+ xhr.responseText);
        }
    });
}