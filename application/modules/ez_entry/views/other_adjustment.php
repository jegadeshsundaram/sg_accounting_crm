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
               <li class="breadcrumb-item active">Other Adjustment</li>
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
                  <h5>Other Adjustment</h5>
                  <a href="/ez_entry" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header">
                  <button class='btn btn-primary btn-sm' id="btn_new">
                     <i class="fa fa-plus-circle" aria-hidden="true"></i> New Adjustment
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
                        <table id="dt_" class="table table-hover" style="min-width: 900px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Action</th>
                                 <th>Date</th>
                                 <th>Reference</th>
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
               'url': '/ez_entry/datatable/ajax_list/batch_adjustment',
               'type': 'post'
         },
         'columnDefs': [
               { 'width': 170, 'targets': 1 },
               { 'width': 120, 'targets': 2 },
               { 'width': 150, 'targets': 3 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id

      $("#btn_new").on('click', function() {
         window.location.href = "/ez_entry/manage_other_adjustment/";
      });      

      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/ez_entry/ajax/delete_adjustment';
            showData("deleteAll", url);
         }
      });

      $("#btn_print_audit").on('click', function() {
         if(table.rows().eq(0).length > 0) {
            url = '/ez_entry/print_adjustment';
            print_all(url);
         }
      });
      
      // EDIT - Single Transaction
      $('#dt_').on('click', 'tbody .dt_edit', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         
         $(this).closest('tr').addClass('selected');

         window.location.href = '/ez_entry/manage_other_adjustment/'+rowID;
      });

      // DELETE - Single Transaction
      $('#dt_').on('click', 'tbody .dt_delete', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         console.log(rowID);

         $(this).closest('tr').addClass('selected');

         $.confirm({
            title: '<i class="fa fa-info"></i> Delete Adjustment',
            content: 'Are you sure to delete?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     $.post('/ez_entry/ajax/delete_adjustment', {
                        rowID: rowID
                     }, function (res) {
                        if(res == "error") {
                           toastr.error("Delete Error!");
                        } else {
                           toastr.success("Transaction Deleted!");
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

      // post - code added here to display variey of messages for user
      $('#dt_').on('click', 'tbody .dt_post', function () {
         $(this).closest('tr').addClass('selected');
         $.confirm({
            title: '<i class="fa fa-info"></i> Post Confirmation',
            content: 'Are you sure to Post?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     post();
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

      // post all
      $("#btn_post_all").on('click', function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();
            
            $.confirm({
               title: '<i class="fa fa-info"></i> Post ALL Confirmation',
               content: 'Are you sure to Post ALL transations?',
               buttons: {
                  yes: {
                     btnClass: 'btn-warning',
                     action: function(){
                        post();
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
         }
      });

      $(document).on('click', '.card', function() {
         $('#message_area').html('');
      });

   }); // document ends
   
   function post() {

      var rowID = 0;
      var posted = 0;
      var unbalanced = 0;

      var row_object = table.rows({ selected: true }).data();
      var count = table.rows({ selected: true }).count();
      for (i = 0; i < count; i++) {
         rowID = row_object[i][0];
         
         $.ajax({
            type: "POST",
            url: "/ez_entry/ajax/post_adjustment",
            data: {'rowID': rowID},
            async: false,
            success: function(data) {
               var obj = $.parseJSON(data);
      
               table.ajax.reload();

               posted = parseInt(posted) + parseInt(obj.posted);
               unbalanced = parseInt(unbalanced) + parseInt(obj.unbalanced);

               console.log("P/U >>> "+posted+" >>> "+unbalanced);

            }
         }).done(function() {

            console.log("Count >>> "+i+" >>>"+count);

            if(i == count - 1) {
               if(posted > 0 && unbalanced > 0) {
                  toastr.success('The transactions are posted except unbalanced transactions.');
               } else if(posted > 0 && unbalanced == 0) {
                  toastr.success('POSTED!');
               } else if(posted == 0 && unbalanced > 0) {
                  toastr.error('The Unbalanced transactions are not posted.');
               } else if(posted == 0 && unbalanced == 0) {
                  toastr.error('There are no transactions to post.');
               }
            }
         });
      }
   }
</script>
