var url = '';
$(function() {

    $('select').select2();

    $("#customer").change(function(event) {

        reset_contra();
        
        id = $("#customer option:selected").val();
        if (id !== "") {
            $.post('/ez_entry/ajax/get_customer_debits_credits', {
                customer_id: id
            }, function (data) {
                var obj = $.parseJSON(data);

                $('.currency').text(obj.currency);
                $('.dv_currency').show();

                $("#tbl_debits tbody").html(obj.debit_entries);
                $("#tbl_credits tbody").html(obj.credit_entries);

                if(obj.credits == 0) {
                    $("#tbl_credits .header_fields").hide();
                } else {
                    $("#tbl_credits .header_fields").show();
                }

                if(obj.debits == 0) {
                    $("#tbl_debits .header_fields").hide();
                } else {
                    $("#tbl_debits .header_fields").show();
                }

                $('#dv_debits_credits').show();

            });
        } else {
            $(".currency").hide();
        }
    });

    var contra_transaction_ids = [];

    // On check - Debit or Credit Entries
    $(document).on("click", ".entry_check", function() {
        if($(this).prop("checked") == true) {
            var entry_id = $(this).closest('tr').attr('id');
            var entry = $(this).closest('tr').find('td.entry').html();
            var entry_amount = $(this).closest('tr').find('td.amount').html();
            $('#entry_amount').val(entry_amount.replace(new RegExp(',', 'g'), ''));

            contra_transaction_ids.push(entry_id);

            process_contra(entry_id, entry);
        }
    });

    $('#btn_reset').on('click', function() {
        $.confirm({
            title: "<i class='fa fa-info'></i> RESET?",
            content: "<span style='color: brown; font-style: italic'>Are you sure to reset this Contra?</span>",
            buttons: {
                yes: {
                    btnClass: 'btn-warning',
                    action: function(){
                        reset_contra();
                    }
                },
                no: {
                    btnClass: 'btn-dark',
                    action: function(){
                    }
                },
            }
        });
    });

    $('#btn_submit').on('click', function() {
        var url = '/ez_entry/save_debtor_contra';
        $('#ar_ids').val(contra_transaction_ids);
        if($('#ar_ids').val() == "") {
            alert("No entries selected");
        } else {
  
            console.log("Entries Selected :: "+$('#ar_ids').val());
            console.log("Final Balance Entry ID :: "+$('#final_balance_entry_id').val());
            console.log("Final Balance Entry REFERENCE :: "+$('#final_balance_entry_reference').val());
            console.log("Final Balance TOTAL :: "+$('#final_balance_amount').val());

            $('#ar_ids').val(removeDuplicates(contra_transaction_ids));
            console.log("Entries Selected :: "+$('#ar_ids').val());
  
            $("#frm_").attr("action", url);
            $("#frm_").submit();
        }
    });

}); // document ends

    function removeDuplicates(arr) {
        let unique = [];
        for (i = 0; i < arr.length; i++) {
            if (unique.indexOf(arr[i]) === -1) {
                unique.push(arr[i]);
            }
        }
        return unique;
    }

    function process_contra(entry_id, entry) {
        if ($('#tbl_contra tbody tr').length == 0) {
            
            add_entry(entry_id, entry, 'F'); // F - First Entry

            $('#tbl_contra').show();
            $('#btn_reset').show();

        } else { // from 2nd entry

            // net balance
            var net_balance = Number($('#net_balance').val());

            // Selected entry amount
            var entry_amount = Number($('#entry_amount').val());

            // balance amount after contra
            var balance_amount = 0;
            if(entry == "DR") {
                balance_amount = net_balance + entry_amount;
            } else if(entry == "CR") {
                balance_amount = (-1 * entry_amount) + net_balance;
            }

            console.log("\n**** Add ENTRY - START ****");
            console.log("1. Net Balance >>>> "+net_balance);
            console.log("2. Entry Amount >>>> "+entry_amount);
            console.log("3. Balance Amount >>>> "+balance_amount);

            var settled_amount = 0;
            var un_settled_amount = 0;

            if(balance_amount == 0) { // all settled

                console.log("\n Entry::"+entry+" - Balance = 0\n");

                clear_entry();

                // get last entry id before adding entry
                var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');                
                
                // Step 1: Insert selected entry as it is
                add_entry(entry_id, entry, 'B'); // B - Balanced Entry
                
                // Step 2: if any entry splitted before which should combine together and insert as single entry and append to last row                    
                var splitted_entries = $('#tbl_contra tbody tr[id="'+last_entry_id+'"]').length;
                console.log("ID = "+last_entry_id+" Entries = "+splitted_entries);
                if(splitted_entries > 1) {
                    merge_splitted_entries(last_entry_id);
                }                

            } else if(balance_amount > 0 && entry == "DR") { // split debit entry

                console.log("\nDebit Entry - Balance > 0\n");

                settled_amount = entry_amount - balance_amount;
                un_settled_amount = balance_amount;

                $('#settled_amount').val(settled_amount);
                $('#unsettled_amount').val(un_settled_amount);

                console.log("4. Settled Amount >>>> "+settled_amount);
                console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

                // Step 1: if any entry splitted before which should combine together and insert as single entry and append to last row 
                var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');
                var splitted_entries = $('#tbl_contra tbody tr[id="'+last_entry_id+'"]').length;
                console.log("ID = "+last_entry_id+" Entries = "+splitted_entries);
                if(splitted_entries > 1) {
                    merge_splitted_entries(last_entry_id);
                }

                // Step 2: add debit settled row
                add_entry(entry_id, 'DR', 'S'); // S - Settled Entry

                // Step 3: add debit un-settled row
                add_entry(entry_id, 'DR', 'U'); // U - Unsettled Entry
            
            } else if(balance_amount > 0 && entry == "CR") {

                console.log("\nCredit Entry - Balance > 0\n");
                
                settled_amount = net_balance - balance_amount;
                un_settled_amount = balance_amount;

                $('#settled_amount').val(settled_amount);
                $('#unsettled_amount').val(un_settled_amount);

                console.log("4. Settled Amount >>>> "+settled_amount);
                console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

                // update last entry amount with settled amount
                $("#tbl_contra tbody tr:last").find("td.dr_amount").html(settled_amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

                // Step 1: if any entry splitted before which should combine together and insert as single entry and append to last row 
                // get last entry id before adding entry
                var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');

                // Step 2: Add credit settled 'CR' entry
                add_entry(entry_id, entry, 'S'); // S - Settled Entry

                var splitted_entries = $('#tbl_contra tbody tr[id="'+last_entry_id+'"]').length;
                console.log("\n\nID = "+last_entry_id+" Entries = "+splitted_entries);
                if(splitted_entries > 1) {
                    merge_splitted_entries(last_entry_id);

                    add_entry(last_entry_id, 'DR', 'U'); // U - UnSettled Entry
                }

                if(splitted_entries == 1) {
                    $('#'+last_entry_id).remove();
                    add_entry(last_entry_id, 'DR', 'S'); // S - Settled Entry
                    add_entry(last_entry_id, 'DR', 'U'); // U - UnSettled Entry
                }                    
            
            } else if(balance_amount < 0 && entry == "DR") { // split credit entry

                console.log("\nDebit Entry - Balance < 0\n");

                settled_amount = net_balance + ((-1) * balance_amount);
                un_settled_amount = balance_amount;
                
                console.log("4. Settled Amount >>>> "+settled_amount);
                console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

                $('#settled_amount').val(settled_amount);
                $('#unsettled_amount').val(un_settled_amount);

                // update top entry amount with settled amount
                if(settled_amount < 0) {
                    $("#tbl_contra tbody tr:last").find("td.cr_amount").html((-1 * settled_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                } else {
                    $("#tbl_contra tbody tr:last").find("td.cr_amount").html(settled_amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                }
                
                // Step 1: if any entry splitted before which should combine together and insert as single entry and append to last row 
                // get last entry id before adding entry
                var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');

                // Step 2: Add the settled 'DR' entry
                $('#settled_amount').val((-1 * settled_amount));
                add_entry(entry_id, entry, 'S'); // S - Settled Entry

                var splitted_entries = $('#tbl_contra tbody tr[id="'+last_entry_id+'"]').length;
                console.log("ID = "+last_entry_id+" Entries = "+splitted_entries);
                if(splitted_entries > 1) {
                    merge_splitted_entries(last_entry_id);

                    // add Credit Unsettled record
                    add_entry(last_entry_id, 'CR', 'U'); // U - UnSettled Entry
                }

                if(splitted_entries == 1) {
                    $('#'+last_entry_id).remove();
                    $('#settled_amount').val(settled_amount);
                    add_entry(last_entry_id, 'CR', 'S'); // S - Settled Entry
                    add_entry(last_entry_id, 'CR', 'U'); // U - UnSettled Entry
                }                    

            } else if(balance_amount < 0 && entry == "CR") {

                console.log("\nCredit Entry - Balance < 0\n");

                settled_amount = balance_amount + entry_amount;
                un_settled_amount = balance_amount;

                $('#settled_amount').val(settled_amount);
                $('#unsettled_amount').val(un_settled_amount);

                console.log("4. Settled Amount >>>> "+settled_amount);
                console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

                // Step 1: if any entry splitted before which should combine together and insert as single entry and append to last row 
                // get last entry id before adding entry
                var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');
                var splitted_entries = $('#tbl_contra tbody tr[id="'+last_entry_id+'"]').length;
                console.log("ID = "+last_entry_id+" Entries = "+splitted_entries);
                if(splitted_entries > 1) {
                    merge_splitted_entries(last_entry_id);
                }

                // Step 2: add credit settled row
                $('#settled_amount').val((-1 * settled_amount));
                add_entry(entry_id, 'CR', 'S'); // S - Settled Entry

                // Step 3: add credit un-settled row
                add_entry(entry_id, 'CR', 'U'); // U - Unsettled Entry
            }
        }

        $('#contra_'+entry_id).prop('disabled', true);
        $('#contra_'+entry_id).parents("tr").find(".checkmark").addClass("disabled");
        process_final_balance();
        process_running_balance();
    }

    function merge_splitted_entries(last_entry_id) {
        $last_row = $('#tbl_contra tbody tr[id="'+last_entry_id+'"]');
        var entry_total = 0;
        $($last_row).each(function() {
            if($(this).find('td.entry').html() == "DR") {
                entry_total += parseFloat($(this).find('td.dr_amount').html().replace(new RegExp(',', 'g'), ''));
            } else if($(this).find('td.entry').html() == "CR") {
                entry_total += parseFloat($(this).find('td.cr_amount').html().replace(new RegExp(',', 'g'), ''));
            }
            $(this).remove();
        });

        $('#splitted_total').val(entry_total);

        var last_entry = $last_row.find('td.entry').html();
        //console.log("Merging - Add Single Entry :: "+last_entry_id+', '+last_entry+', '+entry_total);
        add_entry(last_entry_id, last_entry, 'M'); // M - Merge Entry
    }

    function add_entry(entry_id, entry, status) {

        //console.log("Entry Details :: ID = "+entry_id+", Type = "+entry+", Status = "+status);

        var new_row = $("#tbl_clone tbody tr").clone();
        var copy_row = '';

        if(entry == "DR") {
            
            copy_row = $('#tbl_debits tbody tr[id="'+entry_id+'"]');
            var dr_amount = 0;
            if(status == 'F' || status == 'B') { // first / balanced
                dr_amount = copy_row.find('td.amount').html().replace(/,/g,'');
            } else if(status == 'M') { // merged
                dr_amount = parseFloat($('#splitted_total').val()).toFixed(2);
            } else if(status == 'S') { // settled
                dr_amount = parseFloat($('#settled_amount').val()).toFixed(2);
            } else if(status == 'U') { // un settled
                dr_amount = parseFloat($('#unsettled_amount').val()).toFixed(2);
            }

            console.log("DR Amount >>> "+dr_amount);

            if(dr_amount < 0) {
                dr_amount = (-1) * dr_amount;
            }

            dr_amount = parseFloat(dr_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            new_row.find('td.dr_amount').html(dr_amount);

        } else if(entry == "CR") {

            copy_row = $('#tbl_credits tbody tr[id="'+entry_id+'"]');

            var cr_amount = 0;
            if(status == 'F' || status == 'B') { // first / balanced
                cr_amount = copy_row.find('td.amount').html().replace(/,/g,'');
            } else if(status == 'M') { // merged
                cr_amount = parseFloat($('#splitted_total').val()).toFixed(2);
            } else if(status == 'S') { // settled
                cr_amount = parseFloat($('#settled_amount').val()).toFixed(2);
            } else if(status == 'U') { // un settled
                cr_amount = parseFloat($('#unsettled_amount').val()).toFixed(2);
            }

            //console.log("CR Amount >>> "+cr_amount.replace(/\d(?=(\d{3})+\.)/g, '$&,'));

            if(cr_amount < 0) {
                cr_amount = (-1) * cr_amount;
            }

            
            cr_amount = parseFloat(cr_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            new_row.find('td.cr_amount').html(cr_amount);
        }

        new_row.find('td.entry').html(copy_row.find('td.entry').html());
        new_row.find('td.doc_date').html(copy_row.find('td.doc_date').html());
        new_row.find('td.ref_no').html(copy_row.find('td.ref_no').html());

        new_row.attr('id', entry_id);

        if(status == "U") { // hide unsettled entry in the contra table as last row
            new_row.addClass('hidden');
        }

        $('#tbl_contra').append(new_row);
    }

    function enable_tbl(tbl) {
        if(tbl == "dr_cr") {

            $('.entry_check').prop('disabled', true);
            $('.entry_check').parents("tr").find(".checkmark").addClass("disabled");

            $('#tbl_debits').addClass('tbl-disable');
            $('#tbl_credits').addClass('tbl-disable');

        } else if(tbl == "dr") {

            // If already checked, then disable it always
            $('#tbl_debits tbody tr').each(function() {
                $(this).find('.entry_check').each(function() {
                if($(this).prop("checked") == true) {
                    $(this).prop('disabled', true);
                    $(this).parents("tr").find(".checkmark").addClass("disabled");
                } else {
                    $(this).prop('disabled', false);
                    $(this).parents("tr").find(".checkmark").removeClass("disabled");
                }
                });
            });

            $('#tbl_credits .entry_check').prop('disabled', true);
            $('#tbl_credits .entry_check').parents("tr").find(".checkmark").addClass("disabled");

            $('#tbl_debits').removeClass('tbl-disable');
            $('#tbl_credits').addClass('tbl-disable');

        } else if(tbl == "cr") {

            // If already checked, then disable it always
            $('#tbl_credits tbody tr').each(function() { 
                $(this).find('.entry_check').each(function() {
                if($(this).prop("checked") == true) {
                    $(this).prop('disabled', true);
                    $(this).parents("tr").find(".checkmark").addClass("disabled");
                } else {
                    $(this).prop('disabled', false);
                    $(this).parents("tr").find(".checkmark").removeClass("disabled");
                }
                });
            });

            $('#tbl_debits .entry_check').prop('disabled', true);
            $('#tbl_debits .entry_check').parents("tr").find(".checkmark").addClass("disabled");

            $('#tbl_credits').removeClass('tbl-disable');
            $('#tbl_debits').addClass('tbl-disable');
        }
    }

    function process_final_balance() {
        var debits_total = 0;
        var credits_total = 0;
        $("#tbl_contra tbody tr").each(function() {
            if($(this).find('td.entry').html() == "DR") {
                debits_total += parseFloat($(this).find('td.dr_amount').html().replace(new RegExp(',', 'g'), ''));
                //console.log("DR Entry = "+$(this).find('td.dr_amount').html());
            } else if($(this).find('td.entry').html() == "CR") {
                credits_total += parseFloat($(this).find('td.cr_amount').html().replace(new RegExp(',', 'g'), ''));
                //console.log("CR Entry = "+$(this).find('td.cr_amount').html());
            }
        });

        //console.log("Debits - Credits = "+debits_total+' - '+credits_total);

        var final_balance = debits_total - credits_total;
        $('#net_balance').val(final_balance);

        var last_row = $('#tbl_contra tbody tr:last');
        $('#final_ref').html(last_row.find('td.ref_no').html());

        if(final_balance < 0) {
            $('#final_balance').html("(" + (-1 * final_balance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ")");
            $('#final_balance_amount').val((-1) * final_balance);
        } else {
            $('#final_balance').html(final_balance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            $('#final_balance_amount').val(final_balance);
        }

        $final_tr = $('#tbl_contra tbody tr:last');
        $('#final_balance_entry_id').val($final_tr.attr('id'));
        $('#final_balance_entry_reference').val($final_tr.find('td.ref_no').html());

        if (final_balance < 0) {
            
            enable_tbl("dr");

        } else if (final_balance == 0) {

            $('#final_balance_entry_id').val("");
            $('#final_balance_entry_reference').val("");
            $('#final_balance_amount').val("");

            $('#final_ref').html('Balance');
            enable_tbl("dr_cr");            

        } else if (final_balance > 0) {
            
            enable_tbl("cr");
        }  
    }

    function process_running_balance() {
        var balance = 0;
        $("#tbl_contra tbody tr").each(function() {
            debit = 0;
            credit = 0;

            debit = $(this).find('td.dr_amount').html();
            if(debit !== "") {
                balance += parseFloat(debit.replace(new RegExp(',', 'g'), '')); 
            }

            credit = $(this).find('td.cr_amount').html();
            if(credit !== "") {
                balance -= parseFloat(credit.replace(new RegExp(',', 'g'), ''));
            }

            if(balance < 0) {
                $(this).find('td.entry_balance').html("(" + (-1 * balance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ")");
            } else {
                $(this).find('td.entry_balance').html(balance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            }
        });
    }
    
    function reset_contra() {
        contra_transaction_ids = [];
        $("#tbl_contra > tbody").html("");
        
        $("#tbl_contra").hide();

        $('.entry_check').prop('disabled', false);
        $('.entry_check').parents("tr").find(".checkmark").removeClass("disabled");
        $('.entry_check').prop('checked', false);
        $("#dv_debits_credits").show();

        $('#tbl_credits, #tbl_debits').removeClass('tbl-disable');

        $('#print_btn').hide();
        $('#reset_contra').hide();

        clear_entry();
    }

    function clear_entry() {
        $('#net_balance').val("");
        $('#entry_amount').val("");
        $('#settled_amount').val("");
        $('#unsettled_amount').val("");
        $('#splitted_total').val("");
    }    