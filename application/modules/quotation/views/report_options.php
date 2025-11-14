<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Quotation Reports</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Quotation</li>
               <li class="breadcrumb-item active">Reports</li>
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
                  <h5>Marketting</h5>
                  <a href="/quotation" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_mrkt_report_1" class="btn btn-info btn-block">
                           Summary Analysis by Company
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_mrkt_report_2" class="btn btn-info btn-block">
                           Summary Analysis by Employee
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_mrkt_report_3" class="btn btn-info btn-block">
                           Detailed Listing by Company
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_mrkt_report_4" class="btn btn-info btn-block">
                           Detailed Listing by Employee
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-header">
                  <h5>Sales</h5>
               </div>
               <div class="card-body">
                  <div class="row">                     
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_sls_report_1" class="btn btn-info btn-block">
                           Sales Performance by Staff
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_sls_report_2" class="btn btn-info btn-block">
                           Sales Performance by Product
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_sls_report_3" class="btn btn-info btn-block">
                           Sales Comparison by Staff
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_sls_report_4" class="btn btn-info btn-block">
                           Sales Comparison by Product
                        </a>
                     </div>
                     <div class="col-xl-4 col-md-6">
                        <a id="btn_sls_report_5" class="btn btn-info btn-block">
                           Sales Comparison by Quantity Sold
                        </a>
                     </div>
                  </div>
               </div>

               <div class="card-footer">
                  <a href="/quotation" class="btn btn-warning btn-sm float-right">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>

            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Date Period Modal - starts -->
<div id="datePeriodModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="form_1" method="get">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span class="title" style="margin: 0;">Report</span> <br />
                  <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Report will be printed in the pdf format on submission.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 20px; right: 20px;" data-toggle="modal" data-target="#datePeriodModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-6">
                        <div class="row">
                           <label for="start_date" class="col-12 control-label">Start Date : </label>
                           <div class="col-12">                           
                              <input 
                                 type="text" 
                                 id="start_date" name="start_date"
                                 class="form-control dp_full_date w-120" 
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="end_date" class="col-12 control-label">End Date : </label>
                           <div class="col-12">
                              <input 
                                 type="text" 
                                 id="end_date" name="end_date"
                                 class="form-control dp_full_date w-120" 
                                 placeholder="dd-mm-yyyy" />
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row employee_ddm" style="display: none; padding: 10px;">
                     <label for="employee_id" class="col-12 control-label">Employee : </label>
                     <select class="form-control" name="employee_id" id="employee_id" required>
                        <option value="Company">-- Select Employee --</option>
                        <?php $employee_list = $this->qt_model->get_all_employee();
               foreach ($employee_list as $emp) { ?>
                        <option value="<?php echo $emp['id']; ?>"><?php echo $emp['name']; ?></option>
                        <?php }?>
                     </select>
                  </div>

               </div>

               <div class="card-footer">                  
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#datePeriodModal">Cancel</button>                    
                  <button type="button" class="btn btn-warning btn-sm float-right" id="btn_dp_report_modal_submit">Submit</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Month Year Modal - starts -->
<div id="monthYearModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="form_2" method="get">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span class="title" style="margin: 0;">Report</span> <br />
                  <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Report will be printed in the pdf format on submission.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 20px; right: 20px;" data-toggle="modal" data-target="#monthYearModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-12">
                        <div class="row">
                           <label for="month_year" class="col-5 control-label">Date (Month Year) : </label>
                           <div class="col-7">
                              <input 
                                 type="text" 
                                 id="month_year"
                                 class="form-control dp_month_year w-120" 
                                 placeholder="month year" required />
                                 <input type="hidden" name="month" id="month" />
                                 <input type="hidden" name="year" id="year" />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="card-footer">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#monthYearModal">Cancel</button>                    
                  <button type="button" class="btn btn-warning btn-sm float-right" id="btn_my_report_modal_submit">Submit</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>


<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<style> 
   .btn {
      text-align: left;
      margin-bottom: 10px;
   }
   .card-header span {
      padding-top: 5px;
      color: dimgray;
      font-size: 1.2rem;
      letter-spacing: 1px;
   }
   .control-label {
      display: flex;
   }
</style>

<script>
   // document starts
   $(function() {

      // Marketting Report - Summary Analysis by Company
      $('#btn_mrkt_report_1').click(function() {
         $("#form_1").attr("action", "/quotation/rpt_summary_analysis");
         
         $('.employee_ddm').hide();
         $('#datePeriodModal .title').text("Summary Analysis by Company");
         $('#datePeriodModal').modal();
      });

      // Marketting Report - Summary Analysis by Employee
      $('#btn_mrkt_report_2').click(function() {
         $("#form_1").attr("action", "/quotation/rpt_summary_analysis");
         
         $('.employee_ddm').show();
         $('#datePeriodModal .title').text("Summary Analysis by Employee");
         $('#datePeriodModal').modal();
      });

      // Marketting Report - Detailed Listing by Company
      $('#btn_mrkt_report_3').click(function() {
         $("#form_1").attr("action", "/quotation/rpt_detailed_analysis");
         
         $('.employee_ddm').hide();
         $('#datePeriodModal .title').text("Detailed Listing by Company");
         $('#datePeriodModal').modal();
      });

      // Marketting Report - Detailed Listing by Employee
      $('#btn_mrkt_report_4').click(function() {
         $("#form_1").attr("action", "/quotation/rpt_detailed_analysis");
         
         $('.employee_ddm').show();
         $('#datePeriodModal .title').text("Detailed Listing by Employee");
         $('#datePeriodModal').modal();
      });

      // Sales Report - Sales Performance by Staff
      $('#btn_sls_report_1').click(function() {
         $("#form_1").attr("action", "/quotation/rpt_sales_performance_by_staff");

         $('#datePeriodModal .title').text("Sales Performance by Staff");
         $('#datePeriodModal').modal();
      });

      // Sales Report - Sales Performance by Product
      $('#btn_sls_report_2').click(function() {
         $("#form_1").attr("action", "/quotation/rpt_sales_performance_by_product");

         $('#datePeriodModal .title').text("Sales Performance by Product");
         $('#datePeriodModal').modal();
      });

      // Sales Report - Sales Comparison by Staff
      $('#btn_sls_report_3').click(function() {
         $("#form_2").attr("action", "/quotation/rpt_12months_sales_by_staff");

         $('#monthYearModal .title').text("12 Months Sales by Staff");
         $('#monthYearModal').modal();
      });

      // Sales Report - Sales Comparison by Product
      $('#btn_sls_report_4').click(function() {
         $("#form_2").attr("action", "/quotation/rpt_12months_sales_by_product");

         $('#monthYearModal .title').text("12 Months Sales by Product");
         $('#monthYearModal').modal();
      });

      // Sales Report - Sales Comparison by Quantity Sold
      $('#btn_sls_report_5').click(function() {
         $("#form_2").attr("action", "/quotation/rpt_12months_sales_by_qty_sold");

         $('#monthYearModal .title').text("12 Months Sales by Quantity Sold");
         $('#monthYearModal').modal();
      });

      // date period report modal on focus
      $('#datePeriodModal').on('shown.bs.modal', function () {
         $('#start_date').focus();
      });

      // month year report modal on focus
      $('#monthYearModal').on('shown.bs.modal', function () {
         $('#month_year').focus();
      });
      
      $(document).on("change", "#start_date", function() {
         if($(this).val() !== "") {
            //$('#end_date').focus(); // Not working
         }
      });

      // Btn - Submit - Date Period Report Modal
      $('#btn_dp_report_modal_submit').click(function() {
         if($('#form_1').valid()) {
            $("#form_1").submit();
         }
      });

      // Btn - Submit - Month Year Report Modal
      $('#btn_my_report_modal_submit').click(function() {
         if($('#form_2').valid()) {
            $("#form_2").submit();
         }
      });      
   });
</script>