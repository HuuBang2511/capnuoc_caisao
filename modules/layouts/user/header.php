<header id="page-header">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <a class="font-w600 text-white tracking-wide" href="<?= Yii::$app->homeUrl.'app/cabenh' ?>">
                <img src="https://static.vnuhcm.edu.vn/images/20190530/e8fa53887cb9827fe7b811fe098e0c3c.png" alt="logo" width="165px" class="logo-default" style="line-height: inherit">
            </a>
        </div>
        <div>


        </div>
    </div>
    <div id="page-header-search" class="overlay-header bg-header-dark">
        <div class="content-header">
            <form class="w-100" action="#?" method="POST">
                <div class="input-group">
                    <button type="button" class="btn btn-primary" data-toggle="layout" data-action="header_search_off">
                        <i class="fa fa-fw fa-times-circle"></i>
                    </button>
                    <input type="text" class="form-control" placeholder="Search your websites.." id="page-header-search-input" name="page-header-search-input">
                </div>
            </form>
        </div>
    </div>
    <div id="page-header-loader" class="overlay-header bg-primary">
        <div class="content-header">
            <div class="w-100 text-center">
                <i class="fa fa-fw fa-2x fa-spinner fa-spin text-white"></i>
            </div>
        </div>
    </div>
    <div id="page-header-loader" class="overlay-header bg-primary-darker">
        <div class="content-header">
            <div class="w-100 text-center">
                <i class="fa fa-fw fa-2x fa-sun fa-spin text-white"></i>
            </div>
        </div>
    </div>
</header>