<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-2 rounded-xl">
                        <i class="fas fa-bolt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">
                            ElectroStore
                        </h2>
                        <p class="text-xs text-gray-400 -mt-1">Electronics & Gadgets</p>
                    </div>
                </div>
                <p class="text-gray-300 mb-6 max-w-md">
                    Your trusted destination for premium electronics and cutting-edge technology. 
                    Discover the latest gadgets with warranty and free shipping.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <i class="fab fa-linkedin text-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white transition-colors">All Products</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Categories</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Deals</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Support</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Shipping Info</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Returns</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Warranty</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom -->
        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm mb-4 md:mb-0">
                &copy; {{ date('Y') }} ElectroStore. All rights reserved.
            </p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>