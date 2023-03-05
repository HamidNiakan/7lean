<?php

namespace App\Http\Requests\cart;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this
	 * request.
	 */
	public function authorize (): bool {
		return true;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
	 */
	public function rules (): array {
		return [
			'items' => 'array|present',
			'items.*.productId' => 'required|exists:products,id' ,
			'items.*.quantity' => 'required|integer|gt:0',
		];
	}
}
