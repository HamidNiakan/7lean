<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyOtherServices {
	/**
	 * Create the event listener.
	 */
	public function __construct () {
		//
	}
	
	/**
	 * Handle the event.
	 */
	public function handle ( ProductCreated $event ): void {
		
		// Notify other services about the new product
	}
}
