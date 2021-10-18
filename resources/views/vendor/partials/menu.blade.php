<aside id="aside" class="app-aside hidden-xs bg-dark" style="overflow-y: auto;white-space: nowrap; -webkit-overflow-scrolling: touch;">
    <div class="aside-wrap">
        <div class="navi-wrap">
            {{--            @include('vendor.partials.user')--}}
            <nav ui-nav class="navi clearfix">
                <ul class="nav">
                    <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                        <span>Navigation</span>
                    </li>
                    @foreach(auth()->user()->role->menu_sections_list as $menu_section)
                        <li {{ (request()->is('admin/'.$menu_section->name)) ? 'class=active' : '' }}>
                            <a href="{{ route('admin.'.$menu_section->name.'.index') }}" class="auto">
                                <i class="{{$menu_section->fa_class}}"></i>
                                <span class="font-bold">{{$menu_section->label}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>
</aside>
