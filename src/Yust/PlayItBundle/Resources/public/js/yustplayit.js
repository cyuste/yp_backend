var img_ext = ['jpg','jpeg','tif','tiff','bmp','gif','png'];
var vid_ext = ['avi','mov','mp4','wmv','mkv'];
var ppt_ext = ['zip','rar','pdf'];
var max_filesize = 60000000;

 var editOptions = {
    success: function(responseText,statusText,xhr, $form) {
            $('#editModal').modal('hide');
            $('#editModal').remove();
            refreshActiveList();
            refreshAssetsList();
        }       
 };
 
 function paintAssetsList(tableData){
     $("#table-available").replaceWith(tableData);
     refreshAssetsList();
 }
 
 function turnOnOffContent() {
    var asset_id = $(this).closest("tr").attr("id");
    var group_id = $(this).closest("table").data("group_id");
    var table = $(this).closest("table").attr("id");
    var url;
    if (table == "table-available") {
        url = Routing.generate("turnOnContent", {id: asset_id, groupId: group_id});
    } else {
        url = Routing.generate("turnOffContent", {id: asset_id});
    }
    $.post(url,
    {},
    function(data, status){
        $("#table-active").replaceWith(data);
        refreshActiveList();
    });  
 }
 
 function refreshActiveList() {
    doListSortable();
    activateEditListener();
    activateEraseListener();
    activateTurnOffListener();  
}

function refreshAssetsList() {
    activateEditListener();
    activateEraseListener();
    activateTurnOnListener(); 
}

function doListSortable() {
    $(".sortable").sortable({
       stop: function(event, ui){
            var group_id = $("#table-active").data("group_id");
            var rows = $("#table-active").children("tbody").find("tr");
            var table_order = [];
            for(i = 0; i < rows.length; i++){
                var row = [$(rows[i]).attr('id'), i + 1];
                table_order[i] = row;
            }
            $.post(Routing.generate('reOrderList', {id: group_id}),
            {
                orderList: table_order
            });  
       }
   });
}

function activateEraseListener() {
    $('[data-toggle="popover"]').popover({
        content: get_template('confirm-delete'),
        triger: 'manual'
    }).click(function() {
        $(this).popover('show');
        $(".cancel-delete").click(function() {
            $(this).closest(".popover").popover('hide');
         });
        $(".confirm-delete").click(function() {
            var asset_id = $(this).closest("tr").data('assetid');
            var group_id = $(this).closest(".assets-table").data("group_id");
            $.post(Routing.generate('delete', { id: asset_id }),
                {groupId: group_id},
                function(data, result){
                    $("#contents-tables").replaceWith(data);
                    refreshAssetsList();
                    refreshActiveList();
                }
            );
            $(this).closest(".popover").popover('hide');
         });              
    });
}

function activateTurnOnListener(){
    $('.turnon_asset_button').click(turnOnOffContent);
}

function activateTurnOffListener(){
    $('.turnoff_asset_button').click(turnOnOffContent);
}

function activateEditListener(){
    $('.edit-asset-button').off("click");
    $('.edit-asset-button').click(function() {
        var asset_id = $(this).closest("tr").data('assetid');
        $.post(Routing.generate('edit', {id: asset_id}),
        {},
        function(data, status){
            $("#editFormContainer").append(data);
            $("#editModal").modal('show');         
            var action_url = Routing.generate('edit', {id: asset_id});
            $('#myForm').attr('action', action_url);
            $('#myForm').ajaxForm(editOptions);
            $('#editModal').on('hidden.bs.modal', function () {
                $('#editFormContainer').empty();
            });
        });    
    });
}

function activateTabs(){
    $("#myTab a:first").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        loadAssetForm();
    });
    $("#myTab a:last").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        loadWebForm();
    });
}

function loadWebForm(){
    var group_id = $("#table-available").data("group_id");
    var action_url = Routing.generate('webContent', {id: group_id});
    $.get(action_url, function(data,status) {
        $("#myForm").remove();
        $(".modal-content").append(data);
        activateTabs();
        
        var action_url = Routing.generate('webContent', {id: group_id});
        $('#myForm').attr('action', action_url);
        var options = {
            success: function(responseText,statusText,xhr, $form) {
                    $('#addModal').modal('hide');
                    paintAssetsList(responseText);    
                }       
        };
        $('#myForm').ajaxForm(options);
        
        $('#newWeb_content_type').change(function() {
            var cType = $('#newWeb_content_type').val();
            if (cType != 3){
                loadAssetForm();
            }
        });
    });
}

function loadAssetForm(){
    var group_id = $("#table-available").data("group_id");
    $.post(Routing.generate('upload', {id: group_id}),
    {},
    function(data, status){
        $("#myForm").remove();
        $(".modal-content").append(data);
        $("#addModal").modal('show');
        activateTabs();
        $('#newContent_content_type').change(function() {
            var cType = $('#newContent_content_type').val();
            switch(cType) {
                case '1':
                    $(".img_opts").show();
                    $("#video-help").hide();
                    $("#ppt-help").hide();
                    break;
                case '2':
                    $(".img_opts").hide();
                    $("#video-help").show();
                    $("#ppt-help").hide();
                    break;
                case '3':
                    loadWebForm();
                    break;
                case '4':
                    $(".img_opts").hide();
                    $("#video-help").hide();
                    $("#ppt-help").show();
                    break;
            } 
        });
        var action_url = Routing.generate('uploadContent', {id: group_id});
        $('#myForm').attr('action', action_url);
        var bar = $('.bar');
        var percent = $('.percent');
        var options = {
            beforeSubmit: function(arr, $form, options) {
                for(i = 0; i < arr.length; i++){
                    if(arr[i].type == "file"){
                        var size = arr[i].value.size;
                        break;
                    }
                }
                if(size > max_filesize) {
                    alert("Tamaño de fichero excesivo. El tamaño máximo permitido es " + (max_filesize / 1000000) + " MB." )
                    return false;
                }
                return true;
            },
            beforeSend: function() {
                var percentVal = '0%';
                bar.width(percentVal);
                percent.html(percentVal);
                $('.progress').show()
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal);
                percent.html(percentVal);
            },
            success: function(responseText,statusText,xhr, $form) {
                    $('#addModal').modal('hide');
                    paintAssetsList(responseText);    
                }       
        };
        $('#myForm').ajaxForm(options);
        $('#newContent_content_file').change(function() {
            var filename = $('#newContent_content_file').val();
            var fn_array = filename.split('.');
            var extension = fn_array.pop();
            if($("#newContent_content_name").val() === ""){
                var contentName = fn_array.pop();
                if(navigator.userAgent.indexOf("Chrome") != -1 ) {
                    var pos = contentName.indexOf("fakepath");
                    if (pos > 0) {
                        contentName = contentName.slice(pos+9);
                    }
                }
                $("#newContent_content_name").val(contentName);
            }
            if ($.inArray(extension, img_ext) != -1){
                $(".img_opts").show();
                if($("#newContent_content_length").val() == ""){
                    $("#newContent_content_length").val(10);
                }
            } else if ($.inArray(extension, vid_ext) != -1){                 
                $("#newContent_content_type").val(2);
                $("#newContent_content_length").val(0);
                $("#video-help").show();
            } else if ($.inArray(extension, ppt_ext) != -1){                 
                $("#newContent_content_type").val(4);
                $("#newContent_content_length").val(10);
                $("#ppt-help").show();
            }
        });
    });    
}


$(document).ready(function(){
   
   // Functions inherited from Screenly
   get_template = function(name) {
     return _.template(($("#" + name + "-template")).html());
   };

    // Do assets_on table sortable
    doListSortable();

    // Activate delete button
    activateEraseListener();
    
    // on-off button listener. TODO Mejorar este mierda-codigo
    activateTurnOnListener();
    activateTurnOffListener();
    activateEditListener();
    
    // Add new content listener
    $('#newContent').click(function() {
        loadAssetForm();   
    });
});




