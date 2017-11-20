//**************************************************************************************************
function LoadMoreActions(LastActionId){

	var params = {};
	params['LastActionId'] 	= LastActionId;

	$("#LoadMoreButton").toggleClass('loading');

	return ls.ajax(aRouter['feedbacks']+'LoadMoreActions', params, function(data){
		if (data.aResult.Errors.length > 0){
			var $aErrors = data.aResult.Errors;
			for(var i=0; i < $aErrors.length; i++){
				var $sError	= $aErrors[i];
				ls.msg.error('',$sError);
			}
		} else {
			$("#stream-list").append(data.aResult.Text);
			$("#LoadMoreButton").remove();
		}
	});

};