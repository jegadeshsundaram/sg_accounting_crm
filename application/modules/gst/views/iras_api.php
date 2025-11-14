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
               <li class="breadcrumb-item active">IRAS API</li>
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
                  <h5>IRAS API Submission</h5>
                  <a href="/gst/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-3"></div>
                     <div class="col-md-6">
                        <div class="row">
                           <div class="col-md-12">
                              <a href="/gst/iras_api_filing_info" class="btn btn-block" style="color: #fff; background: teal; padding-left: 56px;">
                                 Filing Information <span>Tax Reference, Form and Period Information</span>
                                 <span style="position: absolute; left: 20px; top: 18px; background: cadetblue; padding: 0px 8px; font-size: 1.5rem; font-style: normal;">1</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a href="/gst/iras_api_form" class="btn btn-block" style="color: #fff; background: teal; padding-left: 56px;">
                                 Form Values <span>Choosing Form 5 / 7 / 8 by submitting or Edit GST Returns.</span>
                                 <span style="position: absolute; left: 20px; top: 18px; background: cadetblue; padding: 0px 8px; font-size: 1.5rem; font-style: normal;">2</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a href="/gst/iras_api_declaration" class="btn btn-block" style="color: #fff; background: teal; padding-left: 56px;">
                                 Declaration <span>Declare the agreement provided by iras before submit with tax agent details </span>
                                 <span style="position: absolute; left: 20px; top: 18px; background: cadetblue; padding: 0px 8px; font-size: 1.5rem; font-style: normal;">3</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a href="/gst/iras_api_contact_info" class="btn btn-block" style="color: #fff; background: teal; padding-left: 56px;">
                                 Contact Information <span>Details of agent who is submitting this gst returns to IRAS</span>
                                 <span style="position: absolute; left: 20px; top: 18px; background: cadetblue; padding: 0px 8px; font-size: 1.5rem; font-style: normal;">4</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a href="/gst/iras_api_fe_validation" class="btn btn-block" style="color: #fff; background: teal; padding-left: 56px;">
                                 Front-End Validation <span>Validate of all the steps and values as per IRAS rules and show results</span>
                                 <span style="position: absolute; left: 20px; top: 18px; background: cadetblue; padding: 0px 8px; font-size: 1.5rem; font-style: normal;">5</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a href="/gst/iras_api_generate_json" class="btn btn-block" style="color: #fff; background: teal; padding-left: 56px;">
                                 Genrate JSON Request <span>Generate request if front-end validation shows no errors or warnings</span>
                                 <span style="position: absolute; left: 20px; top: 18px; background: cadetblue; padding: 0px 8px; font-size: 1.5rem; font-style: normal;">6</span>
                              </a>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-3"></div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/gst" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>