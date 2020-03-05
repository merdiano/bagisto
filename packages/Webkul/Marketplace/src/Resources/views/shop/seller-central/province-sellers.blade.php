@extends('marketplace::shop.layouts.master')

@section('page_title')
    {{ __('marketplace::app.shop.marketplace.title') }}
@stop

@section('content-wrapper')

    <div class="main seller-central-container">
        @if ($sellers->count())
            <div class="popular-sellers-container">
                <div class="popular-sellers-heading">
                    {{$code}}   {{ __('marketplace::app.shop.marketplace.popular-sellers') }}
                </div>

                <div class="popular-sellers-list">

                    @foreach ($sellers as $seller)
                        <div class="popular-seller-item">

                            <div class="profile-information">

                                <div class="profile-logo-block">
                                    @if ($logo = $seller->logo_url)
                                        <img src="{{ $logo }}" style="width: 100%; height: 100%;"/>
                                    @else
                                        <img src="{{ bagisto_asset('images/default-logo.svg') }}" />
                                    @endif
                                </div>

                                <div class="profile-information-block">

                                    <a href="{{ route('marketplace.seller.show', $seller->url) }}" class="shop-title">{{ $seller->shop_title }}</a>

                                    @if ($seller->country)
                                        <label class="shop-address">
                                            {{ $seller->city . ', '. $seller->state . ' (' . core()->country_name($seller->country) . ')' }}
                                        </label>
                                    @endif

                                    <div class="social-links">
                                        @if ($seller->facebook)
                                            <a href="https://www.facebook.com/{{$seller->facebook}}" target="_blank">
                                                <i class="icon social-icon mp-facebook-icon"></i>
                                            </a>
                                        @endif

                                        @if ($seller->twitter)
                                            <a href="https://www.twitter.com/{{$seller->twitter}}" target="_blank">
                                                <i class="icon social-icon mp-twitter-icon"></i>
                                            </a>
                                        @endif

                                        @if ($seller->instagram)
                                            <a href="https://www.instagram.com/{{$seller->instagram}}" target="_blank"><i class="icon social-icon mp-instagram-icon"></i></a>
                                        @endif

                                        @if ($seller->pinterest)
                                            <a href="https://www.pinterest.com/{{$seller->pinterest}}" target="_blank"><i class="icon social-icon mp-pinterest-icon"></i></a>
                                        @endif

                                        @if ($seller->skype)
                                            <a href="https://www.skype.com/{{$seller->skype}}" target="_blank">
                                                <i class="icon social-icon mp-skype-icon"></i>
                                            </a>
                                        @endif

                                        @if ($seller->linked_in)
                                            <a href="https://www.linkedin.com/{{$seller->linked_in}}" target="_blank">
                                                <i class="icon social-icon mp-linked-in-icon"></i>
                                            </a>
                                        @endif

                                        @if ($seller->youtube)
                                            <a href="https://www.youtube.com/{{$seller->youtube}}" target="_blank">
                                                <i class="icon social-icon mp-youtube-icon"></i>
                                            </a>
                                        @endif
                                    </div>

                                    <a href="{{ route('marketplace.products.index', $seller->url) }}" class="btn btn-lg btn-primary">
                                        {{ __('marketplace::app.shop.sellers.profile.visit-store') }}
                                    </a>

                                </div>

                            </div>

                            <?php $popularProducts = app('Webkul\Marketplace\Repositories\ProductRepository')->getPopularProducts($seller->id); ?>

                            <div class="seller-products">

                                @foreach ($popularProducts as $sellerProduct)

                                    <?php $productBaseImage = $productImageHelper->getProductBaseImage($sellerProduct->product, 5) ?>

                                    <div class="seller-product-item">
                                        <a href="{{ route('shop.products.index', $sellerProduct->product->url_key) }}" title="{{ $sellerProduct->product->name }}">
                                            <img src="{{ $productBaseImage['medium_image_url'] }}" />
                                        </a>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
        @endif
    </div>
@endsection