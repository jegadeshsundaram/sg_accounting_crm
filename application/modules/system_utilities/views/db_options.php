<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Database</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">System Manager</li>
               <li class="breadcrumb-item">Utilities</li>
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
                  Database backup is the process of creating, managing, and storing copies of data in case it's lost, corrupted, or damaged. Database backups allow users to recover data before it becomes unusable.
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
                  A copy of data that can be used to restore and recover the data after a failure.
                  User can do backup of complete database before do any major updates and can restore the backup if anything goes wrong.
               </div>
               <div class="card-footer">
                  <a href="/system_utilities/db_restore" class="btn btn-warning float-right">Process Restore</a>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  Zap <i class="fa-regular fa-trash-can float-right"></i>
               </div>
               <div class="card-body">
                  Zap Database will remove records from each and every table in the database. User should make sure to take backup of database before do zap because if user done zap database without backup then it can not be reverted.
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
   
   .fa {
      margin-top: 5px;
   }
</style>
<script type="text/javascript">
   var process = "";
   $(document).ready(function() {

      $('#btn_backup').click(function() {
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Backup Full Database");
         $('#confirmSubmitModal .modal-body').html("Are you sure that you want to do a backup of full database?");
         process = "backup";
         $("#confirmSubmitModal").modal();
      });     

      $('#btn_zap').click(function() {
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Zap Database");
         $('#confirmSubmitModal .modal-body').html("Please do a backup of complete database before proceed to Zap. <br /><br />Zap Database will remove records from all the datafiles in CRM Application.");
         process = "zap";
         $("#confirmSubmitModal").modal();
      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');

         if(process == "backup") {            
            window.location = "/system_utilities/db_backup";
         } else if(process == "zap") {
            $('#reminderAlertModal .modal-title').html("ZAP Alert!");
            $('#reminderAlertModal .modal-body').html("Are you sure to ZAP Database?");
            $('#reminderAlertModal').modal();            
         }
      });

      $('#btn-reminderAlertModal-ok').click(function() {
         window.location = "/system_utilities/db_zap";
      });

   });
</script>
