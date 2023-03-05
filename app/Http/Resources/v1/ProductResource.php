<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray ( Request $request ): array {
		return [
			'id' => $this->id ,
			'title' => $this->title ,
			'price' => $this->price ,
			'discount' => $this->discount,
		];
	}
}