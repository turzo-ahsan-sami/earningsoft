@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.banners.create") }}">
            {{ trans('global.add') }} {{ trans('cruds.banner.title_singular') }}
        </a>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.banner.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Banner">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.banner.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.banner.fields.banner_text1') }}
                        </th>
                        <th>
                            {{ trans('cruds.banner.fields.banner_text2') }}
                        </th>
              
                        <th>
                            {{ trans('cruds.banner.fields.banner_text3') }}
                        </th>
                        <th>
                            {{ trans('cruds.banner.fields.button_text1') }}
                        </th>
                        <th>
                            {{ trans('cruds.banner.fields.banner_image') }}
                        </th>
                         <th>
                            {{ trans('cruds.banner.fields.mini_image') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                    @endphp
                    @foreach($banners as $key => $banner)
                        <tr data-entry-id="{{ $banner->id }}">
                            <td>

                            </td>
                            <td>
                                {{-- {{ $banner->id ?? '' }} --}}
                                {{ ++$sl }}
                            </td>
                            <td>
                                {{ $banner->banner_text1 ?? '' }}
                            </td>
                            <td>
                                {{ $banner->banner_text2 ?? '' }}
                            </td>
                          
                            <td>
                                {{ $banner->banner_text3 ?? '' }}
                            </td>
                            <td>
                                {{ $banner->button_text1 ?? '' }}
                            </td>
                         
                            <td><img src="{{ asset("images/banner/$banner->banner_image") }}" width="60"></td>
                             <td><img src="{{ asset("images/banner/$banner->mini_image") }}" width="60"></td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.banners.show', $banner->id) }}">
                                    {{ trans('global.view') }}
                                </a>

                                <a class="btn btn-xs btn-info" href="{{ route('admin.banners.edit', $banner->id) }}">
                                    {{ trans('global.edit') }}
                                </a>

                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.banners.mass_destroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'asc' ]],
    pageLength: 100,
  });
  $('.datatable-Banner:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
