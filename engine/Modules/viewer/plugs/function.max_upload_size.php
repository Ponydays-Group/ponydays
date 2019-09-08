<?php

include_once './include/function.php';

function smarty_function_max_upload_size() {
	return floor(get_maximum_upload_size() / 1048576);
}
