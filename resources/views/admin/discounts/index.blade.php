@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.discounts.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.discount.title_singular') }}
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.discount.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Discount">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.title') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.planId') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.discount_type') }}
                            </th>

                            <th>
                                {{ trans('cruds.discount.fields.value') }}
                            </th>

                            <th>
                                {{ trans('cruds.discount.fields.effective_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.discount.fields.end_date') }}
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
                        @foreach($discounts as $key => $discount)
                            <tr data-entry-id="{{ $discount->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ ++$sl }}
                                </td>
                                <td>
                                    {{ $discount->title ?? '' }}
                                </td>
                                <td>
                                    {{ $discount->plan->name }}
                                </td>
                                <td>
                                    {{ $discount->discount_type ?? '' }}
                                </td>

                                <td>
                                    {{ $discount->value ?? '' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($discount->effective_date)->format('d/m/Y')}}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y')}}
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.discounts.show', $discount->id) }}">
                                        {{ trans('global.view') }}
                                    </a>

                                    <a class="btn btn-xs btn-info" href="{{ route('admin.discounts.edit', $discount->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>

                                    <form action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
            url: "{{ route('admin.discounts.mass_destroy') }}",
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
            $('.datatable-Discount:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
            });
        })

    </script>
@endsection
