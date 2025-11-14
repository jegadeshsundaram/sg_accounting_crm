<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">EZ Entry</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">EZ Entry</li>
               <li class="breadcrumb-item active">Options</li>
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
            <input type="hidden" id="module" value="ez_entry" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/batch_sales">
                           Credit Sales <span>User can create as many as Sales transaction's in a batch</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/batch_purchase">
                           Credit Purchases <span>User can create as many as Purchase transaction's in a batch</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/batch_receipt">
                           AR Receipt <span>User can create as many as Receipt transaction's in a batch</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/batch_settlement">
                           AP Settlement <span>User can create as many as Settlement transaction's in a batch</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/other_payment">
                           Other Payment <span>User can create as many as Payment transaction's in a batch</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/other_adjustment">
                           Other Adjustment <span>other any kind of transaction's can be created here</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/debtor">
                           Debtor Contra <span>Contra all the Debtor transactions here</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/ez_entry/creditor">
                           Creditor Contra <span>Contra all the Creditor transactions here</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of GL Datafile's</span>
                        </a>
                     </div>

                  </div>
               </div>
               <div class="card-footer">
                  <a href="/dashboard" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Dashboard
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<?php require_once APPPATH.'/modules/includes/modal/utility.php'; ?>
<script src="/assets/js/modal/utility.js"></script>