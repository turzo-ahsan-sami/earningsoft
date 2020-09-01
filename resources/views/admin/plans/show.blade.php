@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.plan.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.plan.fields.id') }}
                        </th>
                        <td>
                            {{ $plan->id }}
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.name') }}
                        </th>
                        <td>
                            {{ $plan->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.code') }}
                        </th>
                        <td>
                            {{ $plan->code }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.description') }}
                        </th>
                        <td>
                            {{ $plan->description }}
                        </td>
                    </tr>
                      <tr>
                        <th>
                            {{ trans('cruds.plan.fields.price') }}
                        </th>
                        <td>
                            {{ $plan->price }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.signup_fee') }}
                        </th>
                        <td>
                            {{ $plan->signup_fee}}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.plan.fields.currency') }}
                        </th>
                        <td>
                            {{ $plan->currency }}
                        </td>
                    </tr>


                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.renewal_fee') }}
                        </th>
                        <td>
                            {{ $plan->renewal_fee }}
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.active_users_limit') }}
                        </th>
                        <td>
                            {{ $plan->active_users_limit }}
                        </td>
                    </tr>
                      <tr>
                        <th>
                            {{ trans('cruds.plan.fields.active_company_limit') }}
                        </th>
                        <td>
                            {{ $plan->active_company_limit }}
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.active_branch_limit') }}
                        </th>
                        <td>
                            {{ $plan->active_branch_limit }}
                        </td>
                    </tr>

                      <tr>
                        <th>
                            {{ trans('cruds.plan.fields.image') }}
                        </th>
                         <td>
                           <img src="{{ asset("images/plan/$plan->image") }}" style="width:400px;height:auto">
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.features') }}
                        </th>
                        <td>
                             @php
                                 $features = $plan->features;
                                 @endphp
                                 @foreach ($features as $key => $features)
                                     <li style="list-style-type: none; list-style-position: outside; text-indent: 0!important; border-top: 1px solid #d4d7dc !important; color: #393a3d; list-style-position: outside; padding: 8px 0; line-height: 1.3!important;">
                                         {{$features}}
                                     </li>
                                 @endforeach
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.trial_period') }}
                        </th>
                        <td>
                            {{ $plan->trial_period }} {{ $plan->trial_interval }}
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.invoice_period') }}
                        </th>
                        <td>
                            {{ $plan->invoice_period }} {{ $plan->invoice_interval }}
                        </td>
                    </tr>

                     <tr>
                        <th>
                            {{ trans('cruds.plan.fields.sort_order') }}
                        </th>
                        <td>
                            {{ $plan->sort_order }}
                        </td>
                    </tr>

                     <!-- <tr>
                        <th>
                            {{ trans('cruds.plan.fields.active_users_limit') }}
                        </th>
                        <td>
                            {{ $plan->active_users_limit }}
                        </td>
                    </tr> -->

                     <tr>
                        <th>
                           {{ trans('cruds.plan.fields.is_active') }}
                        </th>
                        <td>
                            @if($plan->is_active ==1)         
                            <button type="button" class="btn btn-success btn-xs">Active</button>
                            @else
                          <button type="button" class="btn btn-danger btn-xs">Inactive</button>     
                            @endif
                         
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <nav class="mb-3">
            <div class="nav nav-tabs">

            </div>
        </nav>
        <div class="tab-content">

        </div>
    </div>
</div>
@endsection
