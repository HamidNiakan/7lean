<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\CalculateTotalPriceShoppingCartEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\cart\CreateRequest;
use App\Http\Requests\cart\DeleteRequest;
use App\Http\Resources\v1\CartResource;
use App\Repository\CartRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller {
	public function __construct ( protected CartRepository $cartRepository ) { }
	
	public function index () {
		$user = Auth::loginUsingId(9);
		$cart = $this->cartRepository->getUserShoppingCart($user);
		$cart = CartResource::make($cart);
		
		return compact('cart');
	}
	
	public function addProductsToCartItems ( CreateRequest $request ) {
		
		$user = Auth::loginUsingId(9);
		$data = $request->validated();
		$this->cartRepository->store($user , $data);
		event(new CalculateTotalPriceShoppingCartEvent($user));
		$record = $this->cartRepository->getUserShoppingCart($user);
		$cart = CartResource::make($record);
		
		return compact('cart');
	}
	
	public function deleteCartItem ( DeleteRequest $request ) {
		try {
			$this->cartRepository->deleteCartItem($request->get('cartItemId'));
			$user = Auth::loginUsingId(9);
			event(new CalculateTotalPriceShoppingCartEvent($user));
			$record = $this->cartRepository->getUserShoppingCart($user);
			$cart = CartResource::make($record);
			return compact('cart');
		}
		catch ( ModelNotFoundException $exception ) {
			abort(404 , 'not found');
		}
	}
	
	public function deleteAllCart () {
		$user = Auth::loginUsingId(9);
		$this->cartRepository->deleteAllCart($user);
		
		return response()->noContent();
	}
}
