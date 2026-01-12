<header class="header">
    <div class="header-logo">
        <a href="/"><img src="{{ asset('/image/logo.png') }}" alt="ロゴ"></a>
    </div>
    <div class="site-menu">
        <button class="toggle-menu-button" id="toggle-menu-button"></button>
        <nav class="header-nav">
            <ul class="nav-item">
                <li>勤怠</li>
                <li>勤怠一覧</li>
                <li>申請</li>
                <li>
                    @auth('admin')
                    <form action="/admin/logout" method="post">
                    @else
                    <form action="/logout" method="post">
                    @endauth
                    @csrf
                        <button class="logout-button">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</header>
