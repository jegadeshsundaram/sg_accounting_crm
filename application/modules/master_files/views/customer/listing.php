<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Customer's</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item active">Customer</li>
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
                  <?php echo $buttonsPanel; ?>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1100px; width: 100%;">
                           <thead>
                              <tr>
                                 <th>Id</th>
                                 <th>Code</th>
                                 <th>Name</th>
                                 <th>Contact Person</th>
                                 <th>Currency</th>                                 
                                 <th>Phone</th>
                                 <th>Email</th>
                              </tr>
                           </thead>
                        </table>

                        <form id="print_form" method="get" action="#">
                           <input type="hidden" name="rowID" id="rowID" />
                        </form>

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
      var url = '';

      table = $('#dt_').DataTable({
         'processing': true,
         'ajax': {
            'url': '/master_files/datatable/ajax_list/customer',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 90, 'targets': 1 },
            { 'width': 320, 'targets': 2 },
            { 'width': 250, 'targets': 3 },
            { 'width': 70, 'targets': 4 },
            { 'width': 180, 'targets': 5 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false );

      $("#btn_new").on('click', function() {
         window.location.href = "/master_files/add_customer";
      });

      $("#btn_edit").on('click', function() {
         url = '/master_files/edit_customer/';
         showData("edit", url);
      });

      $("#btn_view").on('click', function() {
         url = '/master_files/view_customer/';
         showData("view", url);
      });

      $("#btn_delete").on('click',function() {
         url = '/master_files/Ajax/customer/delete';
         showData("delete", url);
      });

      $("#btn_print").on('click', function() {
         url = '/master_files/Ajax/customer/print';
         showData("print", url);
      });

      $("#btn_print_all").click(function() {
         url = '/master_files/print_customers';
         print_all(url);
      });

   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });
 </script>
