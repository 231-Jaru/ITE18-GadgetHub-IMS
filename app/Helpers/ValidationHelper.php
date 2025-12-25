<?php

namespace App\Helpers;

use Illuminate\Validation\Rule;

class ValidationHelper
{
    /**
     * Get validation rule for GadgetID that excludes soft-deleted gadgets
     * 
     * @param bool $required Whether the field is required
     * @return array
     */
    public static function gadgetIdRule($required = true)
    {
        $rule = Rule::exists('gadgets', 'GadgetID')->whereNull('deleted_at');
        
        return $required ? ['required', $rule] : ['sometimes', $rule];
    }

    /**
     * Get validation rule for SupplierID
     * 
     * @param bool $required Whether the field is required
     * @return array
     */
    public static function supplierIdRule($required = false)
    {
        $rule = 'exists:suppliers,SupplierID';
        
        return $required ? ['required', $rule] : ['nullable', $rule];
    }

    /**
     * Get validation rule for StockID
     * 
     * @param bool $required Whether the field is required
     * @return array
     */
    public static function stockIdRule($required = true)
    {
        $rule = 'exists:stocks,StockID';
        
        return $required ? ['required', $rule] : ['nullable', $rule];
    }

    /**
     * Get validation rule for AdminID
     * 
     * @param bool $required Whether the field is required
     * @return array
     */
    public static function adminIdRule($required = false)
    {
        $rule = 'exists:admins,AdminID';
        
        return $required ? ['required', $rule] : ['nullable', $rule];
    }

    /**
     * Get validation rule for quantity
     * 
     * @param bool $required Whether the field is required
     * @param int $min Minimum value
     * @return array
     */
    public static function quantityRule($required = true, $min = 1)
    {
        $rules = ['integer', "min:{$min}"];
        
        return $required ? array_merge(['required'], $rules) : array_merge(['sometimes'], $rules);
    }

    /**
     * Get validation rule for price/cost
     * 
     * @param bool $required Whether the field is required
     * @return array
     */
    public static function priceRule($required = true)
    {
        $rules = ['numeric', 'min:0'];
        
        return $required ? array_merge(['required'], $rules) : array_merge(['sometimes'], $rules);
    }
}

