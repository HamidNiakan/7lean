<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ProductCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\product\CreateProduct;
use App\Http\Requests\product\DeleteProduct;
use App\Http\Requests\product\UpdateProduct;
use App\Http\Resources\v1\ProductResource;
use App\Repository\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use PharIo\Manifest\ManifestDocumentException;

class ProductController extends Controller {
	public function __construct ( protected ProductRepository $productRepository ) { }
	
	public function index () {
		$products = $this->productRepository->getAllProductWithPagination(5);
		$products = ProductResource::collection($products);
		
		return compact('products');
	}
	
	public function store ( CreateProduct $request ) {
		
		$data = $request->validated();
		$product = $this->productRepository->store($data);
		$record = ProductResource::make($product);
		event(new ProductCreated($product));
		return compact('record');
	}
	
	public function update ( UpdateProduct $request ) {
		
		$id = $request->get('id');
		$data = $request->validated();
		try {
			$product = $this->productRepository->update($id , $data);
			$record = ProductResource::make($product);
			
			return compact('record');
		}
		catch ( ModelNotFoundException $exception ) {
			abort(404 , 'Not found');
		}
	}
	
	public function delete ( DeleteProduct $request ) {
		
		$id = $request->get('productId');
		try {
			$this->productRepository->delete($id);
			
			return response()->noContent();
		}
		catch ( ModelNotFoundException $exception ) {
			abort(404 , 'Not found');
		}
	}
}
