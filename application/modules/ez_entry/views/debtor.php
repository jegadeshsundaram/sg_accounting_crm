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
               <li class="breadcrumb-item active">Debtor</li>
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
                  <h5 style="float: left; margin-top: 8px;">Debtor</h5>
                  <button class='btn btn-outline-secondary btn-sm float-right' id='btn_back'>Back</button>
               </div>
               
               <div class="card-header">
                    <button class='btn btn-primary btn-sm' id='btn_process_contra'>
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Process Contra
                    </button>
                    <button class='btn btn-success btn-sm' id='btn_post_all'>
                        <i class='fa fa-check'></i> Post All
                    </button>
                    <button class='btn btn-danger btn-sm' id='btn_delete_all'>
                        <i class='fa fa-trash'></i> Delete All
                    </button>
                    <button class='btn bg-navy btn-sm' id='btn_print_statement'>
                        <i class='fa fa-print'></i> Print Statement
                    </button>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_batch" class="table table-hover" style="min-width: 600px; width: 100%">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Action</th>
                                 <th>Customer</th>
                                 <th>Currency</th>
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

<!-- Debtor Statement Print - Modal -->
<div id="debtorModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_" method="GET">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-body">
                  <div class="row mt-10">
                     <div class="col-12">
                        <label class="control-label">Customer<span class="cl-red">*</span></label><br />
                        <select id="customer" name="customer" class="form-control">
                           <?php echo $customers; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#debtorModal">Cancel</button>
                  <button type="button" class="btn btn-info btn-sm float-right" id="btn_print">Print</button>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
   var url = '';
   $(function() {

      table = $('#dt_batch').DataTable({
         'processing': true,
         'ajax': {
            'url': '/ez_entry/datatable/ajax_list/debtor',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 140, 'targets': 1 },
            { 'width': 350, 'targets': 2 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id

      $('#debtorModal select').select2();

      $("#btn_process_contra").on('click', function() {
         window.location.href = "/ez_entry/debtor_contra";
      });

      $("#btn_back").on('click', function() {
         window.location.href = "/ez_entry";
      });

      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/ez_entry/ajax/delete_debtor_contra';
            showData("deleteAll", url);
         }
      });

      $("#btn_post_all").on('click', function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/ez_entry/ajax/post_debtor_contra';
            showData("postAll", url);
         }
      });

      $("#btn_print_statement").on('click', function() {
         $('#debtorModal').modal('show');
      });

      $("#btn_print").on('click', function() {
         if($('#customer').val() == "") {
            $("#customer").select2('open');
         } else {
            $("#frm_").attr("action", '/ez_entry/print_debtor');
            $("#frm_").attr("target", "_blank");
            $("#frm_").submit();
         }
      });

      // DELETE - Single Transaction
      $('#dt_batch').on('click', 'tbody .dt_delete', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         console.log(rowID);

         $(this).closest('tr').addClass('selected');

         $.confirm({
            title: '<i class="fa fa-info"></i> Delete Debtor',
            content: 'Are you sure to delete?',
            buttons: {
            yes: {
               btnClass: 'btn-warning',
               action: function() {
                     $.post('/ez_entry/ajax/delete_debtor_contra', {
                        rowID: rowID
                     }, function (res) {
                        if(res == "error") {
                           toastr.error("Delete Error!");
                        } else {
                           toastr.success("Debtor Deleted!");
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

         url = '/ez_entry/ajax/post_debtor_contra';
         showData("postSingle", url);
      });

      $(document).on('click', '.card', function() {
         $('#message_area').html('');
      });

   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });
</script>
