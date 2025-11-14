<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST Opening Balance</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GST</li>
               <li class="breadcrumb-item">Opening Balance</li>
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
            <input type="hidden" id="redirect_url" value="/gst" />
            <input type="hidden" id="process" />
            <input type="hidden" id="edit_id" />

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">

               <input type="hidden" name="gst_type" id="gst_type" value="<?php echo $gst_type; ?>" />
            
               <div class="card card-default">
                  <div class="card-header">
                     <h5><?php echo $gst_type == 'I' ? 'Input' : 'Output'; ?> Tax</h5>
                     <a href="/gst/opening_balance" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>

                  <div class="card-body">
                     <div class="row form-group">
                        <label class="control-label col-md-3">Date : </label>
                        <div class="col-md-3">
                        <input 
                           type="text" 
                           id="doc_date" name="doc_date" 
                           value="<?php echo $page == 'edit' ? date('d-m-Y', strtotime($doc_date)) : ''; ?>"
                           class="form-control dp_full_date doc_date w-120" 
                           placeholder="dd-mm-yyyy" />
                        </div>
                     </div>

                     <div class="row form-group">
                        <label class="control-label col-md-3">Reference : </label><br />
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="ref_no" name="ref_no" 
                              class="form-control ref_no w-150" 
                              value="<?php echo $ref_no; ?>"
                              maxlength="12" />

                           <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
                           <span id="ref_error" style="display: none; color: red;">Duplicate reference disallowed</span>
                        </div>
                     </div>

                     <div class="row form-group">
                        <label class="control-label col-md-3">Remarks : </label><br />
                        <div class="col-md-3">
                           <textarea id="remarks" name="remarks" class="form-control" maxlength="250"><?php echo $remarks; ?></textarea>
                        </div>
                     </div>

                     <br />

                     <div class="row form-group">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 900px; width: 100%; display: <?php echo $page == 'edit' ? 'inline-table' : 'none'; ?>">
                              <thead>
                                 <tr>
                                    <th class="w-120">Action</th>
                                    <th class="w-350">GST</th>
                                    <th><?php echo $gst_type == 'I' ? 'Supplier' : 'Customer'; ?></th>
                                    <th class="w-180">Amount</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php if ($page == 'edit') {
                                 $i = 0;
                                 $this->db->select('*');
                                 $this->db->from('gst_open');
                                 $this->db->where(['date' => $doc_date, 'dref' => $ref_no, 'status' => 'C']);
                                 $query = $this->db->get();
                                 $ob_entries = $query->result();

                                 foreach ($ob_entries as $value) { ?>

                                    <tr id="<?php echo $i; ?>">
                                       <td>
                                          <input 
                                             type="hidden" 
                                             id="ob_id_<?php echo $i; ?>" name="ob_id[]" 
                                             value="<?php echo $value->ob_id; ?>"
                                             class="ob_id" />

                                          <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                          <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                       </td>
                                       <td>
                                          <?php $gst_desc = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gstcate]); ?>
                                          <input 
                                             type="text" 
                                             id="gst_desc_<?php echo $i; ?>" 
                                             value="<?php echo $value->gstcate.' : '.$gst_desc; ?>"
                                             class="form-control-dsply gst_desc txt-cutoff" readonly />
                                          
                                          <input type="hidden" id="gst_code_<?php echo $i; ?>" name="gst_code[]" class="gst_code" />
                                       </td>
                                       <td>
                                          <?php 
                                             if ($value->gsttype == 'I') {
                                                $tbl = 'master_supplier';
                                             } elseif($value->gsttype == 'O') {
                                                $tbl = 'master_customer';
                                             }
                                             $name = $this->custom->getSingleValue($tbl, 'name', ['code' => $value->iden]);
                                          ?>

                                          <input 
                                             type="text" 
                                             id="iden_details_<?php echo $i; ?>" 
                                             value="<?php echo '('.$value->iden.') '.$name; ?>"
                                             class="form-control-dsply iden_details" readonly />

                                          <input 
                                             type="hidden" 
                                             id="iden_<?php echo $i; ?>" name="iden[]"
                                             value="<?php $value->iden; ?>"
                                             class="form-control-dsply iden" readonly />
                                       </td>
                                       <td>
                                          <input 
                                             type="number" 
                                             id="amount_<?php echo $i; ?>" name="amount[]" 
                                             value="<?php echo $value->amou; ?>"
                                             class="form-control-dsply amount" readonly />
                                       </td>
                                    </tr>
                                 <?php ++$i;
                                 } }?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12">
                           <a class="btn_add_item btn btn-outline-danger btn-sm" style="margin-right: 10px;"><i class="fa-solid fa-plus"></i> ADD ENTRY</a>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/gst/opening_balance" class="btn btn-info btn-sm">Cancel</a>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Model of new row -->
<table id="tbl_clone" style="display: none">
   <tbody>
      <tr id="0">
         <td>
            <input 
               type="hidden" 
               id="ob_id_0" name="ob_id[]" class="ob_id" />

            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
            <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>
         <td>
            <input 
               type="text" 
               id="gst_desc_0" class="form-control-dsply gst_desc txt-cutoff" readonly />
            
            <input type="hidden" id="gst_code_0" name="gst_code[]" class="gst_code" />
         </td>
         <td>
            <input 
               type="text" 
               id="iden_details_0" class="form-control-dsply iden_details" readonly />

            <input 
               type="hidden" 
               id="iden_0" name="iden[]" 
               class="form-control-dsply iden" readonly />
         </td>
         <td>
            <input 
               type="number" 
               id="amount_0" name="amount[]" 
               class="form-control-dsply amount" readonly />
         </td>
      </tr>
   </tbody>
</table>


<!-- Modal :: Entry -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="ob_id" />

               <div class="row mt-10">
                  <label class="control-label col-12">GST Category <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="gst_category" class="form-control">
                        <?php echo $gsts; ?>
                     </select>
                  </div>
               </div>

               <hr />

               <div class="row mt-10 entry_field" style="display: none">
                  <label class="control-label col-12">Iden <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="iden" class="form-control">
                        <?php echo $idens; ?>
                     </select>
                  </div>
               </div>

               <div class="row mt-10 entry_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label">Amount</label>
                     <input type="number" id="amount" class="form-control" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save">SAVE</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript" src="/application/modules/gst/js/process.js"></script>
<script src="/assets/js/exit.js"></script>