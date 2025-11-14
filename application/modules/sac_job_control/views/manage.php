<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">SAC Job Control</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
                    <li class="breadcrumb-item active">Jobs</li>
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

                        <button class='btn bg-purple btn-sm' id='btn_detailed'>
                            <i class='fa fa-print'></i> Detailed Report
                        </button>

                        <button class='btn bg-navy btn-sm' id='btn_kash'>
                            <i class='fa fa-print'></i> Kash Report
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
                                            <th>Accountant</th>
                                            <th>Job Code</th>
                                            <th>Job Value</th>
                                            <th>Financial Period</th>
                                            <th>Remarks</th>
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

<!-- Task Modal -->
<div id="jobModal" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="card card-default" style="margin-bottom: 0rem;">
                <div class="card-body" style="padding-top: 0px;">
                    <input type="text" id="focus" style="width: 1px; height: 1px; padding: 0; margin: 0; opacity: 0" readonly />
                    <div class="row form-group job_code_input" style="display: none;">
                        <label for="customer" class="control-label col-12">Job Code / Reference & Finacial Year<span class="cl-red">*</span></label>
                        <div class="col-9">
                            <select id="customer" class="form-control">
                                <?php echo $customers; ?>
                            </select>
                            <span class="duplicate_error" style="display: none; color: red; font-size: 12px;">Duplicate reference disallowed</span>
                        </div>
                        <div class="col-3">
                            <input type="text" id="financial_year" class="form-control" maxlength="4" style="width: 65px" />
                        </div>
                    </div>

                    <div class="row form-group job_code_edit" style="display: none">
                        <div class="col-12">
                            <label class="control-label">Job Code: <span id="job_code" style="color:red; font-weight: bold;"></span></label>
                        </div>
                    </div>
                    
                    <div class="row form-group">
                        <div class="col-6">
                            <label for="fy_start_date" class="control-label">Financial Year Start<span class="cl-red">*</span></label>
                            <input type="text" id="fy_start_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="fy_end_date" class="control-label">Financial Year End<span class="cl-red">*</span></label>
                            <input type="text" id="fy_end_date" class="form-control dp_full_date w-120"  />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="job_confirmed_date" class="control-label">Job Confirmed Date<span class="cl-red">*</span></label>
                            <input type="text" id="job_confirmed_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="promised_delivery_date" class="control-label">Promised Delivery Date<span class="cl-red">*</span></label>
                            <input type="text" id="promised_delivery_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="job_value" class="control-label">Job Value<span class="cl-red">*</span></label>
                            <input type="number" id="job_value" class="form-control w-180" />
                        </div>
                        <div class="col-6">
                            <label for="payment_collected" class="control-label">Payment Collected<span class="cl-red">*</span></label>
                            <input type="number" id="payment_collected" class="form-control w-180" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="balance_to_collect" class="control-label">Balance to Collect</label>
                            <input type="number" id="balance_to_collect" class="form-control w-180" readonly />
                        </div>
                        <div class="col-6">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-12">
                            <label for="accountant" class="control-label">Accountant Incharge<span class="cl-red">*</span></label>
                            <select id="accountant" class="form-control">
                                <?php echo $accountants; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="assignment_date" class="control-label">Assignment Date</label>
                            <input type="text" id="assignment_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="agreed_completion_date" class="control-label">Agreed Completion Date</label>
                            <input type="text" id="agreed_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="actual_completion_date" class="control-label">Actual Completion Date</label>
                            <input type="text" id="actual_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6"></div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="sales_input_target_date" class="control-label">Sales Input Target Date</label>
                            <input type="text" id="sales_input_target_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="receipt_input_target_date" class="control-label">Receipt Input Target Date</label>
                            <input type="text" id="receipt_input_target_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="purchase_input_target_date" class="control-label">Purchase Input Target Date</label>
                            <input type="text" id="purchase_input_target_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="payment_input_target_date" class="control-label">Payment Input Target Date</label>
                            <input type="text" id="payment_input_target_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="draft_accounts_completion_date" class="control-label">Draft Accounts Completion Date</label>
                            <input type="text" id="draft_accounts_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="final_accounts_completion_date" class="control-label">Final Accounts Completion Date</label>
                            <input type="text" id="final_accounts_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-6">
                            <label for="bank_reckon_completion_date" class="control-label">Bank Recon Completion Date</label>
                            <input type="text" id="bank_reckon_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6"></div>
                    </div>

                    <div id="rd_btns" class="row form-group" tabindex='1'>
                        <div class="col-6">
                            <label for="tax_compilation" class="control-label">Is Tax Compiled?<span class="cl-red">*</span></label><br />
                            <input type="radio" id="tax_compilation_yes" name="tax_compilation" value="1" class="radio-inp" autocomplete="off">
                            <label class="radio-lbl" for="tax_compilation_yes">Yes</label>
                            <input type="radio" id="tax_compilation_no" name="tax_compilation" value="0" class="radio-inp" autocomplete="off">
                            <label class="radio-lbl no_rd" for="tax_compilation_no">No</label>
                        </div>
                        <div class="col-6">
                            <label for="job_closed" class="control-label">Is Job Closed?<span class="cl-red">*</span></label><br />
                            <input type="radio" id="job_closed_yes" name="job_closed" value="1" class="radio-inp" autocomplete="off">
                            <label class="radio-lbl" for="job_closed_yes">Yes</label>
                            <input type="radio" id="job_closed_no" name="job_closed" value="0" class="radio-inp" autocomplete="off">
                            <label class="radio-lbl no_rd" for="job_closed_no">No</label>
                        </div>
                    </div>

                    <div class="row form-group tax_compile" style="display: none">
                        <div class="col-6">
                            <label for="estimated_completion_date" class="control-label">Estimated Completion Date<span class="cl-red">*</span></label>
                            <input type="text" id="estimated_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="tax_completion_date" class="control-label">Actual Completion Date<span class="cl-red">*</span></label>
                            <input type="text" id="tax_completion_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>

                    <div class="row form-group tax_compile" style="display: none">
                        <div class="col-6">
                            <label for="tax_compilation_fees" class="control-label">Tax Compilation Fees $<span class="cl-red">*</span></label>
                            <input type="number" id="tax_compilation_fees" class="form-control w-180" />
                        </div>
                        <div class="col-6"></div>
                    </div>

                    <div class="row form-group">
                        <div class="col-12">
                            <label for="accountant_remarks" class="control-label">Remarks by Accountant</label>
                            <textarea id="accountant_remarks" class="form-control" style="height: 50px !important"></textarea>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-12">
                            <label for="manager_remarks" class="control-label">Remarks by Manager</label>
                            <textarea id="manager_remarks" class="form-control" style="height: 50px !important"></textarea>
                        </div>
                    </div>
                    
                </div>
                <div class="card-footer">
                    <input type="hidden" id="job_id" />
                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#jobModal">CANCEL</button>
                    <button type="button" class="btn btn-primary btn-sm float-right" id="btn_submit">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div id="reportModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
                <h5>Report</h5>
            </div>
            <div class="card-body">
                <form id="frm_" method="POST">
                    <div class="row form-group">
                        <div class="col-6">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" id="start_date" name="start_date" class="form-control dp_full_date w-120" />
                        </div>
                        <div class="col-6">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" id="end_date" name="end_date" class="form-control dp_full_date w-120" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-12">
                            <label for="staff" class="control-label">Accountant</label><br />
                            <select id="accountant_id" name="accountant_id" class="form-control">
                                <?php echo $accountants; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-12">
                            <label for="job" class="control-label">Jobs</label><br />
                            <input type="radio" id="job_all" name="job" value="" class="radio-inp" autocomplete="off" checked>
                            <label class="radio-lbl" for="job_all">All</label>
                            <input type="radio" id="job_open" name="job" value="0" class="radio-inp" autocomplete="off">
                            <label class="radio-lbl" for="job_open">Open</label>
                            <input type="radio" id="job_closed" name="job" value="1" class="radio-inp" autocomplete="off">
                            <label class="radio-lbl no_rd" for="job_closed">Closed</label>
                        </div>
                    </div>

                    <div class="row form-group kash" style="display: none">
                        <div class="col-7">
                            <label for="third_party_cost" class="control-label"> Third Part Cost</label>
                            <input type="number" id="third_party_cost" name="third_party_cost" class="form-control w-180" />
                        </div>
                        <div class="col-5">
                            <label for="marketting_cost_percentage" class="control-label"> Marketting Cost (%)</label>
                            <input type="number" id="marketting_cost_percentage" name="marketting_cost_percentage" class="form-control w-120" />
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#reportModal">CANCEL</button>
               <button type="button" class="btn btn-primary btn-sm float-right" id="btn_print_detailed" style="display: none">PRINT</button>
               <button type="button" class="btn btn-primary btn-sm float-right" id="btn_print_kash" style="display: none">PRINT</button>
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

<style>
    #jobModal .card-body {
        height: 450px;
        overflow-y: auto;
    }
    .radio-lbl {
        padding-right: 10px;
        font-size: 1rem !important;
    }
</style>

<script type="text/javascript">
   var url = '';
   var duplicate = false;
   
    $(function() {
        table = $('#dt_').DataTable({
            'processing': true,
            'ajax': {
                'url': '/sac_job_control/ajax/list',
                'type': 'post'
            },
            'columnDefs': [
                { 'width': 120, 'targets': 1 },
                { 'width': 250, 'targets': 2 },
                { 'width': 150, 'targets': 3 },
                { 'width': 150, 'targets': 4 },
                { 'width': 200, 'targets': 5 }
            ],
            "aaSorting": []
        });
        table.column( 0 ).visible( false ); // id

        $('select').select2();        

        $("#btn_new").on('click', function() {
            clear_inputs();
            $('#btn_submit').html("CREATE");
            
            $('.job_code_input').show();
            $('.job_code_edit').hide();
            $('#job_code').text("");

            $('#jobModal').modal('show');
        });

        $('#jobModal').on('shown.bs.modal', function () {
            $('#focus').focus();
        });

        $("#btn_back").on('click', function() {
            window.location.href = "/sac_job_control";
        });

        $("#btn_delete_all").on('click',function() {
            if(table.rows().eq(0).length > 0) {
                selectAllRows();

                url = '/sac_job_control/ajax/delete_job';
                showData("deleteAll", url);
            }
        });

        $("#btn_report").on('click', function() {
            $('#reportModal').modal('show');
        });

        // EDIT - Single Transaction
        $('#dt_').on('click', 'tbody .dt_edit', function () {
            var rowData = table.row($(this).closest('tr')).data();
            var rowID = rowData[0];
            //$(this).closest('tr').addClass('selected');
            
            $('.job_code_input').hide();
            $('.job_code_edit').show();            

            $('#job_id').val(rowID);
            $.post('/sac_job_control/ajax/details', {
                job_id: rowID
            }, function (job) {
                var obj = $.parseJSON(job);

                $('#job_code').text(obj.job['job_code']);

                $('#customer').select2("destroy");
                $('#customer').val(obj.job['customer_id']);
                $('#customer').select2();

                $('#financial_year').val(obj.job['financial_year']);

                $('#fy_start_date').val(getDate(obj.job['fy_start_date']));
                $('#fy_end_date').val(getDate(obj.job['fy_end_date']));

                $('#job_confirmed_date').val(getDate(obj.job['job_confirmed_date']));
                $('#promised_delivery_date').val(getDate(obj.job['promised_delivery_date']));
                $('#job_value').val(obj.job['job_value']);
                $('#payment_collected').val(obj.job['payment_collected']);
                calculate_balance_to_collect();

                $('#accountant').select2("destroy");
                $('#accountant').val(obj.job['accountant_id']);
                $('#accountant').select2();
                $('#assignment_date').val(getDate(obj.job['assignment_date']));
                $('#agreed_completion_date').val(getDate(obj.job['agreed_completion_date']));
                $('#actual_completion_date').val(getDate(obj.job['actual_completion_date']));

                $('#sales_input_target_date').val(getDate(obj.job['sales_input_target_date']));
                $('#receipt_input_target_date').val(getDate(obj.job['receipt_input_target_date']));
                $('#purchase_input_target_date').val(getDate(obj.job['purchase_input_target_date']));
                $('#payment_input_target_date').val(getDate(obj.job['payment_input_target_date']));

                $('#draft_accounts_completion_date').val(getDate(obj.job['draft_accounts_completion_date']));
                $('#final_accounts_completion_date').val(getDate(obj.job['final_accounts_completion_date']));
                $('#bank_reckon_completion_date').val(getDate(obj.job['bank_reckon_completion_date']));

                if(obj.job['tax_compilation'] == "1") {
                    $('#tax_compilation_yes').prop("checked", true);
                    $('.tax_compile').show();
                    $('#estimated_completion_date').val(getDate(obj.job['estimated_completion_date']));
                    $('#tax_completion_date').val(getDate(obj.job['tax_completion_date']));
                    $('#tax_compilation_fees').val(obj.job['tax_compilation_fees']);
                } else if(obj.job['tax_compilation'] == "0") {
                    $('#tax_compilation_no').prop("checked", true);
                    $('.tax_compile').hide();
                }

                if(obj.job['job_closed'] == "1") {
                    $('#job_closed_yes').prop("checked", true);
                } else if(obj.job['job_closed'] == "0") {
                    $('#job_closed_no').prop("checked", true);
                }
                
                $('#accountant_remarks').val(obj.job['accountant_remarks']);
                $('#manager_remarks').val(obj.job['manager_remarks']);

                $('#btn_submit').html("UPDATE");
                $('#jobModal').modal('show');
            });
        });

        // DELETE - Single Transaction
        $('#dt_').on('click', 'tbody .dt_delete', function () {
            var rowData = table.row($(this).closest('tr')).data();
            var rowID = rowData[0];
            console.log(rowID);

            $(this).closest('tr').addClass('selected');

            $.confirm({
                title: '<i class="fa fa-info"></i> Delete Job',
                content: 'Are you sure to delete?',
                buttons: {
                    yes: {
                        btnClass: 'btn-warning',
                        action: function() {
                            $.post('/sac_job_control/ajax/delete', {
                                sa_id: rowID
                            }, function (res) {
                                if(res == "error") {
                                    toastr.error("Delete Error!");
                                } else {
                                    toastr.success("Job Deleted!");
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

        $(document).on("change", "#customer_id", function() {
            var customer_id = $('option:selected', this).val();
            if(customer_id !== "" && $('#financial_year').val() !== "") {
                $('.duplicate_error').hide();
                duplicate();
                $('#financial_year').focus();
            } else if(customer_id !== "") {
                $('#financial_year').focus();
            }
        });

        $(document).on("change", "#financial_year", function() {
            $('.duplicate_error').hide();
            var customer_id = $("#customer_id").val();
            if(customer_id !== "" && $(this).val() !== "") {
                duplicate();
            }
        });

        $(document).on("change", "#job_value", function() {
            var amount = 0;
            if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
                var amount = parseFloat($(this).val()).toFixed(2);
                $(this).val(amount);
                calculate_balance_to_collect();
            }
        });

        $(document).on("change", "#payment_collected", function() {
            var amount = 0;
            if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
                var amount = parseFloat($(this).val()).toFixed(2);
                $(this).val(amount);
                calculate_balance_to_collect();
            }
        });

        $('input[type=radio][name=tax_compilation]').change(function() {
            $('#estimated_completion_date').val("");
            $('#tax_compilation_fees').val("");
            $('#tax_completion_date').val("");

            if (this.value == '1') {
                $('.tax_compile').show();
            } else if (this.value == '0') {
                $('.tax_compile').hide();
            }
        });

        $("#btn_submit").on('click', function (e) {
            if(!duplicate && isFormValid()) {
                save();
            }
        });

        $("#btn_detailed").on('click', function() {
            $('.kash').hide();
            $('#btn_print_kash').hide();
            $('#btn_print_detailed').show();
            
            $('#reportModal .card-header h5').html("Detailed Report");
            $('#reportModal').modal('show');
        });

        $("#btn_kash").on('click', function() {
            $('#btn_print_detailed').hide();

            $('.kash').show();
            $('#btn_print_kash').show();

            $('#reportModal .card-header h5').html("Kash Report");
            $('#reportModal').modal('show');
        });

        $('#reportModal').on('shown.bs.modal', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#accountant_id').select2("destroy").val("").select2();
            $('#third_party_cost').val('');
            $('#marketting_cost_percentage').val('');
            $('#job_all').prop("checked", true);
        });

        $("#btn_print_detailed").on('click', function() {
            $('#frm_').attr('action', '/sac_job_control/print_detailed');
            $('#frm_').attr("target", "_blank");
            $('#frm_').submit();
        });

        $("#btn_print_kash").on('click', function() {
            if($('#start_date').val() == "") {
                $('#start_date').focus();
            } else if($('#end_date').val() == "") {
                $('#end_date').focus();
            } else if($('#accountant_id').val() == "") {
                $("#accountant_id").select2('open');
            } else {
                $('#frm_').attr('action', '/sac_job_control/print_kash');
                $('#frm_').attr('target', '_blank');
                $('#frm_').submit();
            }
        });

        $(document).on('click', '.card', function() {
            $('#message_area').html('');
        });

    }); // document ends
   
    $(document).ajaxComplete(function(event, request, settings) {
        $(".btn").prop("disabled", false);
    });
    
    function clear_inputs() {
        $('#job_id').val('');
        $('#customer').select2("destroy").val("").select2();
        $('#financial_year').val('');
        $('#fy_start_date').val('');
        $('#fy_end_date').val('');

        $('#job_confirmed_date').val('');
        $('#promised_delivery_date').val('');
        $('#job_value').val('');
        $('#payment_collected').val('');
        $('#balance_to_collect').val('');

        $('#accountant').select2("destroy").val("").select2();
        $('#assignment_date').val('');
        $('#agreed_completion_date').val('');
        $('#actual_completion_date').val('');
        
        $('#sales_input_target_date').val('');
        $('#receipt_input_target_date').val('');
        $('#purchase_input_target_date').val('');
        $('#payment_input_target_date').val('');

        $('#draft_accounts_completion_date').val('');
        $('#final_accounts_completion_date').val('');
        $('#bank_reckon_completion_date').val('');

        $('#tax_compilation_yes').prop("checked", false);
        $('#tax_compilation_no').prop("checked", false);
        $('#estimated_completion_date').val('');
        $('#tax_completion_date').val('');
        $('#tax_compilation_fees').val('');

        $('#job_closed_yes').prop("checked", false);
        $('#job_closed_no').prop("checked", false);
        $('#accountant_remarks').val('');
        $('#manager_remarks').val('');
    }

    function isFormValid() {

        var valid = true;
        if($('#customer').val() == "") {
            $("#customer").select2('open');
            valid = false;
        } else if($('#financial_year').val() == "") {
            $('#financial_year').focus();
            valid = false;
        } else if($('#fy_start_date').val() == "") {
            $('#fy_start_date').focus();
            valid = false;
        } else if($('#fy_end_date').val() == "") {
            $('#fy_end_date').focus();
            valid = false;
        }  else if($('#job_confirmed_date').val() == "") {
            $('#job_confirmed_date').focus();
            valid = false;
        } else if($('#promised_delivery_date').val() == "") {
            $('#promised_delivery_date').focus();
            valid = false;
        } else if($('#job_value').val() == "") {
            $('#job_value').focus();
            valid = false;
        } else if($('#payment_collected').val() == "") {
            $('#payment_collected').focus();
            valid = false;
        } else if($('#accountant').val() == "") {
            $("#accountant").select2('open');
            valid = false;
        } else if($("input[name='tax_compilation']:checked").val() == undefined) {
            $('#rd_btns').focus();
            valid = false;
        } else if($("input[name='tax_compilation']:checked").val() == "1"  && $('#estimated_completion_date').val() == "") {
            $("#estimated_completion_date").focus();
            valid = false;
        } else if($("input[name='tax_compilation']:checked").val() == "1"  && $('#tax_completion_date').val() == "") {
            $("#tax_completion_date").focus();
            valid = false;
        } else if($("input[name='tax_compilation']:checked").val() == "1"  && $('#tax_compilation_fees').val() == "") {
            $("#tax_compilation_fees").focus();
            valid = false;
        } else if($("input[name='job_closed']:checked").val() == undefined) {
            $('#rd_btns').focus();
            valid = false;
        }

        return valid;
    }

    function duplicate() {
        $.post('/sac_job_control/ajax/duplicate', {
            customer_id: $("#customer").val(),
            financial_year: $('#financial_year').val()
        }, function(ref) {
            if (ref > 0) {
                duplicate = true;
                $('.duplicate_error').show();
                $('#financial_year').focus();
            } else {
                duplicate = false;
                $('.duplicate_error').hide();
            }
        });
    }

    function calculate_balance_to_collect() {
        var job_value = 0;
        var payment_collected = 0;
        var balance_to_collect = 0;

        job_value = $('#job_value').val();
        payment_collected = $('#payment_collected').val();

        if(job_value !== "" && payment_collected !== "") {
            balance_to_collect = Number(job_value) - Number(payment_collected);
            $('#balance_to_collect').val(balance_to_collect.toFixed(2));
        }
    }

    function getDate(dt_val) {
        if(dt_val !== null) {
            var parts = dt_val.split('-');
            var dt = parts[2]+"-"+parts[1]+"-"+parts[0];
            return dt;
        } else {
            return '';
        }
    }

    function save() {
        $.post('/sac_job_control/ajax/save', {
            job_id: $('#job_id').val(),
            customer_id: $('#customer').val(),
            financial_year: $('#financial_year').val(),
            fy_start_date: $('#fy_start_date').val(),
            fy_end_date: $('#fy_end_date').val(),
            job_confirmed_date: $('#job_confirmed_date').val(),
            promised_delivery_date: $('#promised_delivery_date').val(),
            job_value: $('#job_value').val(),
            payment_collected: $('#payment_collected').val(),
            accountant_id: $('#accountant').val(),
            assignment_date: $('#assignment_date').val(),
            agreed_completion_date: $('#agreed_completion_date').val(),
            actual_completion_date: $('#actual_completion_date').val(),
            sales_input_target_date: $('#sales_input_target_date').val(),
            receipt_input_target_date: $('#receipt_input_target_date').val(),
            purchase_input_target_date: $('#purchase_input_target_date').val(),
            payment_input_target_date: $('#payment_input_target_date').val(),
            draft_accounts_completion_date: $('#draft_accounts_completion_date').val(),
            final_accounts_completion_date: $('#final_accounts_completion_date').val(),
            bank_reckon_completion_date: $('#bank_reckon_completion_date').val(),
            tax_compilation: $("input[name='tax_compilation']:checked").val(),
            estimated_completion_date: $('#estimated_completion_date').val(),
            tax_completion_date: $('#tax_completion_date').val(),
            tax_compilation_fees: $('#tax_compilation_fees').val(),
            job_closed: $("input[name='job_closed']:checked").val(),
            accountant_remarks: $('#accountant_remarks').val(),
            manager_remarks: $('#manager_remarks').val()
        }, function(res) {
            if($.trim(res) == '1') {
                toastr.success("JOB Created!");
                table.ajax.reload();
                $('#jobModal').modal('hide');
            } else if($.trim(res) == 'updated') {
                toastr.success("JOB Updated!");
                table.ajax.reload();
                $('#jobModal').modal('hide');
            } else {
                toastr.error("Save error!");
            }
        });
    }
</script>
