<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Stock</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Stock</li>
               <li class="breadcrumb-item active">Adjustment</li>
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
                  <h5>Adjustment</h5>
                  <a href="/stock/" class="btn btn-outline-dark btn-sm float-right">
                    <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header">
                  <a href="/stock/manage_adjustment" class="btn btn-info btn-sm">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Input
                  </a>
                  <button class='btn btn-primary btn-sm' id='btn_edit'>
                     <i class='fa fa-pencil'></i> Edit
                  </button>
                  <button class='btn btn-success btn-sm' id='btn_post'>
                     <i class='fa fa-check'></i> Post
                  </button>
                  <button class='btn btn-success btn-sm' id='btn_post_all'>
                     <i class='fa-solid fa-check-double'></i> Post All
                  </button>
                  <button class='btn bg-maroon btn-sm' id='btn_delete'>
                     <i class='fa fa-trash'></i> Delete
                  </button>
                  <button class='btn bg-maroon btn-sm' id='btn_delete_all'>
                     <i class='fa fa-trash'></i> Delete All
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print'>
                     <i class='fa fa-print'></i> Print Record
                  </button>
                  <button class='btn bg-navy btn-sm' id='btn_print_all'>
                     <i class='fa fa-print'></i> Print All
                  </button>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 370px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Date</th>
                                 <th>Reference</th>
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
               <input type="hidden" name="ob_type" id="ob_type" value="C" />  
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

      table = $('#dt_').DataTable({
         'processing': true,
         'ajax': {
            'url': '/stock/ajax/populate_adjustment',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 100, 'targets': 1 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id

		$("#btn_edit").on('click',function() {
         url = '/stock/manage_adjustment/';
         showData("edit", url);
		});

      $("#btn_delete").on('click',function() {
         url = '/stock/ajax/delete_adjustment';
         showData("delete", url);
		});

      $("#btn_delete_all").on('click',function() {
         selectAllRows();

         url = '/stock/ajax/delete_adjustment';
         showData("deleteAll", url);
		});

      $("#btn_post").on('click', function() {
         url = '/stock/ajax/post_adjustment';
         showData("postSingle", url);
      });

      $("#btn_post_all").on('click', function() {
         selectAllRows();

         url = '/stock/ajax/post_adjustment';
         showData("postAll", url);
      });

      $("#btn_print").on('click', function() {
         url = '/stock/print_adjustment';
         showData("print", url);
      });

      $("#btn_print_all").click(function() {
         if(table.rows().eq(0).length > 0) {
            url = '/stock/print_adjustment';
            print_all(url);
         }
      });


   }); // document ends

   $(document).ajaxComplete(function(event, request, settings) {
      $(".btn").prop("disabled", false);
   });
</script>
