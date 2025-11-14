<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0"><span class="title"></span> Listing</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Invoice</li>
               <li class="breadcrumb-item active">List</li>
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
                  <button class='btn btn-outline-dark btn-sm' id='btn_back'>
                     <i class="fa-solid fa-angles-left"></i> Back
                  </button>                  
                  <button class='btn bg-navy btn-sm' id='btn_print_all'>
                     <i class='fa fa-print'></i> Print List
                  </button>
                  <select id="listing" class="form-select mt-m-10 float-right">
                     <option value="ar">Accounts Receivable</option>
                     <option value="gl">General Ledger</option>
                     <option value="gst">GST</option>
                     <option value="stock">Stock</option>
                  </select>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1450px; width: 100%;">
                           <thead>
                              <tr>
                                 <th>Id</th>
                                 <th>Date</th>
                                 <th>Reference</th>
                                 <th>Customer</th>
                                 <th class="ar" style="display: none">Currency</th>
                                 <th class="ar" style="display: none">FAMT $</th>
                                 <th class="ar" style="display: none">SGD $</th>
                                 <th class="ar" style="display: none">Remarks</th>
                                 <th class="gl" style="display: none">Account</th>
                                 <th class="gl" style="display: none">Debit</th>
                                 <th class="gl" style="display: none">Credit</th>
                                 <th class="gl" style="display: none">Remarks</th>
                                 <th class="gst" style="display: none">Amount</th>
                                 <th class="gst" style="display: none">GST Category</th>
                                 <th class="gst" style="display: none">GST Amount</th>
                                 <th class="gst" style="display: none">GST Type</th>
                                 <th class="gst" style="display: none">Remarks</th>
                                 <th class="stock" style="display: none">Stock</th>
                                 <th class="stock" style="display: none">Quantity</th>
                                 <th class="stock" style="display: none">Unit Cost</th>
                                 <th class="stock" style="display: none">Amount</th>
                                 <th class="stock" style="display: none">Sign</th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </div>
            </div> <!-- Card - ends -->

         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="/assets/js/datatable.js"></script>

<script type="text/javascript">
   $(function() {

      $('#listing').change(function() {
         var type = $(this).val();
         $('.title').html(type);

         $('.ar, .gl, .gst, .stock').hide();

         if(type == "ar") {
            $('.ar').show();
         } else if(type == "gl") {
            $('.gl').show();
         } else if(type == "gst") {
            $('.gst').show();
         } else if(type == "stock") {
            $('.stock').show();
         }

         table = $('#dt_').DataTable({
            'processing': true,
            'ajax': {
               'url': '/invoice/datatable/ajax_list/'+type,
               'type': 'post'
            },
            'columnDefs': [{
               "defaultContent": " ",
               "targets": "_all"
               },
               { 'width': 110, 'targets': 1 },
               { 'width': 110, 'targets': 2 },
               { 'width': 300, 'targets': 3 }
            ],
            "aaSorting": [],
            "bDestroy": true
         });
         table.column( 0 ).visible( false ); // id 
      });
      
      $("#listing").val('ar').trigger("change");
      
      $("#btn_back").on('click', function() {
         url = "/invoice/";
         showData("back", url);
      });

      $('#btn_print_all').on('click', function() {
         url = '/invoice/print_other_listing/<?php echo $this->uri->segment(3); ?>';
         print_all(url);
      });

   });// document ends

   $( document ).ajaxComplete(function( event, request, settings ) {
      $(".btn").prop("disabled",false);
   });

</script>
