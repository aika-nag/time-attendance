<header class="header admin">
    <div class="header-logo">
        <a href="/admin/attendance/list"><img src="{{ asset('/image/logo.png') }}" alt="ロゴ" class="image"></a>
    </div>
    <div class="site-menu admin">
        <input id="toggle-menu-button" type="checkbox" hidden></input>
        <label for="toggle-menu-button" class="menu-icon admin">
            <span></span>
            <span></span>
            <span></span>
        </label>
        <nav class="header-nav">
            <ul class="nav-item admin">
                <li><a href="/admin/attendance/list" class="page-link">勤怠一覧</a></li>
                <li><a href="/admin/staff/list" class="page-link">スタッフ一覧</a></li>
                <li><a href="/admin/stamp_correction_request/list" class="page-link">申請一覧</a></li>
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
