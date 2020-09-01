@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.plans.create") }}">
            {{ trans('global.add') }} {{ trans('cruds.plan.title_singular') }}
        </a>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.plan.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Plan">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.code') }}
                        </th>
                          <th>
                            {{ trans('cruds.plan.fields.price') }}
                        </th>
                        <th>
                            {{ trans('cruds.plan.fields.signup_fee') }}
                        </th>
                         <th>
                            {{ trans('cruds.plan.fields.renewal_fee') }}
                        </th>

                          <th>
                            {{ trans('cruds.plan.fields.trial_period') }}
                        </th>

                        

                          <th>
                            {{ trans('cruds.plan.fields.invoice_period') }}
                        </th>
                         <th>
                            {{ trans('cruds.plan.fields.active_users_limit') }}
                        </th>
                      <!--    <th>
                            {{ trans('cruds.plan.fields.is_active') }}
                        </th> -->
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                    @endphp
                    @foreach($plans as $key => $plan)
                        <tr data-entry-id="{{ $plan->id }}">
                            <td>

                            </td>
                            <td>
                                {{-- {{ $plan->id ?? '' }} --}}
                                {{ ++$sl }}
                            </td>
                            <td>
                                {{ $plan->name ?? '' }}
                            </td>
                            <td>
                                {{ $plan->code ?? '' }}
                            </td>
                            <td>
                                {{ $plan->price ?? '' }}
                            </td>
                            <td>
                                {{ $plan->signup_fee ?? '' }}
                            </td>
                             <td>
                                {{ $plan->renewal_fee ?? '' }}
                            </td>
                             <td>
                               {{ $plan->trial_period ?? '' }} {{ $plan->trial_interval ?? '' }}
                            </td>
                            </td>
                         

                            <td>
                                {{ $plan->invoice_period ?? '' }} {{ $plan->invoice_interval ?? '' }}
                            </td>
                               <td>
                                {{ $plan->active_users_limit ?? '' }}
                            </td>
                           <!--  <td>
                                @if($plan->is_active ==1)         
                            <button type="button" class="btn btn-success btn-xs">Active</button>
                            @else
                          <button type="button" class="btn btn-danger btn-xs">Inactive</button>     
                            @endif
                            </td> -->
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.plans.show', $plan->id) }}">
                                    {{ trans('global.view') }}
                                </a>

                                <a class="btn btn-xs btn-info" href="{{ route('admin.plans.edit', $plan->id) }}">
                                    {{ trans('global.edit') }}
                                </a>

                                <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    url: "{{ route('admin.plans.mass_destroy') }}",
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
  $('.datatable-Plan:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
