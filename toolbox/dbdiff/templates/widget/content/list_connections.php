<?php namespace toolbox;
$dt = datatableV2::create()
    ->setSelect('*')
    ->setFrom('`db_connections`')
    ->setWhere('`user_id` = '.user::getUserLoggedIn()->getID())
    ->defineCol('name', 'Connection', function($val, $rows){
        return connection::get($rows->connection_id)->getName();
    })
    ->defineCol('host', 'Host', function($val, $rows){
        return connection::get($rows->connection_id)->getHost();
    })
    ->defineCol('date', 'Date Added', function($val){
        return '<span title="'.$val.'" class="timeago">'.$val.'</span>';
    })
    ->defineCol('connection_id', 'Actions', function($val){ ?>
        <a href="#"
            class="btn btn-small btn-blue"
            data-overlay-id="connect"
            title="Edit Connection">Connect</a>
        <a href="#"
            class="btn btn-small btn-silver"
            data-overlay-id="edit_connection"
            title="Edit Connection"><i class="icon-edit"></i></a>
    <?php })
    ->addView(function($tpl){ ?>
    <div class="datatable-info datatable-section">
        <a href="/dbdiff/manage_databases/db/create?prev_overlay=connections"
        class="btn btn-small btn-blue"
        data-overlay-id="create_connection">
            <i class="icon-plus-circled"></i> Create New Connection
        </a>
    </div>
    <?php }, 'footer')
    ->setPaginationDestination('#'.$widget_id)
    ->setPaginationLink($widget_url)
    ->renderViews();