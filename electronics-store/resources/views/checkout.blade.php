<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Messages -->
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf
                
                <!-- Billing Information -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-medium text-gray-900">Billing Information</h2>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    Required fields are marked with *
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Name Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                        <input type="text" name="first_name" id="first_name" 
                                               value="{{ old('first_name', $user->first_name ?? '') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('first_name') border-red-300 @enderror" 
                                               required>
                                        @error('first_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                        <input type="text" name="last_name" id="last_name" 
                                               value="{{ old('last_name', $user->last_name ?? '') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('last_name') border-red-300 @enderror" 
                                               required>
                                        @error('last_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                        <input type="email" name="email" id="email" 
                                               value="{{ old('email', $user->email ?? '') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror" 
                                               required>
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                        <input type="tel" name="phone" id="phone" 
                                               value="{{ old('phone') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-300 @enderror" 
                                               required>
                                        @error('phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                                    <textarea name="address" id="address" rows="3" 
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-300 @enderror" 
                                              required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Location Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700">City *</label>
                                        <input type="text" name="city" id="city" 
                                               value="{{ old('city') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('city') border-red-300 @enderror" 
                                               required>
                                        @error('city')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700">State *</label>
                                        <input type="text" name="state" id="state" 
                                               value="{{ old('state') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('state') border-red-300 @enderror" 
                                               required>
                                        @error('state')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="zip_code" class="block text-sm font-medium text-gray-700">ZIP Code *</label>
                                        <input type="text" name="zip_code" id="zip_code" 
                                               value="{{ old('zip_code') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('zip_code') border-red-300 @enderror" 
                                               required>
                                        @error('zip_code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">Country *</label>
                                    <select name="country" id="country" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('country') border-red-300 @enderror" 
                                            required>
                                        <option value="">Select Country</option>
                                        <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                                        <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                        <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                        <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                        <option value="LK" {{ old('country') == 'LK' ? 'selected' : '' }}>Sri Lanka</option>
                                    </select>
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Payment Information</h2>
                            
                            <!-- Payment Method Selection -->
                            <div class="mb-6">
                                <fieldset>
                                    <legend class="block text-sm font-medium text-gray-700 mb-3">Payment Method *</legend>
                                    <div class="space-y-3">
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="payment_method" value="credit_card" 
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" 
                                                   {{ old('payment_method') == 'credit_card' ? 'checked' : '' }} required>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Credit Card</span>
                                            <div class="ml-auto flex space-x-2">
                                                <img src="https://via.placeholder.com/32x20/000000/FFFFFF?text=VISA" alt="Visa" class="h-5">
                                                <img src="https://via.placeholder.com/32x20/000000/FFFFFF?text=MC" alt="Mastercard" class="h-5">
                                            </div>
                                        </label>
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="payment_method" value="debit_card" 
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" 
                                                   {{ old('payment_method') == 'debit_card' ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Debit Card</span>
                                        </label>
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="payment_method" value="paypal" 
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" 
                                                   {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm font-medium text-gray-700">PayPal</span>
                                            <img src="https://via.placeholder.com/60x20/003087/FFFFFF?text=PayPal" alt="PayPal" class="ml-auto h-5">
                                        </label>
                                    </div>
                                </fieldset>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Card Details -->
                            <div id="card-details" class="space-y-6">
                                <div>
                                    <label for="card_name" class="block text-sm font-medium text-gray-700">Name on Card</label>
                                    <input type="text" name="card_name" id="card_name" 
                                           value="{{ old('card_name') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('card_name') border-red-300 @enderror">
                                    @error('card_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                                    <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456"
                                           value="{{ old('card_number') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('card_number') border-red-300 @enderror">
                                    @error('card_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label for="card_expiry" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                        <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/YY"
                                               value="{{ old('card_expiry') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('card_expiry') border-red-300 @enderror">
                                        @error('card_expiry')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="card_cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                        <input type="text" name="card_cvv" id="card_cvv" placeholder="123"
                                               value="{{ old('card_cvv') }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('card_cvv') border-red-300 @enderror">
                                        @error('card_cvv')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Order Summary</h2>
                            
                            <!-- Cart Items -->
                            <div class="flow-root">
                                <ul role="list" class="-my-6 divide-y divide-gray-200">
                                    @foreach($cartItems as $id => $item)
                                        <li class="flex py-4">
                                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                                <img src="{{ $item['image'] ?? 'https://via.placeholder.com/64x64/F3F4F6/9CA3AF?text=Product' }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="h-full w-full object-cover object-center">
                                            </div>
                                            
                                            <div class="ml-4 flex flex-1 flex-col">
                                                <div>
                                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                                        <h3 class="text-sm">{{ $item['name'] }}</h3>
                                                        <p class="ml-4">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500">Qty: {{ $item['quantity'] }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Order Totals -->
                            <div class="border-t border-gray-200 pt-6 mt-6">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <p>Subtotal</p>
                                    <p>${{ number_format($subtotal, 2) }}</p>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mt-2">
                                    <p>Shipping</p>
                                    <p>{{ $shipping > 0 ? ' . number_format($shipping, 2) : 'Free' }}</p>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mt-2">
                                    <p>Tax</p>
                                    <p>${{ number_format($tax, 2) }}</p>
                                </div>
                                <div class="flex justify-between text-base font-medium text-gray-900 mt-4 pt-4 border-t border-gray-200">
                                    <p>Total</p>
                                    <p>${{ number_format($total, 2) }}</p>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <div class="mt-6">
                                <button type="submit" 
                                        class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                                        id="place-order-btn">
                                    <span class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" id="loading-spinner">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span id="btn-text">Place Order</span>
                                    </span>
                                </button>
                            </div>
                            
                            <!-- Security Notice -->
                            <div class="mt-4 flex items-center justify-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                Secure SSL Encrypted
                            </div>
                            
                            <p class="text-xs text-gray-500 text-center mt-2">
                                By placing your order, you agree to our 
                                <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms & Conditions</a> 
                                and 
                                <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const cardDetails = document.getElementById('card-details');
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('place-order-btn');
            const spinner = document.getElementById('loading-spinner');
            const btnText = document.getElementById('btn-text');
            
            function toggleCardDetails() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                const isCardPayment = selectedMethod && (selectedMethod.value === 'credit_card' || selectedMethod.value === 'debit_card');
                
                if (isCardPayment) {
                    cardDetails.style.display = 'block';
                    cardDetails.classList.remove('opacity-50');
                    // Make card fields required
                    ['card_number', 'card_expiry', 'card_cvv', 'card_name'].forEach(id => {
                        document.getElementById(id).required = true;
                    });
                } else {
                    cardDetails.style.display = 'none';
                    cardDetails.classList.add('opacity-50');
                    // Remove required from card fields
                    ['card_number', 'card_expiry', 'card_cvv', 'card_name'].forEach(id => {
                        document.getElementById(id).required = false;
                    });
                }
            }
            
            // Payment method change handlers
            paymentMethods.forEach(method => {
                method.addEventListener('change', toggleCardDetails);
            });
            
            // Initial check
            toggleCardDetails();
            
            // Card number formatting
            document.getElementById('card_number').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
                if (formattedValue.length > 19) {
                    formattedValue = formattedValue.substring(0, 19);
                }
                e.target.value = formattedValue;
            });
            
            // Expiry date formatting
            document.getElementById('card_expiry').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
            
            // CVV formatting
            document.getElementById('card_cvv').addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
            });
            
            // Form submission handling
            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                btnText.textContent = 'Processing...';
                
                // Re-enable after 30 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    spinner.classList.add('hidden');
                    btnText.textContent = 'Place Order';
                }, 30000);
            });
            
            // Auto-fill user data enhancement
            @auth
            const userEmail = '{{ $user->email ?? "" }}';
            const userFirstName = '{{ $user->first_name ?? "" }}';
            const userLastName = '{{ $user->last_name ?? "" }}';
            
            if (userEmail && !document.getElementById('email').value) {
                document.getElementById('email').value = userEmail;
            }
            if (userFirstName && !document.getElementById('first_name').value) {
                document.getElementById('first_name').value = userFirstName;
            }
            if (userLastName && !document.getElementById('last_name').value) {
                document.getElementById('last_name').value = userLastName;
            }
            @endauth
            
            // Validation enhancements
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            
            emailInput.addEventListener('blur', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email)) {
                    this.classList.add('border-red-300');
                } else {
                    this.classList.remove('border-red-300');
                }
            });
            
            phoneInput.addEventListener('input', function(e) {
                // Allow only numbers, spaces, hyphens, and plus signs
                e.target.value = e.target.value.replace(/[^0-9\s\-\+\(\)]/g, '');
            });
        });
    </script>
    @endpush
</x-app-layout>