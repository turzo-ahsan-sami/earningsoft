<script type="text/javascript">
  $('#loadingModal').hide();
</script>

<style media="screen">
.table > thead > tr > th,
.table > tbody > tr > th,
.table > tfoot > tr > th,
.table > thead > tr > td,
.table > tbody > tr > td,
.table > tfoot > tr > td  {

  line-height:1;
  font-size: 10px !important;
}

.table thead tr th {
  position: relative;
  word-break: normal;
  text-transform: capitalize !important;
}

.left{
  float: left;
}
.right{
  float: right;
}
.center{
  text-align:left;
  margin:0 auto !important;
  display:inline-block
}
.left{
  text-align: left;
  margin-left: 80px !important;
  display: inline-block;
}

th, td {
  padding: 2px !important;
}
</style>

<div class="container-fluid">
 <div class="row" style="color: black;">
   <div class="row" style="padding-bottom: 0px; text-align: center; vertical-align: middle; color: black; font-weight: bold; "> {{-- div for Company --}}
     @php
       $company = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->select('name','address')->first();

       $operationalConfig =DB::table('mfn_cfg')->where('name','operationalPolicy_cfg')->value('config');

       $operationalConfiguration = json_decode($operationalConfig,true);
     @endphp
     <span style="font-size:14px;">{{$company->name}}</span><br/>
     <span style="font-size:11px;">{{$company->address}}</span><br/>
     <span style="font-size:14px;">Budget Information</span><br/>
     <span style="font-size:11px;">Branch Name: {{$BranchName[0]}}</span><br/>
   </div>
   </div>
   <br>

   <div class="row">
     {{-- <div class="table-responsive"> --}}
     <table class="table table-bordered" border="1pt solid ash" style=" text-align: center; font-family: arial; color:black; border-collapse: collapse; font-size: 10px !important;" width="100%">
       <thead style="font-size:11px;">

         <tr>
           <th colspan="1" rowspan="1">
             <div align="center">Name
             </div>
           </th>
           <th colspan="1" rowspan="1">
             <div align="center">Code
             </div>
           </th>
           <th colspan="1" rowspan="1">
             <div align="center">Created Date
             </div>
           </th>
           <th colspan="1" rowspan="1">
             <div align="center">Approved Date
             </div>
           </th>
           <th colspan="1" rowspan="1">
             <div align="center">Amount
             </div>
           </th>
           <th colspan="1" rowspan="1">
             <div align="center">Action
             </div>
           </th>
        </tr>
       </thead>

       <tbody style="font-size: 10px !important;">
         @php
           // dd($BudgetInfos);
         @endphp
         @if (sizeof($BudgetInfos) > 0)
           @foreach ($BudgetInfos as $key => $BudgetInfo)
             <tr>
               @if ($BudgetInfo->budget_category_id != '40000' and $BudgetInfo->budget_category_id != '50000')
                 @php
                   $decodedId = json_decode($BudgetInfo->budget_category_id, true);
                   // dd($decodedId);
                   $name1 = DB::table('acc_account_ledger')
                    ->select('name')
                    ->where('code', $decodedId[0])
                    ->pluck('name')
                    ->toArray();

                  $name2 = DB::table('acc_account_ledger')
                   ->select('name')
                   ->where('code', $decodedId[1])
                   ->pluck('name')
                   ->toArray();
                   // dd($name2[0]);
                 @endphp
                 <td style="text-align: left; padding-left: 5px !important;">{{$name1[0]}} & {{$name2[0]}}</td>
                 <td>{{$decodedId[0]}} & {{$decodedId[1]}}</td>
               @else
                 @php
                 $name = DB::table('acc_account_ledger')
                  ->select('name')
                  ->where('code', $BudgetInfo->budget_category_id)
                  ->pluck('name')
                  ->toArray();

                  // dd($name);
                 @endphp
                 <td style="text-align: left; padding-left: 5px !important;">{{$name[0]}}</td>
                 <td>{{$BudgetInfo->budget_category_id}}</td>
               @endif
               <td>{{$BudgetInfo->createdDate}}</td>
               <td>{{$BudgetInfo->approvedDate}}</td>
               <td></td>
               <td>
                 @if ($BudgetInfo->approvedDate == '0000-00-00')
                   <a href="#" data-toggle="tooltip" title="not approved" class="not-approved-modal" accId=""><i class="fa fa-dot-circle-o" aria-hidden="true" style="color:red;font-size: 1.3em;"></i></a>
                   &nbsp;
                 @else
                   <a href="#" data-toggle="tooltip" title="approved" class="approved-modal" accId=""><i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 1.3em;"></i></a>
                   &nbsp;
                 @endif
                 <a href="#" data-toggle="tooltip" title="View" class="view-modal" accId="">
                     <i class="fa fa-eye" aria-hidden="true"></i>
                 </a>&nbsp;
                 <a href="#" data-toggle="tooltip" title="Edit" class="edit-modal" accId="" >
                   <span class="glyphicon glyphicon-edit"></span>
                 </a>&nbsp;
                 <a href="#" data-toggle="tooltip" title="Delete" class="delete-modal" accId="">
                     <span class="glyphicon glyphicon-trash"></span>
                 </a>
               </td>
             </tr>
           @endforeach
         @else
           <td colspan="6">NO DATA FOUND !</td>
         @endif
       </tbody>
     </table>
   </div>

   <br>
   {{-- {{$endDate}} --}}


   <br>


 </div>
