<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST Returns</h1>
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
            <form id="frm_" action="#" method="POST">
               <input type="hidden" name="fi_id" id="fi_id" value="<?php echo $fi_id; ?>" />
               <div class="card card-default">  
                  <div class="card-header">
                     <h5 class="float-left">JSON Request</h5>
                     <button type="button" class="btn btn-danger btn-sm float-right btn_copy">Copy JSON</button>
                  </div>
                  <div class="card-body">
                     <div class="row gst-section" style="padding-left: 15px; font-size: 17px;">
                        <div class="col-md-12">
                           <pre id="json"></pre>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/gst/iras_api" class="btn btn-info btn-sm">EXIT</a>
                     <button type="button" class="btn btn-danger btn-sm float-right btn_copy">Copy JSON</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<style type="text/css">
   .gst-section input, label {
      cursor: pointer;
      font-weight: normal;
   }
   .gst-section label {
      display: unset;
   }
   pre {
      overflow: auto;
      font-size: 17px;
   }
</style>

<script>
   // document starts
   $(function() {
      document.getElementById("json").textContent = JSON.stringify(<?php echo $json_request_data; ?>, undefined, 2);      
   });

   function copyJSON(element_id) {
      element_id.setAttribute("contentEditable", true);
      element_id.setAttribute("onfocus", "document.execCommand('selectAll',false,null)");
      element_id.focus();
      document.execCommand("copy");
      element_id.removeAttribute("contentEditable");
   }

   document.body.addEventListener('click', function (evt) {
      if (evt.target.className === 'btn btn-danger btn-sm float-right btn_copy') {
         copyJSON(document.getElementById("json"));
         alert("JSON Copied");
      }
   }, false);

</script>
