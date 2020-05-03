<?php

function smarty_modifier_cfg($key, $instance = Engine\Config::DEFAULT_CONFIG_INSTANCE) {
	return Engine\Config::Get($key, $instance);
}
