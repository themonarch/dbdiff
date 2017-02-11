<?php
    namespace toolbox;
	messages::readSessionMessages();
	messages::readMessages();
	messages::readMessages('wrong');
	messages::readMessages('form');
?>