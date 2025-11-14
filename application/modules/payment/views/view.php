<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Payment</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Payment</li>
               <li class="breadcrumb-item">View</li>
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
                  <span class="float-left"><?php echo $payment_data->payment_ref_no; ?></span>
                  <a href="/payment/listing/<?php echo $listing_type; ?>" class="btn btn-info btn-sm float-right">Back</a>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-1"></div>
                     <div class="col-md-5">
                        <strong>To,</strong><br />
                        <strong><?php echo $supplier_name_code; ?></strong><br />
                        <?php echo $supplier_address; ?>
                     </div>
                     <div class="col-md-5" style="margin-top: 20px">
                        <div class="fl-right">
                           <strong>Date:</strong> <?php echo date('d-m-Y', strtotime($payment_date)); ?><br />

                           <strong>Bank:</strong>
                           <?php echo $bank; ?><br />

                           <strong>Cheque No:</strong>
                           <?php echo $cheque; ?><br />

                           <strong>Amount:</strong>
                           <?php echo $currency_code; ?> <?php echo number_format($amount, 2); ?> DR<br />

                        </div>
                     </div>
                     <div class="col-md-1"></div>
                  </div>

                  <div class="row" style="margin-top: 30px">
                     <div class="col-md-1"></div>
                     <div class="col-md-11 table-responsive">
                        <table align="center" style="max-width: 400px; width: 100%; border-collapse: collapse;">
                           <thead>
                              <tr>
                                 <th colspan="2">Payment details as follows :</th>
                              </tr>
                              <tr>
                                 <th style="background: #f5f5f5; border: 1px solid #ccc; text-align: left; padding: 10px;">Reference</th>
                                 <th style="background: #f5f5f5; border: 1px solid #ccc; text-align: right; padding: 10px;">Amount</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php echo $documentToRow; ?>
                           </tbody>
                        </table>
                     </div>   
                  </div>

                  <div class="row" style="margin-top: 30px">
                     <div class="col-md-1"></div>
                     <div class="col-md-10">
                        <strong>Remarks:</strong> <br /><?php echo $other_reference; ?>
                     </div>
                     <div class="col-md-1"></div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/payment/listing/<?php echo $listing_type; ?>" class="btn btn-info btn-sm">Back</a>
               </div>
            </div>
         </div>
      </div>

   </div>
</div>