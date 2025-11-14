<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GST</li>
               <li class="breadcrumb-item active">Opening Balance</li>
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
                  <h5>Opening Balance</h5>
                  <a href="/gst/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header">
                  <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#taxModal">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Create
                  </button>
                  <button class='btn btn-success btn-sm' id='btn_post_all'>
                     <i class='fa-solid fa-check-double'></i> Post All
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
                        <table id="dt_" class="table table-hover" style="min-width: 1450px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Action</th>
                                 <th>Date</th>
                                 <th>Reference</th>
                                 <th>Type</th>
                                 <th>Remarks</th>
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

<!-- Modal :: taxModal -->
<div id="taxModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm modal-sm">
      <div class="modal-content">
         <form id="frm_st_status" action="#" method="GET">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">GST</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Choose entry of input or output tax transactions</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#taxModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-md-12">
                        <a href="/gst/manage_ob?type=input" class="btn bg-purple btn-sm btn-block" style="text-aign: center">Entry Input Tax</a>
                        <a href="/gst/manage_ob?type=output" class="btn bg-purple btn-sm btn-block float-right" style="text-aign: center">Entry Output Tax</a>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#taxModal">Cancel</button>
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

<script type="text/javascript">
   var url = '';
   $(function() {

      table = $('#dt_').DataTable({
         'processing': true,
         'ajax': {
            'url': '/gst/ajax/populate_opening_balance',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 150, 'targets': 1 },
            { 'width': 120, 'targets': 2 },
            { 'width': 150, 'targets': 3 },
            { 'width': 100, 'targets': 4 },
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id 
    
      // edit
      $('#dt_').on('click', 'tbody .dt_edit', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];

         window.location.href = '/gst/manage_ob/' + rowID;
      });

      // delete
      $('#dt_').on('click', 'tbody .dt_delete', function () {
         $(this).closest('tr').addClass('selected');
         url = '/gst/ajax/delete_ob';
         showData("delete", url);
      });

      // post
      $('#dt_').on('click', 'tbody .dt_post', function () {
         $(this).closest('tr').addClass('selected');
         url = '/gst/ajax/post_ob';
         showData("postSingle", url);
      });

      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/gst/ajax/delete_ob';
            showData("deleteAll", url);
         }
		});

      $("#btn_post_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();
            
            url = '/gst/ajax/post_ob';
            showData("postAll", url);
         }
		});      

      $("#btn_print").on('click', function() {
         url = '/gst/print_ob';
         showData("print", url);
      });

      $("#btn_print_all").click(function() {
         if(table.rows().eq(0).length > 0) {
            var url = '/gst/print_ob';
            print_all(url);
         }
      });

   }); // document ends
</script>
