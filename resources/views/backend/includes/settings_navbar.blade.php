 <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
     <li class="nav-item">
         <a class="nav-link {{ Request::routeIs(['admin.system.settings.environment']) ? 'active ' : '' }}"
             href="{{ route('admin.system.settings.environment') }}" role="tab">{{ __tr('Environment Setup') }}
         </a>
     </li>
     <li class="nav-item">
         <a class="nav-link {{ Request::routeIs(['admin.system.settings.smtp']) ? 'active ' : '' }}"
             href="{{ route('admin.system.settings.smtp') }}" role="tab">{{ __tr('SMTP Setup') }}
         </a>
     </li>
     <li class="nav-item">
         <a class="nav-link {{ Request::routeIs(['admin.system.settings.social.login']) ? 'active ' : '' }}"
             href="{{ route('admin.system.settings.social.login') }}" role="tab">{{ __tr('Social Media Login') }}
         </a>
     </li>

 </ul>
