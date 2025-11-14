<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Foreign Bank</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Opening Balance</li>
               <li class="breadcrumb-item active">Manage</li>
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

      <div class="row">
         <div class="col-lg-12">
            <div class="card card-default">
               <div class="card-header">
                  <h5>Opening Balance</h5>
                  <a href="/foreign_bank/" class="btn btn-outline-dark btn-sm float-right">
                    <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header buttons-panel">
                  <button class='btn btn-primary btn-sm' id="btn_create">
                     <i class="fa fa-plus-circle" aria-hidden="true"></i> Create
                  </button>
                  <button class='btn btn-success btn-sm' id='btn_post_all'>
                     <i class='fa-solid fa-check-double'></i> Post All
                  </button>
                  <button class='btn bg-maroon btn-sm' id='btn_delete_all'>
                     <i class='fa fa-trash'></i> Delete All
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print_all'>
                     <i class='fa fa-print'></i> Print All
                  </button>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1400px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Action</th>
                                 <th>Date</th>
                                 <th>Reference</th>
                                 <th>Bank & Currency</th>
                                 <th>Foreign Amount</th>
                                 <th>SGD Amount</th>
                                 <th>Remarks</th>
                                 <th>Sign</th>
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
            </form>

         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- New / Edit Transaction Modal -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="entry_id" />
               <div class="row form-group">
                  <div class="col-12">
                     <label for="bank" class="control-label">Bank<span class="cl-red">*</span></label>
                     <!-- Field : Bank Options -->
                     <select id="bank" class="form-control">
                        <?php echo $banks; ?>
                     </select>
                  </div>
               </div>

               <div class="row form-group dv_currency" style="display: none">
                    <div class="col-6">
                        <label for="doc_date" class="control-label">Currency</label>
                        <input type="text" id="currency" class="form-control w-120" readonly />
                    </div>
                    <div class="col-6">
                        <label for="currency_rate" class="control-label">Exchange Rate<span class="cl-red">*</span></label>
                        <input type="number" id="currency_rate" class="form-control" readonly />
                    </div>
                </div>

               <hr class="entry_field" style="display: none" />

               <div class="row form-group entry_field" style="display: none">
                  <div class="col-6">
                     <label for="doc_date" class="control-label">Date<span class="cl-red">*</span></label>
                     <input type="text" id="doc_date" class="form-control dp_full_date w-120" />
                  </div>
                  <div class="col-6">
                     <label for="doc_date" class="control-label">Reference<span class="cl-red">*</span></label>
                     <input type="text" id="ref_no" class="form-control" maxlength="12" />
                     <span class="error-ref error" style="display: none;">Duplicate reference disallowed</span>
                  </div>
               </div>

               <div class="row form-group entry_field" style="display: none">
                  <div class="col-6">
                     <label for="foreign_amount" class="control-label"><span class="f_curr"></span> Amount<span class="cl-red">*</span></label>
                     <input type="number" id="foreign_amount" class="form-control" />
                  </div>
                  <div class="col-6 dv_local_amount" style="display: none">
                     <label for="local_amount" class="control-label">SGD Amount<span class="cl-red">*</span></label>
                     <input type="number" id="local_amount" class="form-control" />
                  </div>
               </div>
                
               <div class="row form-group entry_field" style="display: none">
                  <div class="col-12">
                     <label for="remarks" class="control-label">Remarks</label>
                     <input type="text" name="remarks" id="remarks" class="form-control" />
                  </div>
               </div>

               <hr class="entry_field" style="display: none" />               

               <div class="row form-group entry_field" style="display: none">
                  <div class="col-3">
                     <label class="control-label">Entry <span class="cl-red">*</span></label>
                  </div>
                  <div class="col-9">
                     <input type="radio" id="entry_debit" name="entry" value="+" class="radio-inp" autocomplete="off" checked="checked">
                     <label class="radio-lbl" for="entry_debit">DEBIT</label>
                     <input type="radio" id="entry_credit" name="entry" value="-" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="entry_credit">CREDIT</label>
                  </div>
               </div>

            </div>
            <div class="card-footer">               
               <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#entryModal">CANCEL</button>
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
            'url': '/foreign_bank/ajax/populate_ob',
            'type': 'post'
         },
         'columnDefs': [
            { targets: [5, 6], className: 'dt-al-right' },
            { 'width': 150, 'targets': 1 },
            { 'width': 110, 'targets': 2 },
            { 'width': 110, 'targets': 3 },
            { 'width': 250, 'targets': 4 },
            { 'width': 150, 'targets': 5 },
            { 'width': 150, 'targets': 6 },
            { 'width': 50, 'targets': 8 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id

      $('#entryModal select').select2();

      $("#btn_create").on('click', function() {
         clear_inputs();
         $('#btn_submit').html("CREATE");
         $('#entryModal').modal('show');
      });

      // bank select
      $(document).on("change", "#bank", function() {
         code = $('option:selected', this).val();
         if (code !== "") {
            $.post('/foreign_bank/ajax/get_bank', {
               code: code
            }, function(data) {
               var obj = $.parseJSON(data);
               $('#currency').val(obj.currency);
               $('#currency_rate').val(obj.currency_rate);

               if(obj.currency == "SGD") {
                  $('.dv_currency').hide();
                  $('.dv_local_amount').hide();
               } else {
                  $('.f_curr').text(obj.currency);
                  $('.dv_currency').show();
                  $('.dv_local_amount').show();
               }

               $('.entry_field').show();
               $('#doc_date').focus();
            });
         }
      });

      // This is commented since David requested that "User is allowed to enter opening balance for 2 or more foreign banks under same reference on Feb 08, 2021"
      var double_ref = 0;
      $(document).on("change", "#ref_no_NOT_USED", function() {
         var ref_no = $(this).val();
         double_ref = 0;
         
         $("#ref_error").hide();

         if(ref_no !== "") {

            // if page is edit and user try changing different ref and again changing to same one
            if(ref_no == $('#original_ref_no').val()) {
               return false;
            }

            // Double Check Reference in FB_Open.TBL
            $.post('/foreign_bank/ajax/double_ref', {
               ref_no: $("#ref_no").val()
            }, function(ref) {
               if (ref > 0) {
                  double_ref = 1;
                  $("#ref_error").show();
               } else {
                  double_ref = 0;
                  $("#ref_error").hide();
               }
            });
         }
      });

      $(document).on("keyup", "#foreign_amount", function() {
         if($(this).val() !== "") {
            get_local_amount();
         }
      });

      $(document).on("change", "#foreign_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         } else {
            $("#local_amount").val('');
         }
      });

      $(document).on("change", "#local_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      // add entry to transaction
      $("#btn_save_item").on('click', function () {

         if(!isEntryValid()) {
            return false;
         }

         if(double_ref == 1) {
            $('#ref_no').focus();
            return false;
         }

         save();
      });

      // EDIT - Single Transaction
      $('#dt_').on('click', 'tbody .dt_edit', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         //$(this).closest('tr').addClass('selected');

         $('#entry_id').val(rowID);
         $.post('/foreign_bank/ajax/get_ob', {
            rowID: rowID
         }, function (data) {
            var obj = $.parseJSON(data);

            $('#bank').select2("destroy");
            $('#bank').val(obj.ob['fb_code']);
            $('#bank').select2();

            $('#doc_date').val(obj.ob['document_date']);
            $('#ref_no').val(obj.ob['document_reference']);

            $('#foreign_amount').val(obj.ob['foreign_amount']);
            $('#local_amount').val(obj.ob['local_amount']);

            $('#remarks').val(obj.ob['remarks']);

            if(obj.ob['sign'] == "+") {
               $('#entry_debit').prop("checked", true);
            } else if(obj.ob['sign'] == "-") {
               $('#entry_credit').prop("checked", true);
            }

            $('#currency').val(obj.currency);
            $('#currency_rate').val(obj.currency_rate);
            if(obj.currency == "SGD") {
               $('.dv_currency').hide();
               $('.dv_local_amount').hide();
            } else {
               $('.f_curr').text(obj.currency);
               $('.dv_currency').show();
               $('.dv_local_amount').show();
            }

            $('.entry_field').show();

            $('#btn_submit').html("UPDATE");
            $('#entryModal').modal('show');
         });
      });

      // DELETE - Single Transaction
      $('#dt_').on('click', 'tbody .dt_delete', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         console.log(rowID);

         $(this).closest('tr').addClass('selected');

         $.confirm({
            title: '<i class="fa fa-info"></i> Delete Transaction',
            content: 'Are you sure to delete?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     $.post('/foreign_bank/ajax/delete_ob', {
                        rowID: rowID
                     }, function (res) {
                        if(res == "error") {
                           toastr.error("Delete Error!");
                        } else {
                           toastr.success("Deleted!");
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
               },
            }
         });
      });
               
      // Post - Single Transaction
      $('#dt_').on('click', 'tbody .dt_post', function () {
         $(this).closest('tr').addClass('selected');

         url = '/ez_entry/ajax/post_ob';
         showData("postSingle", url);
      });

      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/foreign_bank/ajax/delete_ob';
            showData("deleteAll", url);
         }
      });

      $("#btn_post_all").on('click', function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/foreign_bank/ajax/post_ob';
            showData("postAll", url);
         }
      });	

      $("#btn_print_all").click(function() {
         if(table.rows().eq(0).length > 0) {
            var url = '/foreign_bank/print_ob';
            print_all(url);
         }
      });

      $("#btn_submit").on('click', function (e) {
         if(double_ref == 0 && isEntryValid()) {
            save();
         }
      });

   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });

   function clear_inputs() {
      $('.entry_field').hide();
      
      $('#entry_id').val('');

      $('#bank').select2("destroy").val("").select2();
      $('#currency').val('');
      $('#currency_rate').val('');

      $('#doc_date').val('');
      $('#ref_no').val('');
      $('.error-ref').hide();

      $('.dv_currency').hide();
      $('.dv_local_amount').hide();

      $('#foreign_amount').val('');
      $('#local_amount').val('');

      $('#remarks').val('');
      $('#entry_debit').prop("checked", true);
   }

   function isEntryValid() {
      var valid = true;
      if($('#bank').val() == "") {
         $("#bank").select2('open');
         valid = false;
      } else if($('#doc_date').val() == "") {
         $('#doc_date').focus();
         valid = false;
      } else if($('#ref_no').val() == "") {
         $('#ref_no').focus();
         valid = false;        
      } else if($('#foreign_amount').val() == "") {
         $('#foreign_amount').focus();
         valid = false;
      }  else if($('#local_amount').val() == "") {
         $('#local_amount').focus();
         valid = false;
      }

      return valid;
   }

   function get_local_amount() {
      var exchange_rate = $("#currency_rate").val();
      var famt = $('#foreign_amount').val();
      
      var local_amount = 0;

      if(exchange_rate !== "" && famt !== "") {
         local_amount = Number(famt) / Number(exchange_rate);

         $('#local_amount').val(local_amount.toFixed(2));
      } else {
         $('#local_amount').val('');
      }
   }

   function save() {
      var entry_id = $("#entry_id").val();
      var bank = $("#bank").val();

      var doc_date = $("#doc_date").val();
      var ref_no = $("#ref_no").val();
      
      var foreign_amount = $("#foreign_amount").val();
      var local_amount = $("#local_amount").val();
      var remarks = $("#remarks").val();

      var entry = $("input[name='entry']:checked").val();

      $.post('/foreign_bank/ajax/save_ob', {
         entry_id: entry_id,
         bank: bank,
         doc_date: doc_date,
         ref_no: ref_no,
         foreign_amount: foreign_amount,
         local_amount: local_amount,
         remarks: remarks,
         sign: entry
      }, function(res) {
         if($.trim(res) == '1') {
            toastr.success("Opening Balance Created!");
            table.ajax.reload();
            $('#entryModal').modal('hide');
         } else if($.trim(res) == 'updated') {
            toastr.success("Opening Balance Updated!");
            table.ajax.reload();
            $('#entryModal').modal('hide');
         } else {
            toastr.error("Save error!");
         }
      });
   }
</script>
