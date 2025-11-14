<div id="utilityModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">Datafile Utilities</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 15px; right: 20px;" data-toggle="modal" data-target="#utilityModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body df-lnk">
               <div class="row">
                  <div class="col-12">
                     <a id="btn_backup" class="btn btn-block lnk">
                        Backup <span style="color: dimgray">Process of creating copies of data files</span>
                     </a>
                  </div>
               </div>
               <hr />
               <div class="row">
                  <div class="col-12">
                     <a id="btn_restore" class="btn btn-block lnk">
                        Restore <span style="color: dimgray">Process of restoring backup copies of data files</span>
                     </a>
                  </div>
               </div>
               <hr />
               <div class="row">
                  <div class="col-12">
                     <a id="btn_zap" class="btn btn-block lnk">
                        Zap <span style="color: dimgray">Process of deleting records from data files</span>
                     </a>
                  </div>
               </div>
            </div>
            <div class="card-footer" style="text-align: center">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#utilityModal">EXIT</button>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="restoreModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="form_restore" method="post" enctype="multipart/form-data">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  SQL File Upload
               </div>
               <div class="card-body">
                  <div class="form-group row" style="margin-top: 30px; margin-bottom: 30px">
                     <div class="col-md-12">
                        <label id="upload-btn" for="db_file" class="btn btn-secondary btn-sm btn-block">Click here to Upload File (.sql)</label>
                        <input type="text" readonly="true" placeholder="Uploaded file name will display here" name="uploaded_file_details" id="uploaded_file_details" style="line-height: 38px; width: 100%;" />
                        <input class="form-control inputfile ignore" name="db_file" id="db_file" type="file" />
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a id="btn_cancel_restore" class="btn btn-info btn-sm">CANCEL</a>
                  <button type="button" id="btn_submit_restore" class="btn btn-warning btn-sm float-right">RESTORE</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<style>   
   .inputfile {
      width: 0.1px;
      height: 0.1px;
      opacity: 0;
      overflow: hidden;
      position: absolute;
      z-index: -1;
   }
   .error {
      color: red;
      font-weight: normal;
   }
   .error:before {
      content: "* "
   }
   input {
      outline: 0;
      border-width: 0 0 1px;
      border-color: dimgray;
      color: dimgray;
   }
   input:focus {
      outline: 0;
      border-width: 0 0 1px;
      border-color: lightgray
   }
</style>