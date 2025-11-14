<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">EZ Matrix</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item">EZ</li>
                <li class="breadcrumb-item active">Settlement</li>
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
                  <h5>AP Settlement</h5>
                  <a href="/ez_entry" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header">
                  <button class='btn btn-primary btn-sm' id="btn_new">
                     <i class="fa fa-plus-circle" aria-hidden="true"></i> New Settlement
                  </button>

                  <button class='btn btn-success btn-sm' id='btn_post_all'>
                     <i class='fa fa-check'></i> Post All
                  </button>
                  <button class='btn btn-danger btn-sm' id='btn_delete_all'>
                     <i class='fa fa-trash'></i> Delete All
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print_audit'>
                     <i class='fa fa-print'></i> Print Audit Trail
                  </button>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_batch" class="table table-hover" style="min-width: 1400px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Action</th>
                                 <th>Date</th>
                                 <th>Reference</th>
                                 <th>Supplier</th>
                                 <th>Currency</th>
                                 <th>Amount</th>
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

<!-- Entry Modal -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-6">
                        <label for="doc_date" class="control-label">Date<span class="cl-red">*</span></label>
                        <input type="text" name="doc_date" id="doc_date" class="form-control dp_full_date w-120" />
                    </div>
                    <div class="col-6">
                        <label for="doc_date" class="control-label">Reference<span class="cl-red">*</span></label>
                        <input 
                            type="text" 
                            name="ref_no" id="ref_no" 
                            maxlength="12" class="form-control" />
                        
                        <input 
                            type="hidden" 
                            id="original_ref_no" />
                        
                        <span class="error-ref error" style="display: none;">Duplicate reference disallowed</span>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-12">
                        <label for="supplier" class="control-label">Supplier<span class="cl-red">*</span></label>
                        <select id="supplier" name="supplier" class="form-control">
                            <?php echo $suppliers; ?>
                        </select>
                        <input 
                            type="hidden" 
                            id="original_supplier" />
                    </div>
                </div>

                <div class="row form-group dv_currency" style="display: none">
                    <div class="col-6">
                        <label for="doc_date" class="control-label">Currency</label>
                        <input type="text" name="currency" id="currency" class="form-control w-120" readonly />
                    </div>
                    <div class="col-6">
                        <label for="exchange_rate" class="control-label">Exchange Rate<span class="cl-red">*</span></label>
                        <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" />
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-6">
                        <label for="foreign_amount" class="control-label"><span class="f_curr"></span> Amount<span class="cl-red">*</span></label>
                        <input type="number" name="foreign_amount" id="foreign_amount" class="form-control" />
                    </div>
                    <div class="col-6 dv_local_amount" style="display: none">
                        <label for="local_amount" class="control-label">SGD Amount<span class="cl-red">*</span></label>
                        <input type="number" name="local_amount" id="local_amount" class="form-control" />
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <input type="text" name="remarks" id="remarks" class="form-control" />
                    </div>
                </div>
                
                <div class="row form-group">
                    <div class="col-12">
                        <label for="bank" class="control-label">Bank<span class="cl-red">*</span></label>
                        <select id="bank" name="bank" class="form-control select2">
                            <?php echo $banks; ?>
                        </select>
                    </div>
                </div>

                <div class="row form-group dv_foreign_bank" style="display: none">
                    <div class="col-12">
                        <label for="foreign_bank" class="control-label">Foreign Bank<span class="cl-red">*</span></label>
                        <select id="foreign_bank" name="foreign_bank" class="form-control">
                            <?php echo $foreign_banks; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
               <input type="hidden" name="entry_id" id="entry_id" />
               <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#entryModal">CANCEL</button>
               <button type="button" class="btn btn-primary btn-sm float-right" id="btn_submit">SUBMIT</button>
            </div>
         </div>
      </div>
   </div>
</div>

<!--Quick View Modal -->
<div id="quickViewModal" class="modal fade" data-backdrop="true">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
            </div>
            <div class="card-footer" style="text-align: center">
               <button type="button" id="btn_view_close" class="btn btn-secondary btn-sm">CLOSE</button>
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
   var duplicate_ref = false;
   
   $(function() {
      table = $('#dt_batch').DataTable({
         'processing': true,
         'ajax': {
            'url': '/ez_entry/datatable/ajax_list/batch_settlement',
            'type': 'post'
         },
         'columnDefs': [
            { targets: [6], className: 'dt-al-right' },
            { 'width': 170, 'targets': 1 },
            { 'width': 120, 'targets': 2 },
            { 'width': 140, 'targets': 3 },
            { 'width': 150, 'targets': 5 },
            { 'width': 200, 'targets': 6 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id

      $('#entryModal select').select2();

      $("#btn_new").on('click', function() {
         clear_inputs();
         $('#btn_submit').html("CREATE");
         $('#entryModal').modal('show');
      });

      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/ez_entry/ajax/delete_settlement';
            showData("deleteAll", url);
         }
      });

      $("#btn_post_all").on('click', function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/ez_entry/ajax/post_settlement';
            showData("postAll", url);
         }
      });

      $("#btn_print_audit").on('click', function() {
         if(table.rows().eq(0).length > 0) {
            url = '/ez_entry/print_settlement_audit_trail';
            print_all(url);
         }
      });
      

      // VIEW - Single Transaction
      $('#dt_batch').on('click', 'tbody .dt_view', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         console.log(rowID);

         $(this).closest('tr').addClass('selected');

         $.post('/ez_entry/ajax/get_settlement_view', {
            entry_id: rowID
         }, function (data) {
            var obj = $.parseJSON(data);

            $('#quickViewModal .card-body').html(obj.sales_data);
            $('#quickViewModal').modal('show');
         });
      });

      $('#btn_view_close').click(function() {
         table.ajax.reload();
         $('#quickViewModal').modal('hide');
      });

      // EDIT - Single Transaction
      $('#dt_batch').on('click', 'tbody .dt_edit', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         //$(this).closest('tr').addClass('selected');

         $('#entry_id').val(rowID);
         $.post('/ez_entry/ajax/get_settlement', {
            rowID: rowID
         }, function (settlement) {
            var obj = $.parseJSON(settlement);

            $('#doc_date').val(obj.settlement['doc_date']);
            $('#ref_no').val(obj.settlement['ref_no']);
            $('#original_ref_no').val(obj.settlement['ref_no']);
      
            $('#supplier').select2("destroy");
            $('#supplier').val(obj.settlement['supplier_id']);
            $('#supplier').select2();
            $('#original_supplier').val(obj.settlement['supplier_id']);

            $('#currency').val(obj.currency);
            $('#exchange_rate').val(obj.settlement['exchange_rate']);
            if(obj.currency == "SGD") {
               $('.dv_currency').hide();
               $('.dv_local_amount').hide();
            } else {
               $('.f_curr').text(obj.settlement['currency']);
               $('.dv_currency').show();
               $('.dv_local_amount').show();

               $('.dv_foreign_bank').show();
            }

            $('#foreign_amount').val(obj.settlement['foreign_amount']);
            $('#local_amount').val(obj.settlement['local_amount']);
            $('#remarks').val(obj.settlement['remarks']);

            $('#bank').select2("destroy");
            $('#bank').val(obj.settlement['bank_accn']);
            $('#bank').select2();

            $('#foreign_bank').select2("destroy");
            $('#foreign_bank').val(obj.settlement['fb_accn']);
            $('#foreign_bank').select2();

            if(obj.settlement['bank_accn'] == "CA110") {
               $('.dv_foreign_bank').show();
            } else {
               $('.dv_foreign_bank').hide();
            }

            $('#btn_submit').html("UPDATE");
            $('#entryModal').modal('show');
         });
      });

      // DELETE - Single Transaction
      $('#dt_batch').on('click', 'tbody .dt_delete', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         console.log(rowID);

         $(this).closest('tr').addClass('selected');

         $.confirm({
            title: '<i class="fa fa-info"></i> Delete Settlement',
            content: 'Are you sure to delete?',
               buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     $.post('/ez_entry/ajax/delete_settlement', {
                        rowID: rowID
                     }, function (res) {
                        if(res == "error") {
                           toastr.error("Delete Error!");
                        } else {
                           toastr.success("Settlement Deleted!");
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
      $('#dt_batch').on('click', 'tbody .dt_post', function () {
         $(this).closest('tr').addClass('selected');

         url = '/ez_entry/ajax/post_settlement';
         showData("postSingle", url);
      });

      $(document).on('click', '.card', function() {
         $('#message_area').html('');
      });

      $('#entryModal').on('shown.bs.modal', function() {
         if($('#entry_id').val() == "") {
            $('#doc_date').focus();
         }
      });

      $(document).on("change", "#ref_no", function() {
         if($(this).val() == "" || $('#supplier').val() == "") {
            return false;
         }

         double_ref();
      });

      $(document).on('change', '#supplier', function() {
         var supplier_id = $('option:selected', this).val();
         if(supplier_id == "") {
            return false;
         }

         $("#exchange_rate").val('');

         $.post('/ez_entry/ajax/get_supplier_details', {
            supplier_id: supplier_id
         }, function (data) {
            var obj = $.parseJSON(data);
            $("#currency").val(obj.currency);
            $("#exchange_rate").val(obj.currency_amount);
            $('.f_curr').text("");
            if(obj.currency == "SGD") {
               $('.dv_currency').hide();
               $('.dv_local_amount').hide();

               $('#bank').find('option:contains("CA110")').attr("disabled", true);
               $('#bank').val("CA101").trigger('change');
               $('#foreign_bank').val("").trigger('change');
               $('.dv_foreign_bank').hide();

            } else {
               $('.f_curr').text(obj.currency);
               $('.dv_currency').show();
               $('.dv_local_amount').show();

               $('#bank').find('option:contains("CA110")').attr("disabled", false);
               $('#bank').val("CA110").trigger('change');
               $('#foreign_bank').val("").trigger('change');
               $('.dv_foreign_bank').show();
            }

            if($('#foreign_amount').val() == "") {
               $('#foreign_amount').focus();
            } else {
               get_local_amount();
            }

            if($('#ref_no').val() !== "") {
               double_ref();
            }
            
         });
      });

      $(document).on('change', '#bank', function() {
         var accn = $('option:selected', this).val();
         if(accn == "CA110") {
            $('.dv_foreign_bank').show();
         } else {
            $('.dv_foreign_bank').hide();
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
   
      $(document).on("change", "#exchange_rate", function() {
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            $(this).val(Number($(this).val()).toFixed(5));
            get_local_amount();
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

      $("#btn_submit").on('click', function (e) {

         if(duplicate_ref) {
            $('#ref_no').focus();
         } else if(isFormValid()) {
            save();
         }
      });

   }); // document ends
   

   function clear_inputs() {
      $('#entry_id').val('');

      $('#doc_date').val('');
      $('#ref_no').val('');
      $('.error-ref').hide();

      $('#supplier').select2("destroy");
      $('#supplier').val("");
      $('#supplier').select2();

      $('#currency').val('');
      $('#exchange_rate').val('');
      $('.dv_currency').hide();
      $('.dv_local_amount').hide();

      $('#foreign_amount').val('');
      $('#local_amount').val('');

      $('#bank').val("").trigger('change');
      $('#foreign_bank').val("").trigger('change');
      $('.dv_foreign_bank').hide();

      $('#remarks').val('');
   }

   function double_ref() {
      duplicate_ref = false;
      $(".error-ref").hide();

      var ref_no = $('#ref_no').val();
      var supplier = $('#supplier').val();

      // if page is edit and user try changing different ref and again changing to same one
      if(ref_no == $('#original_ref_no').val() && supplier == $('#original_supplier').val()) {
         return false;
      }

      $.post('/ez_entry/ajax/double_settlement', {
         ref_no: ref_no,
         supplier_id: supplier
      }, function(ref) {
         if (ref > 0) {
            duplicate_ref = true;
            $(".error-ref").show();
         }
      });
   }

   function isFormValid() {
      var valid = true;
      if($('#doc_date').val() == "") {
         $('#doc_date').focus();
         valid = false;
      } else if($('#ref_no').val() == "") {
         $('#ref_no').focus();
         valid = false;
      } else if($('#supplier').val() == "") {
         $("#supplier").select2('open');
         valid = false;
      } else if($('#foreign_amount').val() == "") {
         $('#foreign_amount').focus();
         valid = false;
      }  else if($('#local_amount').val() == "") {
         $('#local_amount').focus();
         valid = false;
      } else if($('#bank').val() == "") {
         $("#bank").select2('open');
         valid = false; 
      } else if($('#bank').val() == "CA110" && $('#foreign_bank').val() == "") {
         $("#foreign_bank").select2('open');
         valid = false;
      }

      return valid;
   }

   function get_local_amount() {
      var exchange_rate = $("#exchange_rate").val();
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
      $.post('/ez_entry/ajax/save_settlement', {
         entry_id: $('#entry_id').val(),
         doc_date: $('#doc_date').val(),
         ref_no: $('#ref_no').val(),
         supplier_id: $('#supplier').val(),
         foreign_amount: $('#foreign_amount').val(),
         exchange_rate: $('#exchange_rate').val(),
         local_amount: $('#local_amount').val(),
         bank: $('#bank').val(),
         foreign_bank: $('#foreign_bank').val(),
         remarks: $('#remarks').val()
      }, function(res) {
         if($.trim(res) == '1') {
            toastr.success("CREATED");
            table.ajax.reload();
            $('#entryModal').modal('hide');
         } else if($.trim(res) == 'updated') {
            toastr.success("UPDATED");
            table.ajax.reload();
            $('#entryModal').modal('hide');
         } else {
            toastr.error("ERROR");
         }
      });
   }
</script>
