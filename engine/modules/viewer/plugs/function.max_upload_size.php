<?php

include_once 'engine/include/function.php';

function smarty_function_max_upload_size() {
	return floor(get_maximum_upload_size() / 1048576);
}
