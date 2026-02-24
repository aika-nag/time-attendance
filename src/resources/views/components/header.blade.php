<header class="header">
    <div class="header-logo">
        <a href="/"><img src="{{ asset('/image/logo.png') }}" alt="ロゴ" class="image"></a>
    </div>
    <div class="site-menu">
        <input id="toggle-menu-button" type="checkbox" hidden></input>
        <label for="toggle-menu-button" class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </label>
        <nav class="header-nav">
            <ul class="nav-item">
                <li><a href="/" class="page-link">勤怠</a></li>
                <li><a href="/attendance/list" class="page-link">勤怠一覧</a></li>
                <li><a href="/stamp_correction_request/list" class="page-link">申請</a></li>
                <li>
                    <form action="/logout" method="post">
                    @csrf
                        <button class="logout-button">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</header>
