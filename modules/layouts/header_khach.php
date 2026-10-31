<header id="page-header" class="bg-primary-dark">
    <div class="content-header">
        <div>
            <button type="button" class="btn btn-light" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>
        <div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-light" id="page-header-user-dropdown" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-user d-sm-none"></i>
                    <span class="d-none d-sm-inline-block"><?= (!Yii::$app->user->isGuest) ? Yii::$app->user->identity->username : 'Đăng nhập' ?></span>
                    <i class="fa fa-fw fa-angle-down ml-1 d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">

                    <div class="p-2">
                        <a class="dropdown-item"
                           href="http://localhost/luanvany/web/user/auth/login">
                            <i class="far fa-fw fa-user mr-1"></i> Đăng nhập
                        </a>
                        <a class="dropdown-item"
                           href="http://localhost/luanvany/web/user/auth/login">
                            <i class="fa fa-key mr-1"></i> Đăng ký
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
