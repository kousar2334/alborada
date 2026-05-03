 <div class="content-header">
     <div class="container-fluid">
         <div class="row mb-2">
             <div class="col-sm-6">
                 <h1 class="m-0"> {{ __tr($title) }}</h1>
             </div>
             <div class="col-sm-6">
                 <ol class="breadcrumb float-sm-right">
                     @if ($has_home_link)
                         <li class="breadcrumb-item">
                             <a href="{{ route('admin.dashboard') }}">
                                 {{ __tr('Home') }}
                             </a>
                         </li>
                     @endif
                     @foreach ($links as $key => $link)
                         <li class="breadcrumb-item {{ $link['active'] ? 'active' : '' }}">
                             @if (!$link['active'] && $link['route'] != null)
                                 <a href="{{ route($link['route']) }}">
                                     {{ __tr($link['title']) }}
                                 </a>
                             @else
                                 {{ __tr($link['title']) }}
                             @endif
                         </li>
                     @endforeach
                 </ol>
             </div>
         </div>
     </div>
 </div>
