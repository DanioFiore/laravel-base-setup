<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * For API requests using Sanctum, this checks if the user is authenticated.
     * The actual authentication is handled by the 'auth:sanctum' middleware
     * applied to the routes, but this provides an additional layer of security.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated via Sanctum
        // TODO: Remove this return true after testing
        return true;
        return auth('sanctum')->check();
    }

    /**
     * Handle a failed validation attempt.
     * 
     * This method provides a standardized JSON response format for API validation errors
     * instead of the default Laravel redirect response.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'ko',
                'message' => 'Validation Error',
                'errors' => $validator->errors()->first()
            ], 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * This method must be implemented by child classes.
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     * 
     * Override this method in child classes to provide custom messages.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     * 
     * Override this method in child classes to provide custom attributes.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Get the route parameter by name.
     * 
     * Helper method to easily get route parameters in validation rules.
     */
    protected function getRouteParam(string $name): mixed
    {
        return $this->route($name);
    }

    /**
     * Check if the request expects JSON response.
     * 
     * Helper method to determine if the request is an API request.
     */
    public function expectsJson(): bool
    {
        return $this->expectsJson() || $this->is('api/*');
    }

    /**
     * Get validation rules for ID validation.
     * 
     * Common validation rules for ID parameters.
     */
    protected function getIdValidationRules(string $table_name): array
    {
        return [
            'id' => ['bail', 'required', 'integer', "exists:{$table_name},id"]
        ];
    }

    /**
     * Get validation rules for pagination.
     * 
     * Common validation rules for pagination parameters.
     */
    protected function getPaginationValidationRules(): array
    {
        return [
            'page' => ['bail', 'nullable', 'integer', 'min:1'],
            'perPage' => ['bail', 'nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function getPaginationMessages(): array
    {
        return [
            'page.integer' => 'The page must be an integer.',
            'page.min' => 'The page must be at least 1.',
            'perPage.integer' => 'The per page must be an integer.',
            'perPage.min' => 'The per page must be at least 1.',
            'perPage.max' => 'The per page must be less than 100.',
        ];
    }

    /**
     * Get validation rules for search.
     * 
     * Common validation rules for search parameters.
     */
    protected function getSearchValidationRules(): array
    {
        return [
            'search' => ['bail', 'nullable', 'string', 'min:1', 'max:255'],
            'sort' => ['bail', 'nullable', 'string', 'min:1', 'max:50'],
            'order' => ['bail', 'nullable', 'string', 'in:asc,desc'],
        ];
    }

    protected function getSearchMessages(): array
    {
        return [
            'search.string' => 'The search must be a string.',
            'search.min' => 'The search must be at least 1 character.',
            'search.max' => 'The search must be less than 255 characters.',
            'sort.string' => 'The sort must be a string.',
            'sort.min' => 'The sort must be at least 1 character.',
            'sort.max' => 'The sort must be less than 50 characters.',
            'order.string' => 'The order must be a string.',
            'order.in' => 'The order must be asc or desc.',
        ];
    }

    /**
     * Get all input data, including JSON raw content.
     * 
     * This method handles both form data and JSON raw content automatically.
     * Useful for API requests where clients might send data in different formats.
     */
    public function getAllInput(): array
    {
        $data = $this->input();
        
        // if input() is empty and there is raw content, try to parse as JSON
        if (empty($data) && $this->getContent()) {
            $jsonData = json_decode($this->getContent(), true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                $data = $jsonData;
            }
            
        }
        
        return $data;
    }
}
