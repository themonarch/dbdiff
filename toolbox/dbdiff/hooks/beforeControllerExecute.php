<?php
namespace toolbox;
if(config::getSetting('HTTP_PROTOCOL') === 'https'){
    utils::redirectToHttps();
}else{
    utils::redirectToNonHttps();
}
