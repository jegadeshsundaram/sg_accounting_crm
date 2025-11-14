<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Chart of Account</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Coa</li>
               <li class="breadcrumb-item active">Listing</li>
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
                  <h5>Listing</h5>
                  <a href="/general_ledger/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-header">
                  <button class='btn btn-outline-dark btn-sm' id='btn_back'>
                     <i class="fa-solid fa-angles-left"></i> Back
                  </button>
                  <button class='btn btn-primary btn-info btn-sm' id='btn_new'>
                     <i class='fa fa-plus-circle'></i> New
                  </button>
                  <button class='btn btn-primary btn-sm' id='btn_edit'>
                     <i class='fa fa-pencil'></i> Edit
                  </button>                  
                  <button class='btn bg-maroon btn-sm' id='btn_delete'>
                     <i class='fa fa-trash'></i> Delete
                  </button>        
                  <button class='btn bg-navy btn-sm' id='btn_print'>
                     <i class='fa fa-print'></i> Print
                  </button>                  
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1400px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Code</th>
                                 <th>Description</th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div> <!-- Card - ends -->

            <form id="print_form" method="get" action="#">
               <input type="hidden" name="rowID" id="rowID" />
               <input type="hidden" name="ob_type" id="ob_type" value="<?php echo $this->uri->segment(3); ?>" />  
            </form>

         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Modal :: Add COA -->
<div id="coaModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_coa" action="#" method="POST" autocomplete="off">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Add</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Prefix with 2 characters and suffix with 3 digits, Ex: CA001</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#coaModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <div class="row">
                           <label for="accn" class="col-md-12 control-label">Account : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="accn" name="accn"
                                 maxlength="5"
                                 class="form-control w-120" required />
                              <span class="accn_duplicate" style="color: red; display: none;">Duplicate account disallowed</span>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <div class="row">
                           <label for="description" class="col-md-12 control-label">Description : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="description" name="description"
                                 class="form-control" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <input type="hidden" id="coa_id" name="coa_id" />
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#coaModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_save">Save</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Sort by Account or Description -->
<div id="sortModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_print" action="#" method="GET" autocomplete="off">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Print Accounts</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Print all the chart of accounts in PDF format</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#sortModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <label class="control-label mb-10" style="margin-top: 5px; margin-bottom: 5px !important;">Sort By</label>
                        <input type="radio" id="accn_code" name="sort_by" value="accn" class="radio-inp" autocomplete="off" checked="checked">
                        <label class="radio-lbl" for="accn_code">CODE</label>
                        <input type="radio" id="accn_desc" name="sort_by" value="description" class="radio-inp" autocomplete="off">
                        <label class="radio-lbl" for="accn_desc">DESCRIPTION</label>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#sortModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_pdf" style="margin-left: 10px">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/assets/js/datatable.js"></script>

<style>
   .control-label {
      display: flex;
   }
</style>
<script type="text/javascript">
   var url = '';
   var page = '';
   $(function() {

      table = $('#dt_').DataTable({
         'processing': true,
         'ajax': {
            'url': '/general_ledger/ajax/populate_coa',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 80, 'targets': 1 },
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id 

      $("#btn_new").click(function() {
         page = '';
         $('#coaModal').modal();
      });

      $('#coaModal').on('shown.bs.modal', function () {
         if(page == '') {
            $('#accn').val("");
            $('#description').val("");
            $('#coa_id').val("");
            $('#accn').focus();
         }
      });

      var same_account_exists = false
      var is_format_valid = true;
      var is_prefix_valid = true;
      
      $(document).on("change", "#accn", function(e) {
         accn = $(this).val();
         if(accn !== "") {

            accn_prefix = accn.substring(0, 2);
            accn_suffix = accn.substring(2, 5);

            let regex = /^[a-zA-Z]+$/; 

            if(accn.length !== 5) {
               is_format_valid = false;

               $('#errorAlertModal .modal-title').html("Format Error");
               $('#errorAlertModal .modal-body').html("Prefix: 2 Characters / Digits + Suffix: 3 Digits (Eg: XX000)");
               $('#errorAlertModal').modal();

            } else if (!regex.test(accn.substring(0, 1))) {
               is_format_valid = false;

               $('#errorAlertModal .modal-title').html("Format Error");
               $('#errorAlertModal .modal-body').html("Prefix: 2 Characters / Digits + Suffix: 3 Digits (Eg: XX000)");
               $('#errorAlertModal').modal();

            } else if (isNaN(accn_suffix)) {
               is_format_valid = false;

               $('#errorAlertModal .modal-title').html("Format Error");
               $('#errorAlertModal .modal-body').html("Prefix: 2 Characters / Digits + Suffix: 3 Digits (Eg: XX000)");
               $('#errorAlertModal').modal();

            } else {
               is_format_valid = true;
               $.post('/general_ledger/ajax/double_accn', {
                  accn_prefix: accn_prefix,
                  accn: $(this).val()
               }, function(data) {
                  if (data == 2) {
                     is_prefix_valid = false;
                     $('#errorAlertModal .modal-title').html("COA Prefix");
                     $('#errorAlertModal .modal-body').html(accn_prefix+' Series Not Available');
                     $('#errorAlertModal').modal();

                  } else if (data == 1) {
                     $('.accn_duplicate').show();
                     same_account_exists = true;
                  } else {
                     same_account_exists = false;
                     is_prefix_valid = true;
                     $('.accn_duplicate').hide();
                  }
               });
            }
         } else {
            $('.accn_duplicate').hide();
         }
      });

      $("#btn_save").click(function() {
         if(!$('#frm_coa').valid()) {
            return;
         }

         if(!is_format_valid) {
            $('#errorAlertModal .modal-title').html("Format Error");
            $('#errorAlertModal .modal-body').html('2 Characters (Prefix) + 3 Digits (Suffix) (Eg: XX000)');
            $('#errorAlertModal').modal();
         } else if(!is_prefix_valid) {
            $('#errorAlertModal .modal-title').html("Prefix Error");
            $('#errorAlertModal .modal-body').html('No Account Series in this Prefix');
            $('#errorAlertModal').modal();
         } else {
            if($("#frm_coa").valid() && same_account_exists) {
               $(".accn_duplicate").show();
            } else {
               $.post('/general_ledger/ajax/save_accn', {
                  coa_id: $('#coa_id').val(),
                  accn: $('#accn').val(),
                  description: $('#description').val(),
               }, function(data) {
                  $('#coaModal').modal('hide');
                  if (data == 0) {
                     $('#dt_').DataTable().ajax.reload();
                     $("#message_area").html("<div class='alert alert-success fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>Account Saved</div>");
                  } else {
                     $('#dt_').DataTable().ajax.reload();
                     $("#message_area").html("<div class='alert alert-warning fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>Save Error</div>");
                  }
               });
            }
         }
      });

      $(".card").click(function() {
         $("#message_area").html("");
      });
    
      $("#btn_back").on('click', function() {
         url = "/general_ledger";
         showData("back", url);
      });

		$("#btn_edit").on('click',function() {
         var rowData = table.row('.selected').data();
         if(rowData) {
            page = 'edit';
            $('#coa_id').val(rowData[0]);
            $('#accn').val(rowData[1]);
            $('#description').val(rowData[2]);
            $('#coaModal').modal();
         } else {
            toastr.error("Please select account to edit", 'Error');
         }
		});

      $("#btn_delete").on('click',function() {
         var rowData = table.row('.selected').data();
         if(rowData) {
            if(rowData[1] == "CA001" || rowData[1] == "CL001" || rowData[1] == "CA003") {
               $('#errorAlertModal .modal-title').html("Control Account");
               $('#errorAlertModal .modal-body').html('Control Account can not be deleted!');
               $('#errorAlertModal').modal();
            } else {
               var url = '/general_ledger/ajax/delete_accn';
               showData("delete", url);
            }
         } else {
            toastr.error("Please select account to delete", 'Error');
         }
      });

      $('#btn_print').on('click', function() {
         $('#sortModal').modal();
      });

      $('#btn_print_pdf').on('click', function() {
         $('#sortModal').modal('hide');
         var url = "/general_ledger/print_coa";
         $("#frm_print").attr("action", url);
         $("#frm_print").attr("target", "_blank");
         $("#frm_print").submit();
      });

   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });
</script>