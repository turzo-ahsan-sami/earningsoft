<?php

    namespace App\Http\Controllers\microfin\settings;

    use Illuminate\Http\Request;
    use App\Http\Requests;
    use App\gnr\GnrDivision;
    use Validator;
    use Response;
    use DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Hash;
    use App\Http\Controllers\Controller;
    use App\microfin\settings\MfnPrimaryProduct;


    class MfnPrimaryProductController extends Controller {

        private $TCN;

        public function __construct() {

            $this->TCN = array(
                array('SL No.', 70), 
                array('Name', 0),
                array('Action', 100)
            );
        }

        public function index() {

            $mfnPrimaryProduct = MfnPrimaryProduct::all();
            $TCN = $this->TCN;

            return view('microfin.settings.primaryProduct.viewPrimaryProductList',['mfnPrimaryProduct'=>$mfnPrimaryProduct, 'TCN' => $TCN]);
        }

        public function addPrimaryProductForm() {

             return view('microfin.settings.primaryProduct.addPrimaryProduct');
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: ADD PRIMARY PRODUCT CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function addItem(Request $req) {

            $rules = array(
                'name'      =>  'required|unique:mfn_primary_product,name'
            );

            $attributesNames = array(
                'name'      =>  'Primary Product'
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {
                $mfnPrimaryProduct = new  MfnPrimaryProduct;
                $mfnPrimaryProduct->name =$req->name;
                $mfnPrimaryProduct->createdDate = Carbon::now();
                $mfnPrimaryProduct->save();
            
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'New Primary Product has been saved successfully.'
                );
                
                return response::json($data);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: GET DATA FOR UPDATE MEMBER TYPE CONTROLLER.
        |--------------------------------------------------------------------------
        */

        public function getPrimaryProductInfo(Request $req){

          $mfnPrimaryProduct = MfnPrimaryProduct::find($req->id);

          $data =array(
              'mfnPrimaryProduct'=>$mfnPrimaryProduct

            );

         return response()->json($data);
       }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: UPDATE PRIMARY PRODUCT CONTROLLER.
        |--------------------------------------------------------------------------
        */
        public function updateItem(Request $req) {

            $rules = array(
                'name'       => 'required|unique:mfn_primary_product,name,'.$req->id,
            );

            $attributesNames = array(
                'name'       => 'Primary Product',
            );

            $validator = Validator::make(Input::all(), $rules);
            $validator->setAttributeNames($attributesNames);

            if($validator->fails()) 
                return response::json(array('errors' => $validator->getMessageBag()->toArray()));
            else {

                $mfnPrimaryProduct = MfnPrimaryProduct::find($req->id);
                $mfnPrimaryProduct->name = $req->name;
                $mfnPrimaryProduct->save();
                $data = array(
                    'responseTitle' =>   'Success!',
                    'responseText'  =>   'Primary Product has been updated successfully.'
                );
                
                return response()->json($data);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: DELETE PRIMARY PRODUCT CONTROLLER.
        |--------------------------------------------------------------------------
     */
        public function deleteItem(Request $req) {
            MfnPrimaryProduct::find($req->id)->delete();
            
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Your selected Primary Product deleted successfully.'
            );

            return response()->json($data);
        }
    }