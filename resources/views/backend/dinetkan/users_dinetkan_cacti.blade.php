@extends('backend.layouts.app_new')

@section('title', 'MRTG Management')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>MRTG Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <!-- <a href="">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a> -->
              </li>
            <!-- <li class="breadcrumb-item active">MRTG Management</li> -->
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid user-management-page">

    
    @if($status != 'active')
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="mb-3">
                  <h3>Status Mitra ini belum aktif sehingga tidak bisa di input MRTG</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif  
    @if($status == 'active')
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-body">
              <form id="adminCactiFormxx" method="POST" action="{{ '/dinetkan/users_dinetkan/update_cacti/'.$userdinetkan->dinetkan_user_id }}">
                @csrf
                <!-- <input type="hidden" id="adminCactiMethod" name="_method" value="POST"> -->
                <div class="modal-body">
                  <input type="hidden" id="id" name="id">
                  <input type="hidden" id="dinetkan_user_id" name="dinetkan_user_id" value="{{$userdinetkan->dinetkan_user_id}}">
                  
                  <div class="row">
                    <div class="mb-3 col-sm-12">
                      <h3>Edit Data</h3>
                    </div>
                    <div class="mb-3 col-md-3">
                      <label for="vlan" class="form-label">VLAN</label>
                      <input type="text" class="form-control" id="vlan" name="vlan" value="{{$userdinetkan->vlan}}" required>
                    </div>
                    <!-- <div class="mb-3">
                      <label for="metro" class="form-label">Metro</label>
                      <input type="text" class="form-control" id="metro" name="metro" value="{{$userdinetkan->metro}}" required>
                    </div> -->
                    <div class="mb-3 col-md-3">
                      <label for="metro_id" class="form-label">Metro</label>
                      <select class="form-select" id="metro_id" name="metro_id" required>
                        <option value="">Select Metro</option>
                        @if($metro)
                          @foreach($metro as $row)
                            <option value="{{$row->id}}" <?php if($userdinetkan->metro_id == $row->id){echo 'selected';}?> >{{$row->name}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="mb-3 col-md-3">
                      <label for="vendor" class="form-label">ID/CID/SO/SID Vendor</label>
                      <input type="text" class="form-control" id="vendor" name="vendor" value="{{$userdinetkan->vendor}}" required>
                    </div>
                    <div class="mb-3 col-md-3">
                      <label for="ip_prefix" class="form-label">IP Prefix</label>
                      <input type="text" class="form-control" id="ip_prefix" name="ip_prefix" value="{{$userdinetkan->ip_prefix}}" required>
                    </div>
                    <div class="mb-3">
                      <label for="id_wag" class="form-label">ID Whatsapp group</label>
                      <!-- <input type="text" class="form-control" id="id_wag" name="id_wag" required> -->
                          <div class="form-group mb-3" style="display:grid">
                          <select class="form-control" id="id_wag" name="id_wag" autocomplete="off" data-placeholder="Pilih WAGroup">
                              <div class="row">
                              <option value=""></option>
                              @forelse ($wag as $row)
                                  <option value="{{ $row->group_id }} " {{ $row->group_id == $userdinetkan->group_id ? 'selected' : ''}}>{{ $row->group_name }}</option>
                              @empty
                              @endforelse
                              </div>
                          </select>

                          </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <a type="button" class="btn btn-secondary" href="{{ route('dinetkan.users_dinetkan') }}">Back</a>
                  <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>    
      <div style="margin-top:30px"></div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-body">
              <form id="adminCactiForm" method="POST" action="{{ '/dinetkan/users_dinetkan/update_cacti2/'.$userdinetkan->dinetkan_user_id }}">
                @csrf
                <input type="hidden" id="adminCactiMethod" name="_method" value="POST">
                <div class="modal-body">
                  <input type="hidden" id="id" name="id">
                  <input type="hidden" id="dinetkan_user_id" name="dinetkan_user_id" value="{{$userdinetkan->dinetkan_user_id}}">
                  
                  <div class="row">
                    <div class="mb-3 col-sm-12">
                      <h3>Add Graph</h3>
                    </div>
                    <div class="mb-3 col-md-4">
                      <label for="pop" class="form-label">POP</label>
                      <select class="form-select" id="pop" name="pop" required>
                        <option value="">Select Tree</option>
                        @if($pop)
                          @foreach($pop as $row)
                            <option value="{{$row->id}}">{{$row->name}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="mb-3 col-md-4">
                      <label for="trafic_mrtg" class="form-label">Trafic MRTG</label>
                      <label for="trafic_mrtg_tree" class="form-label">Tree</label>
                      <select class="form-select" id="trafic_mrtg_tree" name="trafic_mrtg_tree" required>
                        <option value="">Select Tree</option>
                        @if($tree)
                          @foreach($tree as $row)
                            <option value="{{$row['value']}}">{{$row['label']}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="mb-3 col-md-4">
                      <label for="trafic_mrtg_tree_node" class="form-label">Tree Node</label>
                      <select class="form-select" id="trafic_mrtg_tree_node" name="trafic_mrtg_tree_node" required>
                        <option value="">Select Node</option>
                      </select>
                    </div>
                    <div class="mb-3 col-md-4">
                      <label for="page_graph" class="form-label">Page Graph</label>
                      <select class="form-select" id="page_graph" name="page_graph" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                      </select>
                    </div>
                    <div class="mb-3 col-md-4">
                      <label for="trafic_mrtg_graph" class="form-label">Graph</label>
                      <select class="form-select" id="trafic_mrtg_graph" name="trafic_mrtg_graph" required>
                        <option value="">Select Graph</option>
                      </select>
                    </div>
                    <div class="mb-3 col-md-4">
                      <label for="graph_name" class="form-label">Graph Name</label>
                      <input type="text" class="form-control" id="graph_name" name="graph_name" required placeholder="graph name">
                      <input type="hidden" id="selected_value" readonly>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <a type="button" class="btn btn-secondary" href="{{ route('dinetkan.users_dinetkan') }}">Back</a>
                  <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
              </form>

              <div class="table-responsive custom-scrollbar" id="row_create">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                  <thead>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                  </thead>
                  @if($userdinetkanGraph)
                    @foreach($userdinetkanGraph as $row)
                      <tr>
                        <td>{{$row->graph_id}}</td>
                        <td>{{$row->graph_name}}</td>
                        <td>
                          <a href="javascript:void(0)" class="delete-icon delete badge badge-danger" data-id="{{ $row->id }}">
                            <i data-feather="trash"></i>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  @endif
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

  </div>
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-label="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Delete Cacti ??</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="deleteForm" method="POST">
          @csrf
          <input type="hidden" id="deleteMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <div class="row">
              <div class="mb-3">
                <input type="text" class="form-control" id="modal_graph_id" readonly>
              </div>
              <div class="mb-3">
                <input type="text" class="form-control" id="modal_graph_name" readonly>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->

@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script> -->
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->

  <script type="text/javascript">
      
    $('#id_wag').select2({
        allowClear: true,
    });
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    $(document).ready(function() {
            
      const modal = $('#deleteModal');
      const form = $('#deleteForm');
      const methodField = $('#deleteMethod');
      const submitBtn = $('#modalSubmitBtn');
      
      // Edit Button Click Handler
      $(document).on('click', '.delete-icon.delete', function() {
        const id = $(this).data('id');
        
        $.ajax({
          url: `/dinetkan/users_dinetkan/single_cacti/${id}`,
          method: 'GET',
          success: function(data) {
            form.attr('action', `/dinetkan/users_dinetkan/delete_cacti/${id}`);
            methodField.val('DELETE');
            submitBtn.text('Delete');

            // Populate the form with data
            $('#id').val(data.id);
            $('#modal_graph_id').val(data.graph_id);
            $('#modal_graph_name').val(data.graph_name);
            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
      });

      
      const modalCacti = $('#adminCactiModal');
      const formCacti = $('#adminCactiForm');
      const methodCactiField = $('#adminCactiMethod');
      
      formCacti.on('submit', function(e) {
        e.preventDefault();

        $.ajax({
          url: formCacti.attr('action'),
          method: formCacti.find('#adminCactiMethod').val() === 'POST' ? 'POST' : 'PUT',
          data: formCacti.serialize(),
          success: function(response) {
            modalCacti.modal('hide');
            // $('.myTable').DataTable().ajax.reload();
            toastr.success(response.message);
            location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }

            toastr.error(message);
          }
        });
      });


    });
  </script>
  <script>  

    $('#trafic_mrtg_tree').on('change', function() {
    $('#trafic_mrtg_tree_node').empty();
    var id = $(this).val();
    if (id) {
        $.ajax({
        url:  baseurl + `/dinetkan/users_dinetkan/get_tree_node_mrtg/`+id,
        type: 'GET',
        dataType: "json",
        success: function(data) {
              if(data){
                let formattedData = data.map((item) => ({
                  id: item.value,
                  text: item.label
                })).sort((a, b) => a.text.localeCompare(b.text));

                // Tambahkan opsi default "Silahkan Pilih"
                formattedData.unshift({
                    id: '',
                    text: 'Silahkan Pilih',
                    disabled: false // Jangan disabled agar bisa dipilih
                });

                $('#trafic_mrtg_tree_node').select2({
                    data: formattedData,
                    allowClear: true,
                    dropdownParent: $("#adminCactiForm .modal-body"),
                });
            }else{
              $('#trafic_mrtg_tree_node').empty();
            }
          }
        });
    } else {
        $('#trafic_mrtg_tree_node').empty();
    }
    });

    $('#trafic_mrtg_tree_node').on('change', function() {
        let page_graph = document.getElementById("page_graph");
        let paging = page_graph.value;
        $('#trafic_mrtg_graph').empty();
        var id = $(this).val();
        if (id) {
            $.ajax({
                url: baseurl + `/dinetkan/users_dinetkan/get_graph_mrtg/` + id + `/` + paging,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data) {
                        let formattedData = data.map((item) => ({
                            id: item.local_graph_id,
                            text: item.Title
                        })).sort((a, b) => a.text.localeCompare(b.text));

                        // Tambahkan opsi default "Silahkan Pilih"
                        formattedData.unshift({
                            id: '',
                            text: 'Silahkan Pilih',
                            disabled: false // Jangan disabled agar bisa dipilih
                        });

                        $('#trafic_mrtg_graph').select2({
                            data: formattedData,
                            allowClear: true,
                            dropdownParent: $("#adminCactiForm .modal-body"),
                        });
                    } else {
                        $('#trafic_mrtg_graph').empty();
                    }
                }
            });
        } else {
            $('#trafic_mrtg_graph').empty();
        }
    });

    $('#page_graph').on('change', function() {
        let trafic_mrtg_tree_node = document.getElementById("trafic_mrtg_tree_node");
        $('#trafic_mrtg_graph').empty();
        var paging = $(this).val();
        var id = trafic_mrtg_tree_node.value;
        if (id) {
            $.ajax({
                url: baseurl + `/dinetkan/users_dinetkan/get_graph_mrtg/` + id + `/` + paging,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data) {
                        let formattedData = data.map((item) => ({
                            id: item.local_graph_id,
                            text: item.Title
                        })).sort((a, b) => a.text.localeCompare(b.text));

                        // Tambahkan opsi default "Silahkan Pilih"
                        formattedData.unshift({
                            id: '',
                            text: 'Silahkan Pilih',
                            disabled: false // Jangan disabled agar bisa dipilih
                        });

                        $('#trafic_mrtg_graph').select2({
                            data: formattedData,
                            allowClear: true,
                            dropdownParent: $("#adminCactiForm .modal-body"),
                        });
                    } else {
                        $('#trafic_mrtg_graph').empty();
                    }
                }
            });
        } else {
            $('#trafic_mrtg_graph').empty();
        }
    });
    
    $('#trafic_mrtg_graph').on('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        document.getElementById("selected_value").value = selectedOption;
      });
  </script>
@endpush
