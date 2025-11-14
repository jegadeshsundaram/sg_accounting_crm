<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0"><span class="title"></span> Payment's</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Payment</li>
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

      <div class="row buttons-panel">
         <div class="col-lg-12">
            <div class="card card-default">
               <div class="card-header">
                  <button class='btn btn-outline-dark btn-sm' id='btn_back'>
                     <i class="fa-solid fa-angles-left"></i> Back
                  </button>
                  <button class='btn btn-warning btn-sm' id='btn_view'>
                     <i class='fa fa-eye'></i> View
                  </button>
                  <button class='btn btn-primary btn-sm d-none btns' id='btn_edit' style="display: none">
                     <i class='fa fa-pencil'></i> Edit
                  </button>
                  <button class='btn bg-maroon btn-sm btns' id='btn_delete' style="display: none">
                     <i class='fa fa-trash'></i> Delete
                  </button>
                  <button class='btn btn-success btn-sm btns' id='btn_post' style="display: none">
                     <i class='fa fa-check'></i> Post
                  </button>
                  <button class='btn btn-success btn-sm btns' id='btn_post_all' style="display: none">
                     <i class='fa-solid fa-check-double'></i> Post All
                  </button>
                  <button class='btn bg-purple btn-sm btns' id='btn_email' style="display: none">
                     <i class='fa fa-inbox'></i> Email Supplier
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print_all'>
                     <i class='fa fa-print'></i> Print All
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print'>
                     <i class='fa fa-print'></i> Print Payment
                  </button>

                  <select id="listing" class="form-select mt-m-10 float-right">
                     <option value="CONFIRMED">Confirmed</option>
                     <option value="POSTED">Posted</option>
                     <option value="DELETED">Deleted</option>
                  </select>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1400px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Date</th>
                                 <th>Reference</th>
                                 <th>Supplier</th>
                                 <th>Bank</th>
                                 <th>Cheque</th>
                                 <th>Amount</th>
                                 <th>Remarks</th>
                                 <th></th>
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
               <input type="hidden" name="status" id="status" value="CONFIRMED" />
            </form>

         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/assets/js/datatable.js"></script>

<script type="text/javascript">
   var url = '';
   $(function() {

      $('#listing').change(function() {
         var type = $(this).val();
         
         $('.title').html(type);
         if(type == "CONFIRMED") {
            $('.btns').show();
         } else {
            $('.btns').hide();
         }

         table = $('#dt_').DataTable({
            'processing': true,
            'ajax': {
               'url': '/payment/datatable/ajax_list/'+type,
               'type': 'post'
            },
            'columnDefs': [
               { targets: [6], className: 'dt-al-right' },
               { 'width': 110, 'targets': 1 },
               { 'width': 110, 'targets': 2 },
               { 'width': 400, 'targets': 3 },
               { 'width': 170, 'targets': 4 },
               { 'width': 170, 'targets': 5 },
               { 'width': 170, 'targets': 6 },
               { 'width': 300, 'targets': 7 }
            ],
            "aaSorting": [],
            "bDestroy": true
         });
         table.column( 0 ).visible( false ); // payment id 
         table.column( 8 ).visible( false ); // modified date
      });

      $("#listing").val('CONFIRMED').trigger("change");      
    
      $("#btn_back").on('click', function() {
         url = "/payment/";
         showData("back", url);
      });

      $("#btn_view").on('click',function() {
			var url = '/payment/manage/view/';
			showData("view", url);
		});

		$("#btn_edit").on('click',function() {
			var url = '/payment/manage/edit/';
			showData("edit", url);
		});

      $("#btn_delete").on('click',function() {
			var url = '/payment/ajax/delete';
			showData("delete", url);
		});

      $("#btn_email").click(function() {
         url = '/payment/print_stage_2';
         showData("email", url);
      });

      $("#btn_post").on('click', function() {
         var url = '/payment/ajax/post';
         showData("postSingle", url);
      });

      $("#btn_post_all").on('click', function() {
         var url = '/payment/ajax/post';
         table.rows().select();
         showData("postAll", url);
      });

      $("#btn_print").on('click', function() {
         url = '/payment/print_stage_2';
         showData("print", url);
      });

      $("#btn_print_all").click(function() {         
         url = '/receipt/print_payments/'+$("#listing").val();
         print_all(url);
      });

   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });
</script>
