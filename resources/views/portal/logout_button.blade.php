<form method="POST" action="{{ route('logout') }}" class="portal-logout-form">
    @csrf
    <button type="submit" class="portal-nav-link logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</form>
