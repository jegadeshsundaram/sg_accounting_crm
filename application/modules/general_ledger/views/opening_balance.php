<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">General Ledger</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GL</li>
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
                  <a href="/general_ledger/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header">
                  <button class="btn btn-info btn-sm" id="btn_create">
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
                        <table id="dt_" class="table table-hover" style="min-width: 800px; width: 100%;">
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
            'url': '/general_ledger/ajax/populate_opening_balance',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 165, 'targets': 1 },
            { 'width': 100, 'targets': 2 },
            { 'width': 120, 'targets': 3 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id 

      // create
      $("#btn_create").on('click', function() {
         window.location.href = "/general_ledger/manage_ob";
      });

      // edit
      $('#dt_').on('click', 'tbody .dt_edit', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];

         window.location.href = '/general_ledger/manage_ob/' + rowID;
      });

      // delete
      $('#dt_').on('click', 'tbody .dt_delete', function () {
         $(this).closest('tr').addClass('selected');
         url = '/general_ledger/ajax/delete_ob';
         showData("delete", url);
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

      // delete all
      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();
         
            url = '/general_ledger/ajax/delete_ob';
            showData("deleteAll", url);
         }
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

      // print
      $("#btn_print").on('click', function() {
         url = '/general_ledger/print_ob';
         showData("print", url);
      });

      // print all
      $("#btn_print_all").click(function() {
         if(table.rows().eq(0).length > 0) {
            var url = '/general_ledger/print_ob';
            print_all(url);
         }
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
            url: "/general_ledger/ajax/post_ob",
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
