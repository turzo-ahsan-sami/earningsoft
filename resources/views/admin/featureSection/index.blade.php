@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.featureSection.create") }}">
            {{ trans('global.add') }} {{ trans('cruds.featureSection.title_singular') }}
        </a>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.featureSection.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-FeatureSection">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.featureSection.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.featureSection.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.featureSection.fields.description') }}
                        </th>
              
                    
                         <th>
                            {{ trans('cruds.featureSection.fields.image') }}
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
                    @foreach($featureSections as $key => $featureSection)
                        <tr data-entry-id="{{ $featureSection->id }}">
                            <td>

                            </td>
                            <td>
                                {{-- {{ $featureSection->id ?? '' }} --}}
                                {{ ++$sl }}
                            </td>
                            <td>
                                {{ $featureSection->name ?? '' }}
                            </td>
                            <td>
                                {{ $featureSection->description ?? '' }}
                            </td>
                          
                         
                            <td><img src="{{ asset("images/featureSection/$featureSection->image") }}" width="60"></td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.featureSection.show', $featureSection->id) }}">
                                    {{ trans('global.view') }}
                                </a>

                                <a class="btn btn-xs btn-info" href="{{ route('admin.featureSection.edit', $featureSection->id) }}">
                                    {{ trans('global.edit') }}
                                </a>

                                <form action="{{ route('admin.featureSection.destroy', $featureSection->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    url: "{{ route('admin.featureSection.mass_destroy') }}",
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
  $('.datatable-FeatureSection:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
