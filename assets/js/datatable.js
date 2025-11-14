$(document).ready(function($) { // document starts

   /* select row in datatable to edit or view 
   $('#dt_ tbody').on('click', 'tr', function(e) {
      if ($(this).hasClass('selected')) {
         $(this).removeClass('selected');
      } else {
         $('#dt_ tr.selected').removeClass('selected');
         $(this).addClass('selected');
      }
   });*/

   $('#dt_, #dt_billing, .dataTable').on('click', 'tbody tr', (e) => {
      let classList = e.currentTarget.classList;

      if (classList.contains('selected')) {
         classList.remove('selected');
      } else {
         table.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
         classList.add('selected');
      }
   });

  
}); // document ends

function selectAllRows() {
   table.rows().every( function () {
      $(this.node()).addClass('selected');
   });
}

function showData(mode, url) {

   if(mode == "back") {
      window.location.href = url;
      return false;
   }
   
   rowData = table.row('.selected').data();
   
   if(rowData) {
      rowID = rowData[0];

      if(mode == "view") {
         window.location.href = url + rowID;

      } else if(mode == "edit") {
         window.location.href = url + rowID;

      } else if(mode == "print") {
         $('#rowID').val(rowID);
         $('#mode').val('print');
         $("#print_form").attr("target", '_blank');
         $("#print_form").attr("action", url);
         $("#print_form").submit();

      } else if(mode == "delete") {
         $.confirm({
            title: '<i class="fa fa-info"></i> Delete Confirmation',
            content: 'Are you sure to Delete ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     $.post(url, { rowID: rowID }, function(result) {
                        table.ajax.reload();
                        if($.trim(result) == 'errors') {
                           $("#message_area").html("<div class='alert alert-warning fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>Record cannot be deleted as it is active in transaction file</div>");
                        } else {
                           //$("#message_area").html("<div class='alert alert-success fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>RECORD DELETED</div>");
                           toastr.success('DELETED!');
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

      } else if(mode == "deleteAll") {
         
         $.confirm({
            title: '<i class="fa fa-info"></i> Delete All Confirmation',
            content: 'Are you sure to Delete ALL ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){

                     var row_object = table.rows({ selected: true }).data();
                     var count = table.rows({ selected: true }).count();
                     var rowID = 0;
                     for (i = 0; i < count; i++) {
                        rowID = row_object[i][0];

                        $.post(url,{ rowID: rowID }, function(result) {
                           table.ajax.reload();
                        });

                        if(i == count - 1) {
                           toastr.success("DELETED ALL");
                        }
                     }

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

      } else if (mode == "postAll") {
         $.confirm({
            title: '<i class="fa fa-info"></i> Post All Confirmation',
            content: 'Are you sure to Post ALL ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     var row_object = table.rows({ selected: true }).data();
                     var count = table.rows({ selected: true }).count();
                     var rowID = 0;
                     for (i = 0; i < count; i++) {
                        rowID = row_object[i][0];

                        $.post(url, { rowID: rowID }, function(result) {
                           table.ajax.reload();
                        });

                        if(i == count - 1) {
                           toastr.success("POSTED ALL");
                        }
                     }
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
      } else if (mode == "postSingle") {

         $.confirm({
            title: '<i class="fa fa-info"></i> Post Confirmation',
            content: 'Are you sure to Post ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     $.post(url, { rowID: rowID }, function(result) {
                        table.ajax.reload();
                        //$("#message_area").html("<div class='alert alert-success fade in'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>POSTED</div>");
                        toastr.success('POSTED!');
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

      } else if (mode == "reject") {        

         $.confirm({
            title: '<i class="fa fa-info"></i> Reject Confirmation',
            content: 'Are you sure to Reject this ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     $.post(url, { rowID: rowID }, function(result) {
                        table.ajax.reload();
                        //$("#message_area").html("<div class='alert alert-success fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>Quotation "+mode+"ed</div>");
                        toastr.success('REJECTED!');
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

      } else if(mode == "email") {
         $.confirm({
            title: '<i class="fa fa-info"></i> Email Confirmation',
            content: 'Are you sure to Send Email?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     $.post(url, { rowID: rowID }, function(result) {
                        table.ajax.reload();
                        if($.trim(result) == 'error') {
                           //$("#message_area").html("<div class='alert alert-warning fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>Sending Email Failed. Please try later.</div>");
                           toastr.success('ERROR IN SENDING EMAIL!');
                        } else {
                           //$("#message_area").html("<div class='alert alert-success fade in show'><button type='button' class='close close-sm' data-dismiss='alert'><i class='fa fa-times'></i></button>Email Sent</div>");
                           toastr.success('EMAIl SENT!');
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
      }
   } else { // Row Data
      message =  mode;
      var notify_msg = "Please select record to "+message;
      toastr.error(notify_msg, 'Error');
   }
}

function clearDTSearch() {
   table.search( '' )
   .columns().search( '' )
   .draw();
}