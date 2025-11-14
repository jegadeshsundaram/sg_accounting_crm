$(function() {
   
   // show prompt on exit - start
   $('.main-sidebar a').click(function(e) {

      var i = 0;
      $('.req').each(function() {
         if($(this).val() !== '') {
            i += 1;
         }
      });

      var url = $(this).attr('href');
      if(url === '#' || url == '' || url === "javascript:void(0);") {
         i = 0;
      }

      if ($("#redirect_url").length) {
         url = $("#redirect_url").val();
      }      

      if(i > 0) {
         e.preventDefault();
         $.confirm({
            title: 'Exit this page? <br />',
            content: 'All unsaved changes will be lost and cannot be recovered!',
            buttons: {
               exit: {
                  btnClass: 'btn btn-white',
                  action: function(){
                     window.location = url;
                  }
               },
               'Stay on Page': {
                  btnClass: 'btn btn-primary float-right',
                  action: function(){
                  }
               }
            }
         });
      }
   });
   // show prompt on exit - end
   
}); // document ends
