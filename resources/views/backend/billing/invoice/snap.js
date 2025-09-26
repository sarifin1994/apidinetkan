console.log(result);
                    var payment_method = result.payment_type;
                    console.log(payment_method);
                    if (invoice_id) {
                        $.ajax({
                            url: `/billing/member/getInvoice/${invoice_id}`,
                            type: "GET",
                            data: {
                                invoice_id: invoice_id,
                                ppp_id: ppp_id,
                            },
                            success: function(data) {
                                var id = data.data[0].id;
                                var full_name = data.data[0].member.full_name;
                                var no_invoice = data.data[0].no_invoice;
                                var invoice_date = data.data[0].invoice_date;
                                var amount = data.data[0].price;
                                var ppn = data.data[0].ppn;
                                var discount = data.data[0].discount;
                                var amount_ppn = amount * ppn / 100;
                                var amount_discount = amount * discount / 100;
                                var total_with_ppn_discount = parseInt(amount) + parseInt(
                                    amount_ppn) - parseInt(amount_discount);
                                var total_plus_ppn_discount = total_with_ppn_discount
                                    .toString();

                                var payment_type = data.data[0].payment_type;
                                var billing_period = data.data[0].billing_period;
                                var payment_url = data.data[0].payment_url;

                                var subscribe = data.data[0].subscribe;
                                var periode = data.data[0].period;
                                var due_date = data.data[0].due_date;

                                var member_id = data.data[0].member_id;
                                var id_member = data.data[0].member.id_member;
                                var nas = data.ppp[0].nas;
                                var no_wa = data.data[0].member.wa;

                                // collect data pppoe
                                var ppp_id = data.ppp[0].id;
                                var ppp_user = data.ppp[0].username;
                                var ppp_pass = data.ppp[0].value;
                                var ppp_profile = data.ppp[0].profile;
                                var ppp_status = data.ppp[0].status;
                                let invoice_id = id;
                                var admin_transaksi = 'system';
                                var data = {
                                    'admin_transaksi':admin_transaksi,
                                    'member_id': member_id,
                                    'id_member': id_member,
                                    'no_wa': no_wa,
                                    'subscribe': subscribe,
                                    'periode': periode,
                                    'full_name': full_name,
                                    'no_invoice': no_invoice,
                                    'invoice_date': invoice_date,
                                    'amount': amount,
                                    'ppn': ppn,
                                    'discount': discount,
                                    'payment_total': total_plus_ppn_discount,
                                    'payment_method': 2,
                                    'payment_type': payment_type,
                                    'billing_period': billing_period,
                                    'payment_url':payment_url,
                                    'due_date': due_date,
                                    'ppp_id': ppp_id,
                                    'ppp_status': ppp_status,
                                    'pppoe_user': ppp_user,
                                    'pppoe_pass': ppp_pass,
                                    'pppoe_profile': ppp_profile,
                                    'nas': nas,
                                }

                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]'
                                            )
                                            .attr('content')
                                    }
                                });

                                // ajax proses
                                $.ajax({
                                    url: `/billing/member/payInvoice/${invoice_id}`,
                                    type: "PUT",
                                    cache: false,
                                    data: data,
                                    dataType: "json",

                                    // tampilkan pesan Success

                                    success: function(data) {
                                        if (data.success) {
                                            location.reload();
                                        } 
                                    },

                                    error: function(err) {
                                        $("#message").html(
                                            "Some Error Occurred!"
                                        )
                                    }

                                });
                            }
                        });
                    }