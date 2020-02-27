<?php $productRepository = app('Webkul\Marketplace\Repositories\ProductRepository'); ?>

@if (request()->route()->getName() == 'shop.products.index')

    @push('css')
        <style>
            .product-detail .product-offers {
                margin-bottom: 15px;
            }
        </style>
    @endpush

    @if ($product->type != 'configurable')
        @if ($count = $productRepository->getSellerCount($product->product))
            <div class="product-offers">
                <a href="{{ route('marketplace.product.offers.index', $product->product_id) }}">
                    {{
                        __('marketplace::app.shop.products.seller-count', [
                            'count' => $count
                        ])
                    }}
                </a>
            </div>
        @endif
    @else
        <div class="product-offers configurable" style="display: none">
            <a href="{{ route('marketplace.product.offers.index', '_id_') }}">
                {{
                    __('marketplace::app.shop.products.seller-count', [
                        'count' => '_count_'
                    ])
                }}
            </a>
        </div>

        <?php
            $variants = [];

            foreach ($product->product->variants as $variant) {
                $variants[$variant->id] = $productRepository->getSellerCount($variant);
            }

        ?>

        @push('scripts')

            <script>
                var variants = @json($variants);

                eventBus.$on('configurable-variant-selected-event', function(variantId) {
                    if (typeof variants[variantId] != "undefined" && variants[variantId]) {
                        $('.product-offers.configurable').show()

                        var text = $('.product-offers.configurable a').text();
                        var href = $('.product-offers.configurable a').attr('href');

                        $('.product-offers.configurable a').text(text.replace("_count_", variants[variantId]))
                        $('.product-offers.configurable a').attr('href', href.replace("_id_", variantId))

                    } else {
                        $('.product-offers.configurable').hide()
                    }
                });

            </script>

        @endpush
    @endif

@endif