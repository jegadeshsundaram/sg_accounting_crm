var process = "";

$(function() {
    
    var module = $('#module').val();

    $.validator.addMethod("extension", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "sql";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    });

    $('#form_restore').validate({
        rules: {
            db_file: {
                required: true,
                extension: true
            }
        },
        messages: {
            db_file: {
                required: "Please upload SQL",
                extension: "Invalid File Format! Please upload in SQL Format"
            }
        },
        highlight: function() {
            $('#uploaded_file_details').css("border-bottom", "1px solid red");
        },
        unhighlight: function() {
            $('#uploaded_file_details').css("border-bottom", "1px solid gray");
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        }
    });

    $('#btn_backup').click(function() {
        $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Backup Datafiles");
        $('#confirmSubmitModal .modal-body').html("Are you sure that you want to do a backup of Datafiles?");
        process = "backup";

        $("#utilityModal").modal('hide');
        $("#confirmSubmitModal").modal();
    });

    $('#btn_zap').click(function() {
        $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Zap Database");
        $('#confirmSubmitModal .modal-body').html("Please do a backup of Datafiles before proceed to Zap. <br /><br />Zap will remove all the records from Datafiles.");
        process = "zap";

        $("#utilityModal").modal('hide');
        $("#confirmSubmitModal").modal();
    });

    $('#btn_restore').click(function() {
        $("#utilityModal").modal('hide');
        $("#restoreModal").modal();

        $('#uploaded_file_details').val("");
        $('#db_file-error').hide();
        $('#db_file-error').val("");
        $('#uploaded_file_details').css("border-bottom", "1px solid gray");
    });

    $('#btn_cancel_restore').click(function() {
        $("#restoreModal").modal('hide');
        $("#utilityModal").modal();
    });

    $('#db_file').change(function() {
        $('#db_file-error').hide();
        var file = this.files[0].name;
        $('#uploaded_file_details').val(file);
    });

    $('#btn_submit_restore').click(function() {
        if($('#form_restore').valid()) {
            $("#restoreModal").modal('hide');
            $('#db_file-error').hide();

            $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Restore");
            $('#confirmSubmitModal .modal-body').html("Please do backup before proceed to restore.<br />Restore will overwrite data files.");
            $("#confirmSubmitModal").modal();

            process = "restore";
        }
    });

    $('#btn-confirm-yes').click(function() {
        $("#confirmSubmitModal").modal('hide');

        if(process == "backup") {
            window.location = "/"+module+"/df_backup";
        } else if(process == "zap") {
            window.location = "/"+module+"/df_zap";
        } else if(process == "restore") {
            $('#form_restore').attr('action', '/'+module+'/df_restore');
            $('#form_restore').submit();
        }
    });

    $('#btn-confirm-no').click(function() {
        $("#utilityModal").modal();
        $("#confirmSubmitModal").modal('hide');
    });
});