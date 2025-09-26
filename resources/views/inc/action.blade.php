<!-- <div class="action-div"> -->
<div>
  @isset($data)
    @isset($prepend)
      {!! $prepend !!}
    @endisset

    @isset($edit)
      <a href="javascript:void(0)"  class="edit-icon edit btn btn-light-warning btn-sm" data-id="{{ $data->id }}">
        Edit
      </a>
    @else
      <a href="javascript:void(0)" class="lock-icon badge badge-info">
        <!-- <i data-feather="lock"></i> -->
         Lock
      </a>
    @endisset

    @isset($delete)
      @if (isset($data->system_reserve) ? !$data->system_reserve : true)
        <a href="#confirmationModal{{ $data->id }}" data-bs-toggle="modal" class="delete-svg btn btn-light-danger btn-sm">
          <i class="remove-icon delete-confirmation">Delete</i>
        </a>
        <!-- Delete Confirmation -->
        <div class="modal fade" id="confirmationModal{{ $data->id }}" tabindex="-1" role="dialog"
          aria-labelledby="confirmationModalLabel{{ $data->id }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title f-w-600">Confirm delete</h3>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <h3 class="mb-3"><b>Are you sure want to delete ?</b></h3>
                <p>This Item Will Be Deleted Permanently. You Can Not Undo This Action.</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route($delete, $data->id) }}" method="post">
                  @csrf
                  @method('delete')
                  <button class="btn btn-primary" data-bs-dismiss="modal" type="button">{{ __('Close') }}</button>
                  <button class="btn btn-danger delete spinner-btn" type="submit">{{ __('Delete') }}</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endif

      @isset($status)
        <a class="badge bg-{{ $status['value'] ? 'danger' : 'success' }} toggle-status-btn"
          data-route="{{ route($status['route'], $data->id) }}" href="javascript:void(0)" data-id="{{ $data->id }}"
          {{ $status['confirmation'] ? 'data-confirmation="true"' : '' }}>
          @if ($status['value'])
            <i class="fas fa-user-check"></i>
          @else
            <i class="fas fa-user-slash"></i>
          @endif
        </a>
      @endisset

      @isset($append)
        {!! $append !!}
      @endisset
    @endisset

  @endisset

  @isset($toggle)
    <label class="switch">
      <input data-route="{{ route($route, $toggle->id) }}" data-id="{{ $toggle->id }}"
        class="form-check-input toggle-status" type="checkbox" name="{{ $name }}" value="{{ $value }}"
        {{ $value ? 'checked' : '' }}>
      <span class="switch-state"></span>
    </label>
  @endisset
</div>
