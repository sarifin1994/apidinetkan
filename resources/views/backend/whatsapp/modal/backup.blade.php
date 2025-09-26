$('#tipe').change(function() {
    let tipe = $(this).val();
    if (tipe === 'all') {
        $('#show_suspend').hide()
        $('#show_byarea').hide()
        $('#show_byodp').hide()
        $('#show_all').show()
        $.ajax({
            url: `/whatsapp/broadcast/getAllUserActive`,
            type: "GET",
            cache: false,
            success: function(data) {

                $('#fill_jmlpelanggan_all').html(data.countuser);
                let wa = []
                $.each(data.data, function(index, row) {
                    if (row.wa !== null) {
                        wa.push(row.wa)
                    }
                })

                $('#sendBroadcast').click(function(e) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        icon: 'warning',
                        text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                        showCancelButton: !0,
                        reverseButtons: !0,
                        confirmButtonText: "Ya, Kirim!",
                        cancelButtonText: "Batal",
                        confirmButtonColor: "#d33",
                        // cancelButtonColor: "#d33",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            let timerInterval;
                            Swal.fire({
                                title: "Mengirim pesan",
                                icon: "info",
                                html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                timer: 9999999,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup()
                                        .querySelector("b");
                                    timerInterval = setInterval(
                                        () => {
                                            timer.textContent =
                                                `${Swal.getTimerLeft()}`;
                                        }, 9999999);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal
                                    .DismissReason.timer) {
                                    console.log("Timeout");
                                }
                            });
                            $('#sendBroadcast').attr("disabled", true);
                            $("#sendBroadcast").html(
                                'Kirim Broadcast <span class="material-symbols-outlined" id="spinner">sync_arrow_up</span>'
                            );
                            var data = {
                                'tipe': $('#tipe').val(),
                                'message': $('#message_all').val(),
                                'wa': wa
                            }


                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]')
                                        .attr(
                                            'content')
                                }
                            });

                            $.ajax({
                                url: `/whatsapp/broadcast/send`,
                                type: "POST",
                                cache: false,
                                data: data,
                                dataType: "json",

                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: `${data.message}`,
                                            showConfirmButton: true,
                                            // timer: 1500
                                        });
                                        setTimeout(function() {
                                            location
                                                .reload();
                                        }, 2000);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed',
                                            text: `Something wen't wrong, please retry`,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        $('#sendBroadcast').attr(
                                            "disabled", false);
                                        $("#spinner").remove();
                                    }
                                },

                                error: function(err) {
                                    $("#message").html(
                                        "Some Error Occurred!")
                                    $('#sendBroadcast').attr(
                                        "disabled", false);
                                    $("#spinner").remove();
                                }
                            });
                        }
                    });

                });

            }
        });
    } else if (tipe === 'suspend') {
        $('#show_all').hide()
        $('#show_byarea').hide()
        $('#show_byodp').hide()
        $('#show_suspend').show()
        $.ajax({
            url: `/whatsapp/broadcast/getAllUserSuspend`,
            type: "GET",
            cache: false,
            success: function(data) {

                $('#fill_jmlpelanggan_suspend').html(data.countuser);
                let wa = []
                $.each(data.data, function(index, row) {
                    if (row.wa !== null) {
                        wa.push(row.wa)
                    }
                })

                $('#sendBroadcast').click(function(e) {
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        icon: 'warning',
                        text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                        showCancelButton: !0,
                        reverseButtons: !0,
                        confirmButtonText: "Ya, Kirim!",
                        cancelButtonText: "Batal",
                        confirmButtonColor: "#d33",
                        // cancelButtonColor: "#d33",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            let timerInterval;
                            Swal.fire({
                                title: "Mengirim pesan",
                                icon: "info",
                                html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                timer: 9999999,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup()
                                        .querySelector("b");
                                    timerInterval = setInterval(
                                        () => {
                                            timer.textContent =
                                                `${Swal.getTimerLeft()}`;
                                        }, 9999999);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal
                                    .DismissReason.timer) {
                                    console.log("Timeout");
                                }
                            });
                            $('#sendBroadcast').attr("disabled", true);
                            $("#sendBroadcast").html(
                                'Kirim Broadcast <span class="material-symbols-outlined" id="spinner">sync_arrow_up</span>'
                            );
                            var data = {
                                'tipe': $('#tipe').val(),
                                'message': $('#message_suspend').val(),
                                'wa': wa
                            }


                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $(
                                            'meta[name="csrf-token"]')
                                        .attr(
                                            'content')
                                }
                            });

                            $.ajax({
                                url: `/whatsapp/broadcast/send`,
                                type: "POST",
                                cache: false,
                                data: data,
                                dataType: "json",

                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: `${data.message}`,
                                            showConfirmButton: true,
                                            // timer: 1500
                                        });
                                        setTimeout(function() {
                                            location
                                                .reload();
                                        }, 2000);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed',
                                            text: `Something wen't wrong, please retry`,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        $('#sendBroadcast').attr(
                                            "disabled", false);
                                        $("#spinner").remove();
                                    }
                                },

                                error: function(err) {
                                    $("#message").html(
                                        "Some Error Occurred!")
                                    $('#sendBroadcast').attr(
                                        "disabled", false);
                                    $("#spinner").remove();
                                }
                            });
                        }
                    });

                });

            }
        });
    } else if (tipe === 'byarea') {
        $('#show_suspend').hide()
        $('#show_all').hide()
        $('#show_byodp').hide()
        $('#show_byarea').show()
    } else if (tipe === 'byodp') {
        $('#show_byarea').hide()
        $('#show_suspend').hide()
        $('#show_all').hide()
        $('#show_byodp').show()
    } else {
        $('#show_byodp').hide()
        $('#show_all').hide()
        $('#show_suspend').hide()
        $('#show_byarea').hide()
    }
});

$('#kode_area').change(function() {
    let kode_area = $(this).val();
    $.ajax({
        url: `/whatsapp/broadcast/getAllUserArea`,
        type: "GET",
        cache: false,
        data: {
            kode_area: kode_area,
            '_token': '{{ csrf_token() }}'
        },
        success: function(data) {
            $('#fill_area').html($('#kode_area').val());
            $('#fill_jmlpelanggan_area').html(data.countuser);
            let wa = []
            $.each(data.data, function(index, row) {
                if (row.wa !== null) {
                    wa.push(row.wa)
                }
            });

            $('#sendBroadcast').click(function(e) {
                Swal.fire({
                    title: "Apakah anda yakin?",
                    icon: 'warning',
                    text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                    showCancelButton: !0,
                    reverseButtons: !0,
                    confirmButtonText: "Ya, Kirim!",
                    cancelButtonText: "Batal",
                    confirmButtonColor: "#d33",
                    // cancelButtonColor: "#d33",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        let timerInterval;
                        Swal.fire({
                            title: "Mengirim pesan",
                            icon: "info",
                            html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                            timer: 9999999,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                const timer = Swal.getPopup()
                                    .querySelector("b");
                                timerInterval = setInterval(
                                    () => {
                                        timer.textContent =
                                            `${Swal.getTimerLeft()}`;
                                    }, 9999999);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal
                                .DismissReason.timer) {
                                console.log("Timeout");
                            }
                        });
                        $('#sendBroadcast').attr("disabled", true);
                        $("#sendBroadcast").html(
                            'Kirim Broadcast <span class="material-symbols-outlined" id="spinner">sync_arrow_up</span>'
                        );
                        var data = {
                            'tipe': $('#tipe').val(),
                            'message': $('#message_area').val(),
                            'wa': wa
                        }


                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]')
                                    .attr(
                                        'content')
                            }
                        });

                        $.ajax({
                            url: `/whatsapp/broadcast/send`,
                            type: "POST",
                            cache: false,
                            data: data,
                            dataType: "json",

                            success: function(data) {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: `${data.message}`,
                                        showConfirmButton: true,
                                        // timer: 1500
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: `Something wen't wrong, please retry`,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $('#sendBroadcast').attr(
                                        "disabled", false);
                                    $("#spinner").remove();
                                }
                            },

                            error: function(err) {
                                $("#message").html(
                                    "Some Error Occurred!")
                                $('#sendBroadcast').attr(
                                    "disabled", false);
                                $("#spinner").remove();
                            }
                        });
                    }
                });

            });

        }
    });
});

$('#kode_odp').change(function() {
    let kode_odp = $(this).val();
    $.ajax({
        url: `/whatsapp/broadcast/getAllUserOdp`,
        type: "GET",
        cache: false,
        data: {
            kode_odp: kode_odp,
            '_token': '{{ csrf_token() }}'
        },
        success: function(data) {
            $('#fill_odp').html($('#kode_odp').val());
            $('#fill_jmlpelanggan_odp').html(data.countuser);
            let wa = []
            $.each(data.data, function(index, row) {
                if (row.wa !== null) {
                    wa.push(row.wa)
                }
            });

            $('#sendBroadcast').click(function(e) {
                Swal.fire({
                    title: "Apakah anda yakin?",
                    icon: 'warning',
                    text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                    showCancelButton: !0,
                    reverseButtons: !0,
                    confirmButtonText: "Ya, Kirim!",
                    cancelButtonText: "Batal",
                    confirmButtonColor: "#d33",
                    // cancelButtonColor: "#d33",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        let timerInterval;
                        Swal.fire({
                            title: "Mengirim pesan",
                            icon: "info",
                            html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                            timer: 9999999,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                const timer = Swal.getPopup()
                                    .querySelector("b");
                                timerInterval = setInterval(
                                    () => {
                                        timer.textContent =
                                            `${Swal.getTimerLeft()}`;
                                    }, 9999999);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal
                                .DismissReason.timer) {
                                console.log("Timeout");
                            }
                        });
                        $('#sendBroadcast').attr("disabled", true);
                        $("#sendBroadcast").html(
                            'Kirim Broadcast <span class="material-symbols-outlined" id="spinner">sync_arrow_up</span>'
                        );
                        var data = {
                            'tipe': $('#tipe').val(),
                            'message': $('#message_odp').val(),
                            'wa': wa
                        }


                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]')
                                    .attr(
                                        'content')
                            }
                        });

                        $.ajax({
                            url: `/whatsapp/broadcast/send`,
                            type: "POST",
                            cache: false,
                            data: data,
                            dataType: "json",

                            success: function(data) {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: `${data.message}`,
                                        showConfirmButton: true,
                                        // timer: 1500
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: `Something wen't wrong, please retry`,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $('#sendBroadcast').attr(
                                        "disabled", false);
                                    $("#spinner").remove();
                                }
                            },

                            error: function(err) {
                                $("#message").html(
                                    "Some Error Occurred!")
                                $('#sendBroadcast').attr(
                                    "disabled", false);
                                $("#spinner").remove();
                            }
                        });
                    }
                });

            });

        }
    });
});