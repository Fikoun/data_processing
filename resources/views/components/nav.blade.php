<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #76c045 !important;">
  <a class="navbar-brand" href="{{ route('home') }}">
      <img src="{{{ asset('favico.ico') }}}" width="30" height="30" class="d-inline-block align-top" alt="">
    Data Processing
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon text"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">    
        <a class="nav-link" href="{{ route('home') }}">Home</span></a>
      </li>
      <li class="nav-item active">    
        <a class="nav-link" href="{{ route('contact') }}">Contact</a>
      </li>
      @guest
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}">Login</a>
        </li>
      @else
        <li class="nav-item">    
          <a class="nav-link" href="{{ route('documentation') }}">Documentation</span></a>
        </li>
        <li class="nav-item">    
          <a class="nav-link" href="{{ route('measurements') }}">Measurements</a>
        </li>
      @endguest

    </ul>
    <ul class="navbar-nav ml-auto">
        @guest
        @else
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                  {{ Auth::user()->name }} <span class="caret"></span>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{ route('logout') }}"
                     onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                      {{ __('Logout') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                  </form>
              </div>
          </li>
        @endguest
    </ul>
  </div>
</nav>