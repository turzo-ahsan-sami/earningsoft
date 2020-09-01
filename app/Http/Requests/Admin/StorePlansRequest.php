<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlansRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:150',
            // 'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.subscriptions.tables.plans').',slug',
            'code' => 'required|alpha_dash|max:150|unique:'.config('rinvex.subscriptions.tables.plans').',code',
            'description' => 'nullable|string|max:10000',
            'modules' => 'required',
            'currency' => 'required|alpha|size:3',
            // 'plan_type_id' => 'required|integer|max:150',
            'price' => 'required|numeric',
            'signup_fee' => 'nullable|numeric',
            'renewal_fee' => 'nullable|numeric',
            'trial_period' => 'sometimes|integer|max:10000',
            'trial_interval' => 'sometimes|in:day,week,month',
            'invoice_period' => 'sometimes|integer|max:10000',
            'invoice_interval' => 'sometimes|in:month,year',
            'is_active' => 'sometimes|boolean',
            'active_users_limit' => 'sometimes|integer|max:1000',
            'image' => 'nullable|max:3000',
            'features' => 'nullable',
            'grace_period' => 'nullable|integer|max:10000',
            'grace_interval' => 'nullable|in:hour,day,week,month',
            'sort_order' => 'nullable|integer|max:10000000',
            'prorate_day' => 'nullable|integer|max:150',
            'prorate_period' => 'nullable|integer|max:150',
            'prorate_extend_due' => 'nullable|integer|max:150',
            'active_subscribers_limit' => 'nullable|integer|max:10000',
        ];
    }
}
