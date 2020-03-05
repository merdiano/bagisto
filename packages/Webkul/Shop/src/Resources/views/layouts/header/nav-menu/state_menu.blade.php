{!! view_render_event('bagisto.shop.layout.header.category.before') !!}

<?php

$provinces = app('Webkul\Core\Repositories\CountryStateRepository')->all();
?>

<province-nav provinces='@json($provinces)' url="{{url()->to('/')}}"></province-nav>

{!! view_render_event('bagisto.shop.layout.header.category.after') !!}


@push('scripts')


<script type="text/x-template" id="province-nav-template">

    <ul class="nav">
        <province-item
            v-for="(item, index) in items"
            :key="index"
            :url="url"
            :item="item"
            :parent="index">
        </province-item>
    </ul>

</script>

<script>
    Vue.component('province-nav', {

        template: '#province-nav-template',

        props: {
            provinces: {
                type: [Array, String, Object],
                required: false,
                default: (function () {
                    return [];
                })
            },

            url: String
        },

        data: function(){
            return {
                items_count:0
            };
        },

        computed: {
            items: function() {
                return JSON.parse(this.provinces)
            }
        },
    });
</script>

<script type="text/x-template" id="province-item-template">
    <li>
        <a :href="url+'/provinces/'+this.item.code">
            @{{ province_name }}&emsp;
        </a>
    </li>
</script>

<script>
    Vue.component('province-item', {

        template: '#province-item-template',

        props: {
            item:  Object,
            url: String,
        },

        data: function() {
            return {
                items_count:0,
                show: false,
            };
        },

        mounted: function() {
            if(window.innerWidth > 770){
                this.show = true;
            }
        },

        computed: {

            province_name: function() {
                if (this.item.translations && this.item.translations.length) {
                    this.item.translations.forEach(function(translation) {
                        if (translation.locale == document.documentElement.lang)
                            return translation.name;
                    });
                }

                return this.item.default_name;
            }
        },

        methods: {
            showOrHide: function() {
                this.show = !this.show;
            }
        }
    });
</script>


@endpush