<?php

namespace App\Listeners;

use App\Repository\CartRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculateTotalPriceShoppingCartListener {
	/**
	 * Create the event listener.
	 */
	public function __construct ( protected CartRepository $cartRepository ) {
		//
	}
	
	/**
	 * Handle the event.
	 */
	public function handle ( object $event ): void {
		$user = $event->user;
		$cart = $this->cartRepository->getUserShoppingCart($user);
		if ( $cart ) {
			$cart->total_price = $cart->cartItems()
									  ->sum('price');
			$cart->save();
		}
	}
}
