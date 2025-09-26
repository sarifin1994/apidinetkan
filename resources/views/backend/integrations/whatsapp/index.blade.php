@extends('backend.layouts.app')

@section('title', 'WhatsApp Integration')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>WhatsApp Integration</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Integration</li>
            <li class="breadcrumb-item active">WhatsApp</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-header-actions">
            <div class="card-header">
              <div class="h5 col-auto">STATUS WHATSAPP</div>
              <input type="hidden" id="wablas_id">
              <input type="hidden" id="token">
              <input type="hidden" id="sender_logout">
            </div>
            <div class="row me-3 ms-3">
              <div class="col-auto mt-3">
                <button id="btn-scan" class="btn btn-sm btn-primary text-light mb-2 me-2">
                  <i class="fas fa-mobile-screen me-1"></i>GANTI NOMOR
                </button>
                @if ($result['data']['body'] !== '081222339257')
                  <button id="btn-rescan" class="btn btn-sm btn-success text-light mb-2 me-2">
                    <i class="fas fa-qrcode me-1"></i>SCAN DEVICE
                  </button>
                  <button id="btn-logout" class="btn btn-sm btn-danger text-light mb-2 me-2">
                    <i class="fas fa-power-off me-1"></i>DISCONNECT
                  </button>
                @endif
              </div>
            </div>

            <div class="card-body table-responsive custom-scrollbar" style="padding-top:0px">
              <table id="tableWA" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th>NOMOR PENGIRIM</th>
                    <th>KUOTA</th>
                    <th>TERKIRIM</th>
                    <th>STATUS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?= $result['data']['body'] ?></td>
                    <td>Unlimited</td>
                    <td><?= $result['data']['message_sent'] ?></td>
                    @if ($result['data']['status'] === 'Connected')
                      <td><span class="btn btn-sm btn-success">CONNECTED</span></td>
                    @else
                      <td><span class="btn btn-sm btn-danger">DISCONNECTED</span></td>
                    @endif
                  </tr>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="row mb-5 px-5">
              <ol>
                <li>Klik GANTI NOMOR untuk mengganti NOMOR PENGIRIM</li>
                <li>Klik SCAN DEVICE untuk mendapatkan QR Code lalu scan di aplikasi whatsapp</li>
                <li>Setelah scan berhasil, klik REFRESH PAGE dan pastikan kolom STATUS sudah berubah menjadi
                  <span class="text-success">
                    CONNECTED</span>
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-header">
            <div class="h5 col-auto">TEMPLATE MESSAGE</div>
          </div>
          <div class="card-body">
            <button class="btn btn-primary m-2" id="account_active1" data-id="{{ auth()->user()->id_group }}">Pelanggan
              Aktif</button>
            <button class="btn btn-secondary m-2" id="invoice_terbit1" data-id="{{ auth()->user()->id_group }}">Invoice
              Terbit</button>
            <button class="btn btn-warning m-2" id="invoice_reminder1" data-id="{{ auth()->user()->id_group }}">Invoice
              Reminder</button>
            <button class="btn btn-danger m-2" id="invoice_overdue1" data-id="{{ auth()->user()->id_group }}">Invoice
              Overdue</button>
            <button class="btn btn-success m-2" id="payment_paid1" data-id="{{ auth()->user()->id_group }}">Payment
              Paid</button>
            <button class="btn btn-info m-2" id="payment_cancel1" data-id="{{ auth()->user()->id_group }}">Payment
              Cancel</button>
            <hr>
            <div class="row mt-3">
              <span>Silakan sesuaikan template message whatsapp sesuka hati dengan menggunakan
                parameter yang tersedia. Jika butuh bantuan jangan sungkan untuk menghubungi kami melalui
                whatsapp</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-header-actions h-100">
          <div class="card-header">
            <div class="h5 col-auto">RIWAYAT MESSAGE</div>
          </div>
          <div class="row me-3 ms-3">
            <div class="col-auto mt-3">
              <button class="btn btn-primary text-light mb-2 me-2" type="button" data-bs-toggle="modal"
                data-bs-target="#broadcast">
                <i class="fas fa-bullhorn me-2"></i>KIRIM BROADCAST&nbsp
              </button>
              <button class="btn btn-warning text-light mb-2 me-2" type="button" id="resend" disabled>
                <i class="fas fa-file-import me-2"></i>KIRIM ULANG&nbsp
                <span class="row-count badge bg-dark text-light fs-1"></span>
              </button>
              <button class="btn btn-danger text-light mb-2 me-2" id="delete" type="button" data-bs-toggle="modal"
                data-bs-target="#" disabld>
                <i class="fas fa-trash-alt me-2"></i>HAPUS&nbsp
                <span class="row-count badge bg-dark text-light fs-1"></span>
              </button>
            </div>
          </div>
          <div class="card-body table-responsive custom-scrollbar">
            <table id="myTable" class="table-hover display nowrap table" width="100%">
              <thead>
                <tr>
                  <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                  <th style="text-align:left!important">NO</th>
                  <th>TANGAL</th>
                  <th>JAM</th>
                  <th>PENERIMA</th>
                  <th>SUBJECT</th>
                  <th>MESSAGE</th>
                  <th>STATUS</th>
                  <th>ID</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    @include('integrations.whatsapp.modal.show_account_active')
    @include('integrations.whatsapp.modal.show_invoice_terbit')
    @include('integrations.whatsapp.modal.show_invoice_reminder')
    @include('integrations.whatsapp.modal.show_invoice_overdue')
    @include('integrations.whatsapp.modal.show_payment_paid')
    @include('integrations.whatsapp.modal.show_payment_cancel')
    @include('integrations.whatsapp.modal.broadcast')
    @include('integrations.whatsapp.modal.scan')
    @include('integrations.whatsapp.modal.rescan')
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

  <script type="text/javascript">
    let table2 = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      order: [
        [2, 'desc']
      ],
      ajax: '{{ url()->current() }}',
      columns: [{
          data: 'checkbox',
          'sortable': false,
          name: 'checkbox',
          // render: function(data, type, row, meta) {
          //     return '<input type="checkbox" class="form-check-input row-cb" value="' + row.id +
          //         '">';
          // },
        },
        {
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'created_at',
          name: 'created_at',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        {
          data: 'created_at',
          name: 'created_at',
          render: function(data, type, row, meta) {
            return moment(data).local().format('HH:mm');
          },
        },
        {
          data: 'phone',
          name: 'phone'
        },

        {
          data: 'subject',
          name: 'subject'
        },
        {
  data: 'message',
  name: 'message',
  render: function(data, type, row) {
    // If 'data' is null or undefined, just return empty string or handle as needed
    if (!data) {
      return '';
    }
    // Truncate the message to 30 characters
    let truncated = data.substr(0, 30);
    
    // If the message is 30 chars or less, just return as-is
    if (data.length <= 30) {
      return data;
    }
    
    // If more than 30 chars, return truncated + "Show more" button
    return `
      <span class="message-preview">
        ${truncated}...
        <br><button 
          class="badge badge-primary show-more" 
          data-fulltext="${escapeHtml(data)}" 
          data-truncated="${escapeHtml(truncated)}"
        >
          Show more
        </button>
      </span>
    `;
  }
},
        {
          data: 'status',
          name: 'status',
          render: function(data, type, row) {
            if (data === 'success') {
              return "<span class='btn btn-sm btn-success'>success</span>";
            } else if (data === 'failed') {
              return "<span class='btn btn-sm btn-danger'>failed</span>";
            } else if (data === 'pending') {
              return "<span class='btn btn-sm btn-warning'>pending</span>";
            } else {
              return data;
            }
          }
        },
        {
          data: 'id_message',
          name: 'id_message'
        }
      ]
    });

    // Escape HTML helper to safely place text in data attributes
function escapeHtml(text) {
  if (!text) return '';
  return text
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

// Delegate the click event to the table for dynamic content
$(document).on('click', '.show-more', function(e) {
  e.preventDefault();
  const $btn = $(this);
  
  // Get the truncated and full text from data attributes
  const fullText = $btn.data('fulltext');
  const truncated = $btn.data('truncated');
  
  // Check the current button text
  if ($btn.text().trim() === 'Show more') {
    // Switch to full text and change button to 'Show less'
    $btn
      .closest('.message-preview')
      .html(`
        ${fullText}
        <button 
          class="badge badge-primary show-more" 
          data-fulltext="${escapeHtml(fullText)}" 
          data-truncated="${escapeHtml(truncated)}"
        >
          Show less
        </button>
      `);
  } else {
    // Switch back to truncated text and change button to 'Show more'
    $btn
      .closest('.message-preview')
      .html(`
        ${truncated}...
        <button 
          class="badge badge-primary show-more" 
          data-fulltext="${escapeHtml(fullText)}" 
          data-truncated="${escapeHtml(truncated)}"
        >
          Show more
        </button>
      `);
  }
});

    // let table = $('#tableWA').DataTable({
    //     paginate: false,
    //     searching: false,
    //     info: false,
    // });
    var id_selected = [];
    table2.on('preXhr.dt', function(e, settings, data) {
      data.idsel = id_selected;
    });
    $('#head-cb').on('click', function(e) {
      if ($(this).is(':checked', true)) {
        $(".row-cb").prop('checked', true);
        $(".row-count").html($('.row-cb:checked').length);
        $('#delete').prop('disabled', false);
        $('#resend').prop('disabled', false);
      } else {
        $(".row-cb").prop('checked', false);
        $(".row-count").html('');
        $('#delete').prop('disabled', true);
        $('#resend').prop('disabled', true);

      }
    });

    $('#myTable').on('click', '.row-cb', function() {
      if ($('.row-cb:checked').length == $('.row-cb').length) {
        $('#head-cb').prop('checked', true);
        $(".row-count").html($('.row-cb:checked').length);
        $('#delete').prop('disabled', false);
        $('#resend').prop('disabled', false);
      } else if ($('.row-cb:checked').length == 0) {
        $('#head-cb').prop('checked', false);
        $(".row-count").html('');
        $('#delete').prop('disabled', true);
        $('#resend').prop('disabled', true);
      } else {
        $('#head-cb').prop('checked', false);
        $(".row-count").html($('.row-cb:checked').length);
        $('#delete').prop('disabled', false);
        $('#resend').prop('disabled', false);
      }
    });

    $('#delete').on('click', function() {
      Swal.fire({
        title: "Apakah anda yakin?",
        icon: 'warning',
        text: "Data yang sudah dihapus tidak dapat dikembalikan",
        showCancelButton: !0,
        reverseButtons: !0,
        confirmButtonText: "Yes, delete!",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#d33",
        // cancelButtonColor: "#d33",
      }).then(function(result) {
        if (result.isConfirmed) {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $(
                  'meta[name="csrf-token"]'
                )
                .attr('content')
            }
          });

          let checked = $('#myTable tbody .row-cb:checked')
          let ids = []
          $.each(checked, function(index, elm) {
            ids.push(elm.value)
          })
          $.ajax({
            url: baseUrl + `/delete`,
            type: "POST",
            cache: false,
            data: {
              _method: "DELETE",
              ids: ids
            },
            dataType: "json",

            // tampilkan pesan Success
            success: function(data) {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
              });
              setTimeout(
                function() {
                  table2.ajax.reload()
                });
              $('#head-cb').prop('checked', false)
              $(".row-count").html('');
            },

            error: function(err) {
              $("#message").html(
                "Some Error Occurred!"
              )
            }

          });
        }
      });
    });

    $('#btn-scan').on('click', function() {
      $(document).ready(function() {
        $.ajax({
          url: baseUrl + '/server',
          type: "GET",
          cache: false,
          success: function(response) {
            $('#wablas_id').val(response.data[0].id);
            $('#sender').val(response.data[0].sender),
              $('#token').val(response.data[0].token);
          }
        });
      });
      $('#scan').modal('show');
    });

    $('#resend').on('click', function() {
      Swal.fire({
        title: "Apakah anda yakin?",
        icon: 'warning',
        text: "Pesan yang berstatus pending akan dikirim ulang",
        showCancelButton: !0,
        reverseButtons: !0,
        confirmButtonText: "Ya, kirim ulang!",
        cancelButtonText: "Batal",
        confirmButtonColor: "#d33",
        // cancelButtonColor: "#d33",
      }).then(function(result) {
        if (result.isConfirmed) {
          let timerInterval;
          Swal.fire({
            title: "Mengirim ulang",
            icon: "info",
            html: "Mengirim ulang pesan pending. Harap tunggu...",
            timer: 1000000000000000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading();
              const timer = Swal.getPopup().querySelector("b");
              timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
              }, 1000000000000000);
            },
            willClose: () => {
              clearInterval(timerInterval);
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log("Timeout");
            }
          });

          let checked = $('#myTable tbody .row-cb:checked')
          let ids = []
          $.each(checked, function(index, elm) {
            ids.push(elm.value)
          })
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $(
                  'meta[name="csrf-token"]'
                )
                .attr('content')
            }
          });

          $.ajax({
            url: baseUrl + `/message/resend`,
            type: "POST",
            cache: false,
            data: {
              ids: ids
            },
            dataType: "json",

            // tampilkan pesan Success
            success: function(data) {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
              });
              setTimeout(
                function() {
                  table2.ajax.reload();
                }, 1500);
              $('#head-cb').prop('checked', false)
              $(".row-count").html('');
            },

            error: function(err) {
              $("#message").html(
                "Some Error Occurred!"
              )
            }

          });
        }
      });
    });

    $('#update').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      $('#update').attr("disabled", true);
      $("#update").html(
        'SIMPAN&nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>'
      );
      let whatsapp = $('#wablas_id').val();

      var data = {
        'sender': $('#sender').val(),
        'token': $('#token').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/${whatsapp}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
              $("#spinner").remove();
              $('#update').attr("disabled", false);
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
              $("#spinner").remove();
              $('#update').attr("disabled", false);
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });

    $('#btn-rescan').on('click', function() {
      $(document).ready(function() {
        $.ajax({
          url: baseUrl + '/server',
          type: "GET",
          cache: false,
          success: function(response) {
            $('#wablas_id').val(response.data[0].id);
            $('#token').val(response.data[0].token);
            $('#sender_rescan').val(response.data[0].sender);
          }
        });
      });
      $('#rescan').modal('show');
    });

    $('#action-rescan').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      $('#action-rescan').attr("disabled", true);
      $("#action-rescan").html(
        '<i class="fas fa-qrcode me-1"></i>GENERATE QR&nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>'
      );
      let whatsapp = $('#wablas_id').val();

      var data = {
        'sender': $('#sender_rescan').val(),
        'token': $('#token').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/device/scan/`,
        type: "GET",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success === true) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              if (data.data) {
                var imgURL = data.data;
                var $img = $("<img />");
                $img.attr("src", imgURL);
                $("#show_qr").html($img);
              }
              $("#spinner").remove();
              $('#action-rescan').attr("disabled", false);
            });
          } else if (data.success === false) {
            Swal.fire({
              icon: 'error',
              title: 'Sudah Terhubung',
              text: `${data.message}`,
              showConfirmButton: true,
              // timer: 1500
            });
            $("#spinner").remove();
            $('#action-rescan').attr("disabled", false);
          } else {
            $("#spinner").remove();
            $('#action-rescan').attr("disabled", false);
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
          $("#spinner").remove();
          $('#action-rescan').attr("disabled", false);
        }

      });

    });

    $('#btn-logout').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }

      $(document).ready(function() {
        $.ajax({
          url: baseUrl + '/server',
          type: "GET",
          cache: false,
          success: function(response) {
            $('#wablas_id').val(response.data[0].id);
            $('#token').val(response.data[0].token);
            $('#sender_logout').val(response.data[0].sender);
            Swal.fire({
              title: "Disconnet Device?",
              icon: 'warning',
              text: "Apakah anda yaking ingin men-disconnect device ini?",
              showCancelButton: !0,
              reverseButtons: !0,
              confirmButtonText: "Ya, disconnect!",
              cancelButtonText: "Cancel",
              confirmButtonColor: "#d33",
              // cancelButtonColor: "#d33",
            }).then(function(result) {
              if (result.isConfirmed) {
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $(
                        'meta[name="csrf-token"]'
                      )
                      .attr('content')
                  }
                });

                var data = {
                  'sender': $('#sender_logout').val(),
                  'token': $('#token').val(),
                }

                $.ajax({
                  url: baseUrl + `/device/logout`,
                  type: "POST",
                  cache: false,
                  data: data,
                  dataType: "json",

                  success: function(data) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: `${data.message}`,
                      showConfirmButton: false,
                      timer: 1500
                    });
                    setTimeout(
                      function() {
                        location.reload()
                      }, 1500);
                  },

                  error: function(err) {
                    $("#message").html(
                      "Some Error Occurred!")
                    $("#spinner").remove();
                    $('#action-rescan').attr("disabled",
                      false);
                  }

                });


              }
            });
          }
        });
      });



    });

    $('#account_active1').on('click', function() {
      let id = $(this).data('id');
      $.ajax({
        url: baseUrl + `/template/${id}`,
        type: "GET",
        data: {
          id: id,
        },
        success: function(data) {
          $("#id").val(data[0].id);
          var account_active = data[0].account_active.replace(/<br>/gi, '\n');
          $('#account_active').val(account_active);


        }
      });
      $('#show_account_active').modal('show');
    });

    $('#invoice_terbit1').on('click', function() {
      let id = $(this).data('id');
      $.ajax({
        url: baseUrl + `/template/${id}`,
        type: "GET",
        data: {
          id: id,
        },
        success: function(data) {
          $("#id").val(data[0].id);
          var invoice_terbit = data[0].invoice_terbit.replace(/<br>/gi, '\n');
          $('#invoice_terbit').val(invoice_terbit);


        }
      });
      $('#show_invoice_terbit').modal('show');
    });

    $('#invoice_reminder1').on('click', function() {
      let id = $(this).data('id');
      $.ajax({
        url: baseUrl + `/template/${id}`,
        type: "GET",
        data: {
          id: id,
        },
        success: function(data) {
          $("#id").val(data[0].id);
          var invoice_reminder = data[0].invoice_reminder.replace(/<br>/gi, '\n');
          $('#invoice_reminder').val(invoice_reminder);
        }
      });
      $('#show_invoice_reminder').modal('show');
    });
    $('#invoice_overdue1').on('click', function() {
      let id = $(this).data('id');
      $.ajax({
        url: baseUrl + `/template/${id}`,
        type: "GET",
        data: {
          id: id,
        },
        success: function(data) {
          $("#id").val(data[0].id);
          var invoice_overdue = data[0].invoice_overdue.replace(/<br>/gi, '\n');
          $('#invoice_overdue').val(invoice_overdue);


        }
      });
      $('#show_invoice_overdue').modal('show');
    });
    $('#payment_paid1').on('click', function() {
      let id = $(this).data('id');
      $.ajax({
        url: baseUrl + `/template/${id}`,
        type: "GET",
        data: {
          id: id,
        },
        success: function(data) {
          $("#id").val(data[0].id);
          var payment_paid = data[0].payment_paid.replace(/<br>/gi, '\n');
          $('#payment_paid').val(payment_paid);


        }
      });
      $('#show_payment_paid').modal('show');
    });
    $('#payment_cancel1').on('click', function() {
      let id = $(this).data('id');
      $.ajax({
        url: baseUrl + `/template/${id}`,
        type: "GET",
        data: {
          id: id,
        },
        success: function(data) {
          $("#id").val(data[0].id);
          var payment_cancel = data[0].payment_cancel.replace(/<br>/gi, '\n');
          $('#payment_cancel').val(payment_cancel);


        }
      });
      $('#show_payment_cancel').modal('show');
    });
    $('#updateAccountActive').click(function() {
      let id = $('#id').val();

      // collect data by id
      var data = {
        'account_active': $('#account_active').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/template/active/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });
    $('#updateInvoiceTerbit').click(function() {
      let id = $('#id').val();

      // collect data by id
      var data = {
        'invoice_terbit': $('#invoice_terbit').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/template/terbit/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });
    $('#updateInvoiceReminder').click(function() {
      let id = $('#id').val();

      // collect data by id
      var data = {
        'invoice_reminder': $('#invoice_reminder').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/template/reminder/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });
    $('#updateInvoiceOverdue').click(function() {
      let id = $('#id').val();

      // collect data by id
      var data = {
        'invoice_overdue': $('#invoice_overdue').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/template/overdue/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });
    $('#updatePaymentPaid').click(function() {
      let id = $('#id').val();

      // collect data by id
      var data = {
        'payment_paid': $('#payment_paid').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/template/paid/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });
    $('#updatePaymentCancel').click(function() {
      let id = $('#id').val();

      // collect data by id
      var data = {
        'payment_cancel': $('#payment_cancel').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/template/cancel/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });

    $('#myTable').on('click', '#sendMessage', function() {
      let message_id = $(this).data('id')
      let wa = $(this).data('wa');
      let message = $(this).data('message');


      Swal.fire({
        title: "Apakah anda yakin?",
        icon: 'warning',
        text: "Pesan ini akan dikirim ulang",
        showCancelButton: !0,
        reverseButtons: !0,
        confirmButtonText: "Ya, kirim ulang!",
        cancelButtonText: "Batal",
        confirmButtonColor: "#d33",
        // cancelButtonColor: "#d33",
      }).then(function(result) {
        if (result.isConfirmed) {
          let timerInterval;
          Swal.fire({
            title: "Mengirim ulang",
            icon: "info",
            html: "Mengirim ulang pesan pending. Harap tunggu...",
            timer: 1000000000000000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading();
              const timer = Swal.getPopup().querySelector("b");
              timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
              }, 1000000000000000);
            },
            willClose: () => {
              clearInterval(timerInterval);
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log("Timeout");
            }
          });
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $(
                  'meta[name="csrf-token"]'
                )
                .attr('content')
            }
          });

          // collect data by id
          var data = {
            'message_id': message_id,
            'wa': wa,
            'message': message
          };

          $.ajax({
            url: baseUrl + `/single/resend`,
            type: "POST",
            data: data,
            cache: false,
            dataType: "json",

            // tampilkan pesan Success
            success: function(data) {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
              });
              setTimeout(
                function() {
                  table.ajax.reload();
                }, 1500);
            },

            error: function(err) {
              $("#message").html(
                "Some Error Occurred!"
              )
            }

          });
        }
      });

    });

    $('#tipe').change(function() {
      let tipe = $(this).val();
      if (tipe === 'all') {
        $('#show_all').show()
        $('#show_byarea').hide()
        $.ajax({
          url: baseUrl + `/user/getUserAll`,
          type: "GET",
          cache: false,
          success: function(data) {
            $('#fill_jmlarea').html(data.countarea);
            $('#fill_jmlpelanggan_all').html(data.countuser);
            let wa = []
            $.each(data.data, function(index, row) {
              if (row.wa !== null) {
                wa.push(row.wa)
              }
            })

            $('#sendBroadcast').click(function(e) {
              e.preventDefault();
              $('#sendBroadcast').attr("disabled", true);
              $("#sendBroadcast").html(
                'Kirim Broadcast&nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>'
              );
              var data = {
                'tipe': $('#tipe').val(),
                'subject_all': $('#subject_all').val(),
                'message_all': $('#message_all').val(),
                'wa': wa
              }


              $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content')
                }
              });

              $.ajax({
                url: baseUrl + `/send/broadcast`,
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
                      table2.ajax.reload();
                      $('#sendBroadcast').attr(
                        "disabled", false);
                      $("#spinner").remove();
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Failed',
                      text: `Something wen't wrong, please retry`,
                      showConfirmButton: false,
                      timer: 1500
                    });
                    $('#sendBroadcast').attr("disabled", false);
                    $("#spinner").remove();
                  }
                },

                error: function(err) {
                  $("#message").html("Some Error Occurred!")
                  $('#sendBroadcast').attr("disabled", false);
                  $("#spinner").remove();
                }
              });
            });

          }
        });
      } else if (tipe === 'byarea') {
        $('#show_all').hide()
        $('#show_byarea').show()
      } else {
        $('#show_all').hide()
        $('#show_byarea').hide()
      }
    });

    $('#kode_area').change(function() {
      let kode_area = $(this).val();
      $.ajax({
        url: baseUrl + `/user/getUser`,
        type: "GET",
        cache: false,
        data: {
          kode_area: kode_area,
          '_token': '{{ csrf_token() }}'
        },
        success: function(data) {

          $('#fill_area').html($('#kode_area').val());
          $('#fill_jmlpelanggan_area').html(data.count);
          let wa = []
          $.each(data.data, function(index, row) {
            if (row.wa !== null) {
              wa.push(row.wa)
            }
          });

          $('#sendBroadcast').click(function(e) {
            e.preventDefault();
            $('#sendBroadcast').attr("disabled", true);
            $("#sendBroadcast").html(
              'Kirim Broadcast&nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>'
            );
            var data = {
              'tipe': $('#tipe').val(),
              'subject_area': $('#subject_area').val(),
              'message_area': $('#message_area').val(),
              'wa': wa
            }


            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                  'content')
              }
            });

            $.ajax({
              url: baseUrl + `/send/broadcast`,
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
                    table2.ajax.reload();
                    $('#sendBroadcast').attr("disabled",
                      false);
                    $("#spinner").remove();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: `Something wen't wrong, please retry`,
                    showConfirmButton: false,
                    timer: 1500
                  });
                  $('#sendBroadcast').attr("disabled",
                    false);
                  $("#spinner").remove();
                }
              },

              error: function(err) {
                $("#message").html("Some Error Occurred!")
                $('#sendBroadcast').attr("disabled", false);
                $("#spinner").remove();
              }
            });
          });
        }
      });
    });

    $("#broadcast").on("hidden.bs.modal", function() {
      $('#sendBroadcast').attr("disabled", false);
      $("#spinner").remove();
    });

    $('#kode_area').select2({
      dropdownParent: $("#broadcast"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });
  </script>
@endsection
