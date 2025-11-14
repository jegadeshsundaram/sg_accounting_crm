<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">EZ Matrix</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item">EZ</li>
                <li class="breadcrumb-item active">Contra</li>
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
                <!-- form - starts -->
                <div class="card card-default">
                    <div class="card-header">
                        <h5 style="float: left; margin-top: 8px;">Contra</h5>
                        <a href="/ez_entry/creditor" class="btn btn-outline-secondary btn-sm float-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label" for="supplier">Supplier</label>
                                <select id="supplier" name="supplier" class="form-control">
                                    <?php echo $suppliers; ?>
                                </select>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                        <br />
                        <div class="row dv_currency" style="display: none">
                            <div class="col-md-6">
                                <label class="control-label" for="currency">Currency:</label>
                                <span class="currency"></span>
                            </div>
                            <div class="col-md-6"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 table-responsive" style="margin: 15px 0 30px">
                                <!-- Contra Balance Table -->
                                <table id="tbl_contra" class="tbl-main" align="center" style="width: 600px; margin-top: 10px; display: none;">
                                    <thead>
                                        <tr>
                                            <th class="header" colspan="5">Contra Payment</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 100px;">Date</th>
                                            <th style="width: 120px;">Reference</th>
                                            <th style="width: 180px; text-align: right">Debit</th>
                                            <th style="width: 180px; text-align: right">Credit</th>
                                            <th style="width: 200px; text-align: right">Balance</th>
                                        </tr>
                                    </thead>

                                    <tbody></tbody>

                                    <tfoot>
                                        <tr>
                                            <td id="final_ref"></td>
                                            <td id="final_balance" colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <input type="hidden" id="net_balance" />
                                <input type="hidden" id="entry_amount" />
                                <input type="hidden" id="settled_amount" />
                                <input type="hidden" id="unsettled_amount" />
                                <input type="hidden" id="splitted_total" />
                            </div>
                        </div>

                        <div class="row" id="dv_debits_credits" style="display: none">
                            <div class="col-md-6 table-responsive">

                                <!-- Credit Entry Table -->
                                <table id="tbl_credits" class="tbl-sub" style="width: 500px !important; margin-top: 30px">
                                    <thead>
                                        <tr>
                                            <th class="header" colspan="4">Credit's</th>
                                        </tr>
                                        <tr class="header_fields">
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th style="width: 200px; text-align: right;">Amount</th>
                                            <th style="width: 50px; text-align: center;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                            <div class="col-md-6 table-responsive">
                                
                                <!-- Debit Entry Table -->
                                <table id="tbl_debits" class="tbl-sub" style="width: 500px !important; margin-top: 30px">
                                    <thead>
                                        <tr>
                                            <th class="header" colspan="4">Debit's</th>
                                        </tr>
                                        <tr class="header_fields">
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th style="width: 200px; text-align: right;">Amount</th>
                                            <th style="width: 50px; text-align: center;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="btn_reset" class="btn btn-primary btn-sm" style="display: none">Reset Contra</button>
                        <button type="button" id="btn_submit" class="btn btn-info btn-sm float-right">SUBMIT</button>
                    </div>
                </div>
            </div>
        </div>
   </div> <!-- container-fluid - ends -->
</div>

<form id="frm_" name="frm_" method="post" action="#">
    <input type="hidden" name="ap_ids" id="ap_ids" />
    <input type="hidden" name="final_balance_entry_id" id="final_balance_entry_id" />
    <input type="hidden" name="final_balance_entry_reference" id="final_balance_entry_reference" />
    <input type="hidden" name="final_balance_amount" id="final_balance_amount" />
</form>

<!-- Model of new row -->
<table id="tbl_clone" style="display: none">
    <tbody>
        <tr id="0">
            <td class="entry d-none"></td>
            <td class="doc_date"></td>
            <td class="ref_no"></td>
            <td class="dr_amount"></td>
            <td class="cr_amount"></td>
            <td class="entry_balance"></td>
        </tr>
   </tbody>
</table>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="/application/modules/ez_entry/js/creditor_contra.js"></script>
<style>
    .select2-container {
        width: 100% !important;
    }
    .details {
        padding: 10px 10px 10px 20px;
        color: dimgray;
        border-radius: 5px;
    }
    table td {
        color: #263544;
        letter-spacing: 1px;
    }
    .tbl-main .header {
        background: gray !important;
        font-size: 1.1rem;
        color: #fff;
        padding: 8px 10px;
        letter-spacing: 1px;
    }
    .tbl-sub .header {
        background: lavender !important;
        font-size: 1rem;
        color: #000;
        padding: 8px 10px;
        letter-spacing: 1px;
        border: 1px solid lavender;
    }
    #balance_entry_reference {   
        color: dimgray;
        font-style: italic;
        font-weight: 600;
    }
    #final_balance_amount_display {
        color: black;
        font-weight: 500;
        margin-left: 25px;
    }
    .tbl-main th {
        background: gainsboro;
        padding: 8px 10px;
    }
    .tbl-sub th {
        border: 1px solid gainsboro;
        padding: 8px 10px;
    }
    .tbl-main tr td, .tbl-sub tr td {
        border: 1px solid gainsboro;
        padding: 10px;
    }
    tfoot td {
        border: none !important;
        border-top: 2px solid gainsboro !important;
        border-bottom: 2px solid gainsboro !important;
    }
    .check-container {
        cursor: pointer;
    }
    .checkmark {
        position: absolute;
        top: -3px;
        left: 2px;
        height: 30px;
        width: 30px;
        background-color: skyblue;
        border-radius: 3px;
    }
    .check-container .checkmark::after {
        left: 11px;
        top: 2px;
        width: 10px;
        height: 18px;
    }
    .credit_amount, .debit_amount {
        text-align: right;
    }
    .amount {
        text-align: right;
    }
    .tbl-disable {
        background: gainsboro;
        opacity: 0.5;
        user-select: none;
        cursor: not-allowed;
    }
    .disabled {
        background: gray !important;
        opacity: 0.5;
        cursor: not-allowed;
    }
    .dr_amount, .cr_amount, .entry_balance {
        text-align: right;
    }
    .hidden {
        display: none;
    }
</style>
