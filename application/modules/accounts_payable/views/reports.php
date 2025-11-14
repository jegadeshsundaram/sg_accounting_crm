<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Accounts Payable</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Accounts Payable</li>
               <li class="breadcrumb-item">Creditors</li>
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
                  <h5>Reports</h5>
                  <a href="/accounts_payable/" class="btn btn-outline-dark btn-sm float-right">
                    <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-body options">
                  <div class="row">
                     <div class="col-xl-4 col-md-6 mt-10">
                        <a data-toggle="modal" data-target="#statementModal" class="btn btn-info btn-block">
                           Current Statement <span>Statment of Current transactions can be printed for any Creditor</span>
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6 mt-10">
                        <a data-toggle="modal" data-target="#historicalStatementModal" class="btn btn-info btn-block">
                           Hisorical Statement <span>Statment of Historical transactions can be printed for any Creditor</span>
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6 mt-10">
                        <a data-toggle="modal" data-target="#listingModal" class="btn btn-info btn-block">
                           Current Listing <span>List of current transcations by single currency or all currencies</span>
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6 mt-10">
                        <a data-toggle="modal" data-target="#historicalListingModal" class="btn btn-info btn-block">
                           Historical Listing <span>List of Historical transcations by single currency or all currencies</span>
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6 mt-10">
                        <a data-toggle="modal" data-target="#ageingModal" class="btn btn-info btn-block">
                           Ageing <span>Report of transactions ageing will be categorized and printed</span>
                        </a>
                     </div>
                  </div>
               </div>

               <div class="card-footer">
                  <a href="/accounts_payable" class="btn btn-warning btn-sm float-right" style="font-size: 1rem">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Modal :: Current Statement -->
<div id="statementModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_stmt" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Creditor Statement</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Current Statement of the selected Creditor will be printed in the pdf format</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#statementModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="supplier" class="col-md-12 control-label">Customer : </label>
                     <div class="col-md-12">
                        <select name="supplier" id="supplier" class="form-control">
                           <?php echo $suppliers; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#statementModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_statement">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Historical Statement -->
<div id="historicalStatementModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_hstl_stmt" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span class="float-left" style="margin: 0;">Historical Creditor Statement</span>
                  <span style="margin: 0;font-size: 0.7rem; float: left;"><strong>Note: </strong>Historical statement wil be printed for the selected Creditor</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#historicalStatementModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">                  
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="from_date" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="from_date" name="from_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="to_date" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="to_date" name="to_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" />
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <label for="supplier_id" class="col-md-12 control-label">Creditor : </label>
                     <div class="col-md-12">
                        <select name="supplier_id" id="supplier_id" class="form-control">
                           <?php echo $suppliers; ?>
                        </select>
                     </div>
                  </div>                  
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#historicalStatementModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_historical_statement">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Current Listing -->
<div id="listingModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_lstng" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Creditor Listing</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Current statement of the selected currency will be printed in the pdf format</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#listingModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="currency" class="col-md-12 control-label">Currency : </label>
                     <div class="col-md-12">
                        <select name="currency" id="currency" class="form-control">
                           <?php echo $currencies; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#listingModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_listing">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Historical Creditor Listing -->
<div id="historicalListingModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_hstl_lstng" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span class="float-left" style="margin: 0;">Historical Creditor Listing</span>
                  <span style="margin: 0;font-size: 0.7rem; float: left;"><strong>Note: </strong>Historical Listing wil be printed for the selected Creditor</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#historicalListingModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">                  
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="hl_from_date" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="hl_from_date" name="hl_from_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="hl_to_date" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="hl_to_date" name="hl_to_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" />
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <label for="hl_currency" class="col-md-12 control-label">Currency : </label>
                     <div class="col-md-12">
                        <select name="hl_currency" id="hl_currency" class="form-control">
                           <?php echo $currencies; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#historicalListingModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_historical_listing">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Ageing -->
<div id="ageingModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_agng" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span class="float-left" style="margin: 0;">Creditor'S Ageing</span>
                  <span style="margin: 0;font-size: 0.7rem; float: left;"><strong>Note: </strong>Ageing report will be printed for the selected and all currencies</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#ageingModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <div class="row">
                           <label for="cutoff_date" class="col-md-4 control-label">Cutoff Date : </label>
                           <div class="col-md-8">
                              <input 
                                 type="text"
                                 id="cutoff_date" name="cutoff_date"
                                 class="form-control dp_full_date w-120"
                                 value="<?php echo date('d-m-Y'); ?>" />
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <label for="currency_code" class="col-md-12 control-label">Currency : </label>
                     <div class="col-md-12">
                        <select name="currency_code" id="currency_code" class="form-control">
                           <?php echo $currencies; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#ageingModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_ageing">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
   // document starts
   $(function() {

      $('select').select2();

      $("#btn_print_statement").click(function() {
         if($('#supplier').val() == '') {
            $("#supplier").select2('open');
            return false;
         }

         $("#frm_stmt").attr("action", '/accounts_payable/print_statement');
         $("#frm_stmt").attr("target", "_blank");
         $("#frm_stmt").submit();
      });      

      $('#btn_print_historical_statement').on('click', function() {
         if($('#from_date').val() == '') {
            $('#from_date').focus();
            return false;
         } else if ($('#to_date').val() == '') {
            $('#to_date').focus();
            return false;
         } else if($('#supplier_id').val() == '') {
            $("#supplier_id").select2('open');
            return false;
         }
         
         var url = '/accounts_payable/print_historical_statement';
         $("#frm_hstl_stmt").attr("action", url);
         $("#frm_hstl_stmt").attr("target", "_blank");
         $("#frm_hstl_stmt").submit();
      });

      $("#btn_print_listing").click(function() {
         if($('#currency').val() == '') {
            $("#currency").select2('open');
            return false;
         }

         $("#frm_lstng").attr("action", '/accounts_payable/print_listing');
         $("#frm_lstng").attr("target", "_blank");
         $("#frm_lstng").submit();
      });

      $('#btn_print_historical_listing').on('click', function() {
         if($('#hl_from_date').val() == '') {
            $('#hl_from_date').focus();
            return false;
         } else if ($('#hl_to_date').val() == '') {
            $('#hl_to_date').focus();
            return false;
         } else if($('#hl_currency').val() == '') {
            $("#hl_currency").select2('open');
            return false;
         }

         var url = '/accounts_payable/print_historical_listing';
         $("#frm_hstl_lstng").attr("action", url);
         $("#frm_hstl_lstng").attr("target", "_blank");
         $("#frm_hstl_lstng").submit();
      });

      $('#btn_print_ageing').on('click', function() {
         if($('#cutoff_date').val() == '') {
            $('#cutoff_date').focus();
            return false;
         }

         var url = '/accounts_payable/print_ageing';
         $("#frm_agng").attr("action", url);
         $("#frm_agng").attr("target", "_blank");
         $("#frm_agng").submit();
      });

   });
</script>
