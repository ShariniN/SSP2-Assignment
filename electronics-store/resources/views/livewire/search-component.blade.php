<div>
    <input type="text" wire:model="query" placeholder="Search products..." class="border p-2 w-full" />

    @if(!empty($results))
        <ul class="border mt-2 bg-white">
            @foreach($results as $product)
                <li class="p-2 border-b">
                    <a href="{{ route('product.details', $product['id']) }}">
                        {{ $product['name'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
