$(document).ready(function($) {   

   toastr.options = {
      "closeButton": true,
      "debug": true,
      "newestOnTop": true,
      "progressBar": true,
      "positionClass": "toast-top-center",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
   }

   $(".table-responsive").on('click', '.select2.select2-container', function() {
      $('body').addClass('overlaybg');
   });   

   $(window).scroll(function() {
      if ($(this).scrollTop() > 20) {
         $('#back-to-top').fadeIn();
      } else {
         $('#back-to-top').fadeOut();
      }
   });

   // ______________Sidebar Menu Active
   /*** add active class and stay opened when selected ***/
   var url = window.location;

   // for sidebar menu entirely but not cover treeview
   $('ul.nav-sidebar a').filter(function() {
      if (this.href) {
         return this.href == url || url.href.indexOf(this.href) == 0;
      }
   }).addClass('active');

   // for the treeview
   $('ul.nav-treeview a').filter(function() {
      if (this.href) {
         return this.href == url || url.href.indexOf(this.href) == 0;
      }
   }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');

   // desktop menu clicks - starts
   $('.dt-box').click(function(e) {
      var menu = $(this).attr("id");
      $("li[class^='sb-'],li[class*=' sb-']").hide();
      $('body').removeClass('sidebar-closed');
      $('body').removeClass('sidebar-collapse');
      $('body').addClass('sidebar-open');
      $("li.sb-"+menu).show();
      $("li.sb-"+menu).addClass('menu-is-opening menu-open');      
   });

   $('#quot').click(function(e) {
      window.location = "/quotation";
      return false;
   });

   $('#inv').click(function(e) {
      window.location = "/invoice";
      return false;
   });

   $('#rec').click(function(e) {
      window.location = "/receipt";
      return false;
   });

   $('#ar').click(function(e) {
      window.location = "/accounts_receivable";
      return false;
   });

   $('#stock').click(function(e) {
      window.location = "/stock";
      return false;
   });

   $('#pay').click(function(e) {
      window.location = "/payment";
      return false;
   });

   $('#ap').click(function(e) {
      window.location = "/accounts_payable";
      return false;
   });

   $('#pc').click(function(e) {
      window.location = "/petty_cash";
      return false;
   });

   $('#fb').click(function(e) {
      window.location = "/foreign_bank";
      return false;
   });

   $('#br').click(function(e) {
      window.location = "/bank_reconciliation";
      return false;
   });

   $('#am').click(function(e) {
      window.location = "/general_ledger";
      return false;
   });

   $('#gst').click(function(e) {
      window.location = "/gst";
      return false;
   });

   $('#ez').click(function(e) {
      window.location = "/ez_entry";
      return false;
   });

   $('#st').click(function(e) {
      window.location = "/staff_activity";
      return false;
   });

   $('#sac').click(function(e) {
      window.location = "/sac_job_control";
      return false;
   });

   $('#tfm').click(function(e) {
      window.location = "/data_migration/options";
      return false;
   });   
   // desktop menu clicks - ends

   var device_width = $(window).width();
   if(device_width >= 992) {
      $('body').removeClass('sidebar-collapse');
      $('body').addClass('sidebar-open');
   }

   if(device_width <= 768) {
      $('.content-wrapper').removeClass('px-4');
      $('.content-wrapper').addClass('px-1');
   }

   $('.sb-menu-bar').click(function(e) {
      $("li[class^='sb-'],li[class*=' sb-']").removeClass('menu-is-opening');
      $("li[class^='sb-'],li[class*=' sb-']").removeClass('menu-open');
      $("li[class^='sb-'],li[class*=' sb-']").show();

      if ($("body").hasClass("sidebar-open")) {
         $('body').removeClass('sidebar-open');
         $('body').addClass('sidebar-closed');
         $('body').addClass('sidebar-collapse');
      } else if ($("body").hasClass("sidebar-closed")) {
         $('body').removeClass('sidebar-closed');
         $('body').removeClass('sidebar-collapse');
         $('body').addClass('sidebar-open');
      } else {
         if(device_width < 768) { // Mobile Devices
            $('body').addClass('sidebar-open');
            $('body').removeClass('sidebar-closed');
            $('body').removeClass('sidebar-collapse');
         } else {
            $('body').removeClass('sidebar-open');
            $('body').addClass('sidebar-closed');
            $('body').addClass('sidebar-collapse');
         }
      }
   });

   $('#sidebar-overlay').click(function() {
      $('body').removeClass('sidebar-open');
      $('body').addClass('sidebar-closed');
      $('body').addClass('sidebar-collapse');
   });

   $(".content").on('click', '.select2.select2-container', function() {
      $('body').addClass('overlaybg');
   });

   /* back button */
   $("#back").on('click',function() {
      $.confirm({
         title: "<i class='fa fa-info'></i> Go Back",
         text: "Confirm?",
         cancelButton: "No",
         confirm: function(button) {
            location.reload();
         },
         cancel: function(button) {
         }
      });
   });

   $('#btn-confirm-no').click(function() {
      $("#confirmSubmitModal").modal('hide');
   });

   $("#uploadBtn").on('change',function() {
      $("#uploadFile").val($(this).val());
      readURL(this);
   });

   $(".dp_month_year_NOT_USED").datepicker({
      format: "mm/yyyy",
      startView: "months", 
      minViewMode: "months",
      autoclose: true
   });

   $('.dp_month_year').datepicker({
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      dateFormat: 'M yy',
      yearRange: '-3:+0'
   }).focus(function() {
      var thisCalendar = $(this);
      $('.ui-datepicker-calendar').detach();
      $('.ui-datepicker-close').click(function() {
         var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
         var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
         thisCalendar.datepicker('setDate', new Date(year, month, 1));
         $('#month').val(month);
         $('#year').val(year);
      });
   });
   
   $(".dp_full_date").datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'dd-mm-yy',
      yearRange: '-3:+1'
   });

   $(document).mouseup(function (e) {
      var container = $(".select2-dropdown");
      if (container.has(e.target).length === 0) {
         $('body').removeClass('overlaybg');
      }
   });

   $(".card").click(function() {
      $('#message_area').html("");
   });
   
}); // document ends

function trimInput(el) {
   return el.
   replace (/(^\s*)|(\s*$)/gi, ""). // removes leading and trailing spaces
   replace (/[ ]{2,}/gi," ").       // replaces multiple spaces with one space
   replace (/\n +/,"\n");           // Removes spaces after newlines
}

function print_all(url) {
   let a= document.createElement('a');
   a.target= '_blank';
   a.href= url;
   a.click();
}

$(document).mouseup(function (e) {
   var container = $(".select2-dropdown");
   if (container.has(e.target).length === 0) {
      $('body').removeClass('overlaybg');
   }
});