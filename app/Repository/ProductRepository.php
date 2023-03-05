<?php

namespace App\Repository;

use App\Models\Product;

class ProductRepository {
	public function getAllProductWithPagination ( int $page ) {
		return $this->query()
					->latest()
					->paginate($page);
	}
	
	/**
	 * @param array $data
	 * @return \App\Models\Product
	 */
	public function store ( array $data ): Product {
		return $this->query()
					->create([
								 'title' => $data[ 'title' ] ,
								 'price' => $data[ 'price' ] ,
								 'discount' => $data[ 'discount' ] ?? 0 ,
							 ]);
	}
	
	/**
	 * @param int $productId
	 * @return \App\Models\Product
	 */
	public function findProductById ( int $productId ): Product {
		return $this->query()
					->findOrFail($productId);
	}
	
	/**
	 * @param int   $productId
	 * @param array $data
	 * @return \App\Models\Product
	 */
	public function update ( int $productId , array $data ): Product {
		$product = $this->findProductById($productId);
		$product->fill($data);
		$product->save();
		
		return $product;
	}
	
	/**
	 * @param int $productId
	 * @return void
	 */
	public function delete ( int $productId ) {
		$product = $this->findProductById($productId);
		$product->delete();
	}
	
	/**
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	private function query () {
		return Product::query();
	}
}