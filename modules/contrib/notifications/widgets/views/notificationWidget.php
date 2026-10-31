<style>
    .navbar-nav-link .badge {
        top: unset;
        right: unset;
        bottom: 55%;
        left: 50%;
    }
</style>
<li class="nav-item dropdown" id="notification">
    <a href="#" class="navbar-nav-link dropdown-toggle caret-0 h-100" data-toggle="dropdown">
        <i class="icon-bell2"></i>
        <span class="d-md-none ml-2">Notifications</span>
        <span class="badge badge-pill bg-warning-400 ml-auto ml-md-0" v-cloak>{{ notifications.length > 0 ? notifications.length : '' }}</span>
    </a>
    
    <div class="dropdown-menu dropdown-menu-right dropdown-content wmin-md-350">
        <div class="dropdown-content-header p-2">
            <span class="font-weight-semibold">Notifications</span>
            <a href="#" class="text-default" @click="readAll($event)">Mark all as read</a>
        </div>

        <div class="dropdown-content-body dropdown-scrollable px-0 pb-0" v-cloak>
            <div class="no-notifications" v-if="notifications.length == 0">
                <h6 class="text-center">No new notifications</h6>
            </div>
            <ul class="media-list media-list-linked media-list-bordered" v-else>
                <li v-for="notification in notifications">
                    <a href="#" class="media p-2 align-items-center" @click="read($event, notification.id)">
                        <div class="mr-2">
                            <span class="btn btn-icon btn-sm rounded-round" :class="'bg-' + notification.color"><i :class="notification.icon"></i></span>
                        </div>
                        <div class="media-body">
                            <p class="text-muted mb-1">{{ timeSince(notification.created_at) }}</p>
                            {{ notification.subject }}
                        </div>
                        <!-- <div class="mx-2" v-if="notification.destination_thumbnail">
                            <span><i class="icon-checkmark2"></i></span>
                        </div>
                        <div class="ml-2">
                            <span><i class="icon-checkmark2"></i></span>
                        </div> -->
                    </a>
                </li>
            </ul>
        </div>

        <div class="dropdown-content-footer justify-content-center p-0" v-if="notifications.length > 0">
            <a href="#" class="bg-light text-grey w-100 py-2 text-center" data-popup="tooltip" title="" data-original-title="View all">
                View all
            </a>
        </div>
    </div>
</li>

<script>
    $(function() {
        var notification = new Vue({
            el: '#notification',
            data: {
                notifications: []
            },
            created: function() {
                this.getUnreadNotifications()
            },
            methods: {
                getUnreadNotifications: function() {
                    var _this = this,
                        api = '<?= Yii::$app->homeUrl . 'contrib/notifications/notification/get-unread-notifications' ?>'

                    sendAjax(api, {}, (resp) => {
                        _this.notifications = resp
                    }, 'GET')
                },

                timeSince: function(datetime) {
                    return timeSince(datetime);
                },

                read: function(e, id) {
                    e.preventDefault();
                    var api = '<?= Yii::$app->homeUrl . 'contrib/notifications/notification/read' ?>',
                        data = { id: id}

                    sendAjax(api, data, (resp) => {
                        if(resp) {
                            window.location.assign(resp)
                        } else {
                            toastMeassage('error', 'Something went wrong!')
                        }
                    })
                },

                readAll: function(e) {
                    e.preventDefault();
                    var _this = this,
                        api = '<?= Yii::$app->homeUrl . 'contrib/notifications/notification/read-all' ?>'

                    sendAjax(api, {}, (resp) => {
                        if(resp) {
                            _this.notifications = []
                        } else {
                            toastMeassage('error', 'Something went wrong!')
                        }
                    })
                }
            }
        })
    })
</script>