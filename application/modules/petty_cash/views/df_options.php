<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Petty Cash</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/petty_cash/options">Petty Cash</a></li>
              <li class="breadcrumb-item">Utilities</li>
              <li class="breadcrumb-item">Datafiles</li>
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
         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  Backup <i class="fa-solid fa-download float-right"></i>
               </div>
               <div class="card-body">
                  Backup is the process of creating, managing, and storing copies of data files.
               </div>
               <div class="card-footer">
                  <button type="button" id="btn_backup" class="btn btn-warning float-right">Process Backup</button>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  Restore <i class="fa-regular fa-share-from-square float-right"></i>
               </div>
               <div class="card-body">
                  User can restore backup anytime to recover all Data's.
               </div>
               <div class="card-footer">
                  <a href="/petty_cash/df_restore" class="btn btn-warning float-right">Process Restore</a>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  Zap <i class="fa-regular fa-trash-can float-right"></i>
               </div>
               <div class="card-body">
                  Zap will remove all the records from each and every tables under Petty Cash.
               </div>
               <div class="card-footer">
                  <button type="button" id="btn_zap" class="btn btn-warning float-right">Process Zap</button>
               </div>
            </div>
         </div>

      </div>

   </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
   ::placeholder {
      color: gray;
      font-size: 0.9rem;
      font-style: italic;
   }

   .card-header {
      font-size: 1.2rem;
      color: dimgray;      
      font-variant: petite-caps;
      font-weight: 505;
      letter-spacing: 1px;
   }
   .card-body {
      font-style: italic;
      color: dimgray;
      user-select: none;
   }
   .card-header i {
      margin-top: 6px;
   }
</style>
<script type="text/javascript">
   var process = "";
   $(document).ready(function() {

      $('#btn_backup').click(function() {
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Backup Petty Cash");
         $('#confirmSubmitModal .modal-body').html("Are you sure that you want to do a backup of Datafiles?");
         process = "backup";
         $("#confirmSubmitModal").modal();
      });     

      $('#btn_zap').click(function() {
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Zap Database");
         $('#confirmSubmitModal .modal-body').html("Please do a backup of Petty Cash Datafiles before proceed to Zap. <br /><br />Zap Database will remove records from Petty Cash Datafiles in CRM Application.");
         process = "zap";
         $("#confirmSubmitModal").modal();
      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');

         if(process == "backup") {
            window.location = "/petty_cash/df_backup";
         } else if(process == "zap") {
            $('#reminderAlertModal .modal-title').html("ZAP Alert!");
            $('#reminderAlertModal .modal-body').html("Are you sure to ZAP Petty Cash Datafiles?");
            $('#reminderAlertModal').modal();            
         }
      });

      $('#btn-reminderAlertModal-ok').click(function() {
         window.location = "/petty_cash/df_zap";
      });

   });
</script>
