<?php
    namespace toolbox;
?><a class="item" href="/account">
    <i class="icon-user"></i> My Account [ <span style="font-weight: 600;"><?php
        echo user::getUserLoggedIn() -> getEmail();
    ?></span> ]</a><?php
?><a class="item" href="/logout">Log Out</a>
