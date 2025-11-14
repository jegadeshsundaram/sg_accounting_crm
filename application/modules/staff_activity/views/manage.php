<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Staff Activity</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
                    <li class="breadcrumb-item">Staff</li>
                    <li class="breadcrumb-item active">Batch Receipt</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div id="message_area">
                    <?php get_flash_message('message'); ?>
                </div>
            </div>
        </div>

        <div class="row buttons-panel">
            <div class="col-lg-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h5 style="float: left; margin-top: 8px;">Manage</h5>
                        <button class='btn btn-outline-secondary btn-sm float-right' id='btn_back'>Back</button>
                    </div>
                    
                    <div class="card-header">
                        <button class='btn btn-primary btn-sm' id="btn_new">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i> Create
                        </button>

                        <button class='btn btn-danger btn-sm' id='btn_delete_all'>
                            <i class='fa fa-trash'></i> Delete All
                        </button>

                        <button class='btn bg-navy btn-sm' id='btn_report'>
                            <i class='fa fa-print'></i> Print
                        </button>
                    </div>
                    <div class="card-body table-responsive">
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="dt_" class="table table-hover" style="min-width: 1000px; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Action</th>
                                            <th>Date</th>
                                            <th>Staff</th>                                        
                                            <th>Task Details</th>
                                            <th>Time</th>
                                            <th>Minutes</th>
                                        </tr>
                                    </thead>
                                <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- Card - ends -->

            </div>
        </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Report Modal -->
<div id="reportModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
                <form id="frm_report" method="POST">
                    <div class="row form-group">
                        <div class="col-6">
                            <label for="start_date" class="control-label">Start Date<span class="cl-red">*</span></label>
                            <input type="text" id="start_date" name="start_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="end_date" class="control-label">End Date<span class="cl-red">*</span></label>
                            <input type="text" id="end_date" name="end_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-12">
                            <label for="staff" class="control-label">Staff<span class="cl-red">*</span></label><br />
                            <select id="staff" name="staff" class="form-control">
                                <?php echo $employees; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#reportModal">CANCEL</button>
               <button type="button" class="btn btn-primary btn-sm float-right" id="btn_print">Print</button>
            </div>
         </div>
      </div>
   </div>
</div>
            
<!-- Task Modal -->
<div id="taskModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-6">
                        <label for="activity_date" class="control-label">Date<span class="cl-red">*</span></label>
                        <input type="text" id="activity_date" class="form-control dp_full_date w-120" />
                    </div>
                    <div class="col-6">
                    </div>
                </div>
                
                <div class="row form-group">
                    <div class="col-12">
                        <label for="employee" class="control-label">Staff<span class="cl-red">*</span></label>
                        <select id="employee" class="form-control">
                            <?php echo $employees; ?>
                        </select>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-12">
                        <label for="task_description" class="control-label">Task Description<span class="cl-red">*</span></label>
                        <textarea rows="2" id="task_description" class="form-control"></textarea>
                    </div>
                </div>
                
                <div class="row form-group">
                    <div class="col-4">
                        <label for="start_time" class="control-label">Start Time<span class="cl-red">*</span></label>
                        <input type="text" id="start_time" class="form-control" onchange="validateHhMm(this);" />
                    </div>
                    <div class="col-4">
                        <label for="end_time" class="control-label">End Time<span class="cl-red">*</span></label>
                        <input type="text" id="end_time" class="form-control" onchange="validateHhMm(this);" />
                    </div>
                    <div class="col-4">
                        <label for="minutes" class="control-label">Minutes<span class="cl-red">*</span></label>
                        <input type="number" id="minutes" class="form-control" readonly />
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea rows="2" id="remarks" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-12">
                        <label for="supervisor_comments" class="control-label">Supervisor Comments</label>
                        <textarea rows="2" id="supervisor_comments" class="form-control"></textarea>
                    </div>
                </div>
                
            </div>
            <div class="card-footer">
               <input type="hidden" id="task_id" />
               <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#taskModal">CANCEL</button>
               <button type="button" class="btn btn-primary btn-sm float-right" id="btn_submit">SUBMIT</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/assets/js/datatable.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
   var url = '';
   
    $(function() {
        table = $('#dt_').DataTable({
            'processing': true,
            'ajax': {
                'url': '/staff_activity/ajax/tasks',
                'type': 'post'
            },
            'columnDefs': [
                { 'width': 130, 'targets': 1 },
                { 'width': 120, 'targets': 2 },
                { 'width': 300, 'targets': 3 },
                { 'width': 350, 'targets': 4 }
            ],
            "aaSorting": []
        });
        table.column( 0 ).visible( false ); // id

        $('select').select2();

        $("#btn_new").on('click', function() {
            clear_inputs();
            $('#btn_submit').html("CREATE");
            $('#taskModal').modal('show');
        });

        $("#btn_back").on('click', function() {
            window.location.href = "/staff_activity";
        });

        $("#btn_delete_all").on('click',function() {
            if(table.rows().eq(0).length > 0) {
                selectAllRows();

                url = '/staff_activity/ajax/delete_task';
                showData("deleteAll", url);
            }
        });

        $("#btn_report").on('click', function() {
            $('#reportModal').modal('show');
        });

        $("#btn_print").on('click', function() {
            $('#frm_report').attr('action', '/staff_activity/print_tasks');
            $('#frm_report').attr("target", "_blank");
            $('#frm_report').submit();
        });

        $(document).on("change", "#start_time", function() {
            var dtStart = new Date("7/20/2015 " + $(this).val());
            var dtEnd = new Date("7/20/2015 " + $('#end_time').val());

            if($(this).val() !== "" && $('#end_time').val() !== "") {
                var diff = (dtStart.getTime() - dtEnd.getTime()) / 1000;
                diff /= 60;
                $('#minutes').val(Math.abs(Math.round(diff)));
            }
        });

        $(document).on("change", "#end_time", function() {
            var dtStart = new Date("7/20/2015 " + $(this).val());
            var dtEnd = new Date("7/20/2015 " + $('#start_time').val());

            if($(this).val() !== "" && $('#start_time').val() !== "") {
                var diff = (dtStart.getTime() - dtEnd.getTime()) / 1000;
                diff /= 60;
                $('#minutes').val(Math.abs(Math.round(diff)));
            }
        });

        // EDIT - Single Transaction
        $('#dt_').on('click', 'tbody .dt_edit', function () {
            var rowData = table.row($(this).closest('tr')).data();
            var rowID = rowData[0];
            //$(this).closest('tr').addClass('selected');

            $('#task_id').val(rowID);
            $.post('/staff_activity/ajax/get_task', {
                sa_id: rowID
            }, function (task) {
                var obj = $.parseJSON(task);

                $('#employee').select2("destroy");
                $('#employee').val(obj.task['employee_id']);
                $('#employee').select2();
                var date = 
                $('#activity_date').val(obj.activity_date);
                $('#task_description').val(obj.task['activity_date']);

                $('#start_time').val(obj.task['start_time']);
                $('#end_time').val(obj.task['end_time']);
                $('#minutes').val(obj.task['minutes']);

                $('#remarks').val(obj.task['remarks']);
                $('#supervisor_comments').val(obj.task['supervisor_comments']);

                $('#btn_submit').html("UPDATE");
                $('#taskModal').modal('show');
            });
        });

        // DELETE - Single Transaction
        $('#dt_').on('click', 'tbody .dt_delete', function () {
            var rowData = table.row($(this).closest('tr')).data();
            var rowID = rowData[0];
            console.log(rowID);

            $(this).closest('tr').addClass('selected');

            $.confirm({
                title: '<i class="fa fa-info"></i> Delete Task',
                content: 'Are you sure to delete?',
                buttons: {
                    yes: {
                        btnClass: 'btn-warning',
                        action: function() {
                            $.post('/staff_activity/ajax/delete_task', {
                                sa_id: rowID
                            }, function (res) {
                                if(res == "error") {
                                    toastr.error("Delete Error!");
                                } else {
                                    toastr.success("Task Deleted!");
                                    table.ajax.reload();
                                }
                            });
                        }
                    },
                    no: {
                        btnClass: 'btn-dark',
                        action: function(){
                            table.ajax.reload();
                        }
                    }
                }
            });
        });

        $("#btn_submit").on('click', function (e) {
            if(isModalValid()) {
                save();
            }
        });

        $(document).on('click', '.card', function() {
            $('#message_area').html('');
        });

    }); // document ends
   
    $(document).ajaxComplete(function(event, request, settings) {
        $(".btn").prop("disabled", false);
    });

    function validateHhMm(inputField) {
        var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField.value);

        if (isValid) {
            inputField.style.borderColor = 'green';
        } else {
            inputField.style.borderColor = 'red';
        }

        return isValid;
    }

    function clear_inputs() {
        $('#task_id').val('');
        $('#employee').select2("destroy").val("").select2();
        $('#activity_date').val('');
        $('#task_description').val('');
        $('#start_time').val('');
        $('#end_time').val('');
        $('#minutes').val('');
        $('#remarks').val('');
        $('#supervisor_comments').val('');
    }

    function isModalValid() {
        var valid = true;
        if($('#employee').val() == "") {
            $("#employee").select2('open');
            valid = false;
        } else if($('#activity_date').val() == "") {
            $('#activity_date').focus();
            valid = false;
        } else if($('#task_description').val() == "") {
            $('#task_description').focus();
            valid = false;
        } else if($('#start_time').val() == "") {
            $('#start_time').focus();
            valid = false;
        }  else if($('#end_time').val() == "") {
            $('#end_time').focus();
            valid = false;
        }

        return valid;
    }    

    function save() {
        $.post('/staff_activity/ajax/save_task', {
            sa_id: $('#task_id').val(),
            employee_id: $('#employee').val(),
            activity_date: $('#activity_date').val(),
            task_description: $('#task_description').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val(),
            minutes: $('#minutes').val(),
            remarks: $('#remarks').val(),
            supervisor_comments: $('#supervisor_comments').val()
        }, function(res) {
            if($.trim(res) == '1') {
                toastr.success("Task Created!");
                table.ajax.reload();
                $('#taskModal').modal('hide');
            } else if($.trim(res) == 'updated') {
                toastr.success("Task Updated!");
                table.ajax.reload();
                $('#taskModal').modal('hide');
            } else {
                toastr.error("Save error!");
            }
        });
    }

</script>
