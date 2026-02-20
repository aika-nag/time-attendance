<header class="header">
    <div class="header-logo">
        <a href="/admin/attendance/list"><img src="{{ asset('/image/logo.png') }}" alt="ロゴ" class="image"></a>
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
                <li>勤怠一覧</li>
                <li><a href="/admin/staff/list" class="page-link">スタッフ一覧</a></li>
                <li>申請一覧</li>
                <li>
                    <form action="/admin/logout" method="post">
                    @csrf
                        <button class="logout-button">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</header>
