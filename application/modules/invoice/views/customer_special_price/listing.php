<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Customer Special Price</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Customer</li>
               <li class="breadcrumb-item active">Special Price</li>
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
                  <button class='btn btn-success btn-sm' id='btn_new'>
                     <i class='fa-solid fa-plus'></i> New
                  </button>
                  <button class='btn btn-warning btn-sm' id='btn_view'>
                     <i class='fa fa-eye'></i> View
                  </button>
                  <button class='btn btn-primary btn-sm' id='btn_edit'>
                     <i class='fa fa-pencil'></i> Edit
                  </button>                  
                  <button  class='btn bg-maroon btn-sm' id='btn_delete'>
                     <i class='fa fa-trash'></i> Delete
                  </button>                  
                  <button class='btn bg-navy btn-sm' id='btn_print_all'>
                     <i class='fa fa-print'></i> Print All
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print'>
                     <i class='fa fa-print'></i> Print Record
                  </button>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1200px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Date</th>
                                 <th>Customer</th>
                                 <th>Stock & Price</th>
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

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="/assets/js/datatable.js"></script>

<script type="text/javascript">
   $(function() {
      var url = '';

      table = $('#dt_').DataTable({
         'processing': true,
         'ajax': {
            'url': '/invoice/customer_special_price/list',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 110, 'targets': 1 }
         ],         
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id 
      
      $("#btn_back").on('click', function() {
         url = "/invoice";
         showData("back", url);
      });

      $("#btn_new").on('click', function() {
         window.location.href = "/invoice/add_customer_price";
      });

      $("#btn_view").on('click',function() {
			url = '/invoice/view_customer_price/';
			showData("view", url);
		});

		$("#btn_edit").on('click',function() {
			url = '/invoice/edit_customer_price/';
			showData("edit", url);
		});

      $("#btn_delete").on('click',function() {
			url = '/invoice/customer_special_price/delete';
			showData("delete", url);
		});
	
      $("#btn_print").on('click', function() {
         url = '/invoice/customer_special_price/print';
         showData("print", url);
      });

      $("#btn_print_all").click(function() {
         var url = '/invoice/customer_special_price/print';
         $('#rowID').val(0);
         $("#print_form").attr("action", url);
         $("#print_form").submit();
      });


   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });
</script>


