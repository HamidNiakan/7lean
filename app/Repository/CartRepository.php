<?php

namespace App\Repository;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function PHPUnit\Framework\returnArgument;

class CartRepository {
	public function __construct ( protected ProductRepository $productRepository ) { }
	
	public function store ( User $user , array $data ) {
		if ( $this->doesTheUserHaveShoppingCart($user) ) {
			$cart = $this->getUserShoppingCart($user);
		}
		else {
			$cart = new Cart();
		}
		
		return $this->addProductsToCartItems($cart , $user , $data);
	}
	
	public function deleteCartItem ( int $cartItemId ) {
		$cartItem = $this->findCartItemById($cartItemId);
		$cartItem->delete();
	}
	
	public function deleteAllCart ( User $user ) {
		$record = $this->getUserShoppingCart($user);
		if ( $record ) {
			if ( $record->cartItems->isNotEmpty() ) {
				$record->cartItems()
					   ->delete();
			}
			$record->delete();
		}
	}
	
	private function findCartItemById ( int $cartItemId ) {
		return CartItem::query()
					   ->findOrFail($cartItemId);
	}
	
	private function doesTheUserHaveShoppingCart ( User $user ): bool {
		$exists = $this->query()
					   ->where('user_id' , $user->id)
					   ->exists();
		if ( $exists ) {
			return true;
		}
		
		return false;
	}
	
	public function getUserShoppingCart ( User $user ): null|Cart {
		return $this->query()
					->with('cartItems')
					->where('user_id' , $user->id)
					->first();
	}
	
	private function addProductsToCartItems ( Cart $cart , User $user , array $data ): Cart {
		try {
			$cart->user_id = $user->id;
			$cart->save();
			foreach ( $data[ 'items' ] as $item ) {
				$product = $this->productRepository->findProductById($item[ 'productId' ]);
				if ( $this->ckeckingProductInCartItem($cart->id , $item[ 'productId' ]) ) {
					$this->updateProductCartItem($cart->id , $product);
				}
				else {
					$price = $this->calculateDiscountPriceProduct($product);
					$cart->cartItems()
						 ->create([
									  'product_id' => $product->id ,
									  'quantity' => $item[ 'quantity' ] ,
									  'price' => $price * $item[ 'quantity' ] ,
								  ]);
				}
			}
			
			return $cart;
		}
		catch ( ModelNotFoundException $exception ) {
			abort(404 , 'not found');
		}
	}
	
	private function calculateDiscountPriceProduct ( Product $product ): int {
		if ( $product->discount != 0 ) {
			return $product->price - ( ( $product->discount / 100 ) * $product->price);
		}
		else {
			return $product->price;
		}
	}
	
	private function ckeckingProductInCartItem ( int $cartId , int $productId ): bool {
		$exists = CartItem::query()
						  ->where([
									  'cart_id' => $cartId ,
									  'product_id' => $productId ,
								  ])
						  ->exists();
		if ( $exists ) {
			return true;
		}
		
		return false;
	}
	
	private function updateProductCartItem ( int $cartId , Product $product ): CartItem {
		$record = CartItem::query()
						  ->where([
									  'cart_id' => $cartId ,
									  'product_id' => $product->id ,
								  ])
						  ->first();
		if ( $record ) {
			$price = $this->calculateDiscountPriceProduct($product) ?? $product->price;
			$record->update([
								'product_id' => $product->id ,
								'quantity' => $record->quantity + 1 ,
								'price' => ( $record->quantity + 1 ) * $price ,
							]);
		}
		
		return $record;
	}
	
	private function query () {
		return Cart::query();
	}
}