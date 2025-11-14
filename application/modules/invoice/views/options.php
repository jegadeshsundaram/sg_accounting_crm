<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Invoice</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item active">Invoice</li>

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
            <input type="hidden" id="module" value="invoice" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#settingsModal">
                           Settings <span>Settings of Invoice Template</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#optionsModal">
                           Create <span>Create Invoice by itemized entries or extract quotatio for any customer</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/invoice/listing">
                           Listing <span>Confirmed / Posted / Deleted Invoices Listings</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/invoice/other_listing">
                           Other Listing <span>AR / GL / GST / Stock Listings</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/invoice/customer_price">
                           Customer Special Price <span>Add special price for any customer</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of Invoice & Special Price Datafile's</span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/dashboard" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Dashboard
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<div id="settingsModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">SETTINGS</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 15px; right: 20px;" data-toggle="modal" data-target="#settingsModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <div class="col-6">
                     <div class="row">
                        <label for="text_prefix" class="col-12 control-label float-right">Text Prefix <span class="req">*</span><span class="txt">{Max: 4 Characters}</span></label>
                        <div class="col-12">
                           <input type="text" id="text_prefix" maxlength="4" class="form-control w-120" />
                        </div>
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="row">
                        <label for="number_suffix" class="col-12 control-label float-right">Number Suffix <span class="req">*</span><span class="txt">{Max: 6 digits}</span></label>
                        <div class="col-12">
                           <input type="number" id="number_suffix" class="form-control w-120" onkeypress="if(this.value.length==6) return false;" />
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="header_notes" class="col-12 control-label">Header Notes</label>
                  <div class="col-12">
                     <textarea id="header_notes" class="form-control"></textarea>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="footer_notes" class="col-12 control-label">Footer Notes</label>
                  <div class="col-12">
                     <textarea id="footer_notes" class="form-control"></textarea>
                  </div>
               </div>
               
            </div>
            <div class="card-footer">
               <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#settingsModal">CANCEL</a>
               <button type="button" name="btn_submit" id="btn_submit" class="btn btn-warning btn-sm float-right">SUBMIT</button>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="optionsModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">OPTIONS</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 15px; right: 20px;" data-toggle="modal" data-target="#optionsModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <div class="col-12">
                     <a href="/invoice/create" class="btn btn-block btn-outline-primary">
                        Itemized Entries <span style="color: dimgray">Create Invoice by Itemized Entries</span>
                     </a>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <div class="col-12">
                     <a class="btn btn-block btn-outline-primary btn_quotation_select">
                        Extract Quotation <span style="color: dimgray">Create Invoice by Extract Any Quotation</span>
                     </a>
                  </div>
               </div>
               
            </div>
            <div class="card-footer center">
               <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#optionsModal">EXIT</a>               
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Billing Modal - starts -->
<div id="quotationsModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm modal-dialog-scrollable">
      <div class="modal-content">         
         <div class="modal-body table-responsive">
            <table id="dt_" class="table table-hover" style="width: 100%">
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>Reference</th>
                     <th>Customer</th>
                  </tr>
               </thead>
               <tbody></tbody>
            </table>
         </div>
         <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" data-target="#quotationsModal">Cancel</button>
            <button type="button" id="btn_extract_quotation" class="btn btn-danger btn-sm float-right">Extract</button>
         </div>
      </div>
   </div>
</div>
<!-- Billing Modal - ends -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="/assets/js/datatable.js"></script>

<?php require_once APPPATH.'/modules/includes/modal/utility.php'; ?>
<script src="/assets/js/modal/utility.js"></script>

<script type="text/javascript">

   var same_reference_exists = 0;
   $(function() {

      $('#settingsModal').on('shown.bs.modal', function() {
         $.post('/invoice/ajax/get_settings', {
         }, function (settings) {
            var obj = $.parseJSON(settings);

            $('#text_prefix').val(obj.settings['text_prefix']);
            $('#number_suffix').val(obj.settings['number_suffix']);
            $('#header_notes').val(obj.settings['header_notes']);
            $('#footer_notes').val(obj.settings['footer_notes']);

            if($('#text_prefix').val() == "") {
               $('#text_prefix').focus();
            }
         });
      });

      $(document).on("change", "#text_prefix, #number_suffix", function(e) {
         var text_prefix = $('#text_prefix').val();
         var number_suffix = $('#number_suffix').val();
         $.post('/invoice/ajax/double_invoice', {
            text_prefix: text_prefix, 
            number_suffix: number_suffix
         }, function(data) {
            if (data == 1) {
               same_reference_exists = 1;
               $('.suffix_error').text('Invoice reference ' + text_prefix + '.' + (parseInt(number_suffix) + 1) +' is already in the system, please change suffix number.');
               $('.suffix_error').show();
            } else {
               same_reference_exists = 0;
               $('.suffix_error').text('');
               $('.suffix_error').hide();
            }
         });
      });

      $('#btn_submit').click(function() {
         if(same_reference_exists == 0 && isFormValid()) {
            $.post('/invoice/ajax/save_settings', {
               text_prefix: $('#text_prefix').val(),
               number_suffix: $('#number_suffix').val(),
               header_notes: $('#header_notes').val(),
               footer_notes: $('#footer_notes').val()
            }, function(res) {
               toastr.success(res);
               $('#settingsModal').modal('hide');
            });
         }
      });

      // Extraction Quotation
      // Populate Quotations in Modal Datatable for user to find and select any Quotaton and create Invoice
      table = $('#dt_').DataTable({
         'processing': true,
         "ajax": {
            'url': "/invoice/ajax/get_quotations",
            'type': 'GET'
         },
         'columnDefs': [
            { 'width': 150, 'targets': 1 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // quotation_id

      // open billing modal with data table displaying products and services
      $(document).on('click', '.btn_quotation_select', function() {
         clearDTSearch();
         $('#dt_').DataTable().ajax.reload();

         $("#optionsModal").modal('hide');
         $("#quotationsModal").modal();
      });

      // process the selected product or service
      $('#btn_extract_quotation').click(function () {
         rowData = table.row('.selected').data();
         if(rowData) {
            quotation_id = rowData[0];
            quotation_ref_no = rowData[1];
            customer_name_code = rowData[2]

            $("#quotationsModal").modal('hide');

            var url = '/invoice/extract_quotation/';
			   showData("edit", url);
         }
      });
 
   }); // document ends

   function isFormValid() {
      var valid = true;
      if($('#text_prefix').val() == "") {
         $('#text_prefix').focus();
         valid = false;
      } else if($('#number_suffix').val() == "") {
         $('#number_suffix').focus();
         valid = false;
      }

      return valid;
   }
</script>
