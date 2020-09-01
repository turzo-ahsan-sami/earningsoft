<?php
                      		/*$purchaseQty = DB::table('inv_purchase_details')->select(DB::raw('SUM(quantity) as totalQty'))->where('productId', $InvProduct->id)->get();
                          foreach($purchaseQty as $purchaseQtys){
                            echo $purchaseQtys->totalQty;
                          }*/
                          $purchaseBrnachIds = DB::table('inv_purchase')->where('branchId', $gnrBranchId)->pluck('id')->all();
                          $supplierNames = DB::table('inv_purchase_details')->select('purchaseId','productId','quantity')->get();
                          $supplierNames = $supplierNames->whereIn('purchaseId', $purchaseBrnachIds)
                                                          ->whereIn('productId', $InvProduct->id)->sum('quantity');
                          
                      		/*$purchaseQty = (int)DB::table('inv_purchase_details')->where('productId', $InvProduct->id)->sum('quantity');*/
                      	?>
                      		{{-- {{$purchaseQty}} --}}
                          {{$supplierName}}