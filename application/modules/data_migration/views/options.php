<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Data File's Migration</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item active">Migration Files</li>
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
                  Master Files
               </div>
               <div class="card-body">
                  <a href="/data_migration/import/customer" class="btn btn-outline-secondary btn-sm btn-block">Customer & Supplier Master</a>
                  <a href="/data_migration/import/billing" class="btn btn-outline-secondary btn-sm btn-block">Billing Master</a>
                  <a href="/data_migration/import/employee" class="btn btn-outline-secondary btn-sm btn-block">Employee Master</a>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  Combo Tables
               </div>
               <div class="card-body">
                  <a href="/data_migration/import/purchase_gst" class="btn btn-outline-secondary btn-sm btn-block">GST Master - Purchase Items</a>
                  <a href="/data_migration/import/supply_gst" class="btn btn-outline-secondary btn-sm btn-block">GST Master - Supply Items</a>
                  <a href="/data_migration/import/forex" class="btn btn-outline-secondary btn-sm btn-block">Forex Master</a>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  Foreign Bank
               </div>
               <div class="card-body">                  
                  <a href="/data_migration/import/fb_master" class="btn btn-outline-secondary btn-sm btn-block">Foreign Bank Master</a>
                  <a href="/data_migration/import/fb_ledger" class="btn btn-outline-secondary btn-sm btn-block">Foreign Bank Subleder</a>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  AR & AP
               </div>
               <div class="card-body">                  
                  <a href="/data_migration/import/ar" class="btn btn-outline-secondary btn-sm btn-block">Accounts Receivable</a>
                  <a href="/data_migration/import/ap" class="btn btn-outline-secondary btn-sm btn-block">Accounts Payable</a>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-6">
            <div class="card card-default">
               <div class="card-header">
                  GL & GST
               </div>
               <div class="card-body">                  
                  <a href="/data_migration/import/coa" class="btn btn-outline-secondary btn-sm btn-block">Chart Of Account</a>
                  <a href="/data_migration/import/gl" class="btn btn-outline-secondary btn-sm btn-block">General Ledger</a>
                  <a href="/data_migration/import/gst" class="btn btn-outline-secondary btn-sm btn-block">GST Subledger</a>
               </div>
            </div>
         </div>

      </div>

   </div>
</div>

<style>
   .card-header {
      font-size: 1.2rem;
      color: dimgray;
      font-style: italic;
      font-variant: petite-caps;
      font-weight: 505;
      letter-spacing: 1px;
   }
   .btn {
      font-variant: all-petite-caps;
      font-size: 1rem;
      text-align: left;
   }
   .btn-sm {
      padding: .25rem 1.5rem;
   }
</style>