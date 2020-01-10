{assign var="noSidebar" value=true}
{include file='header.tpl'}

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#registration-form').find('input.js-ajax-validate').blur(function(e){
			var aParams={ };
			if ($(e.target).attr('name')=='password_confirm') {
				aParams['password']=$('#user-password').val();
			}
			if ($(e.target).attr('name')=='password') {
				aParams['password']=$('#user-password').val();
				if ($('#user-password-confirm').val()) {
					ls.user.validateRegistrationField('password_confirm',$('#user-password-confirm').val(),$('#registration-form'),{ 'password': $(e.target).val() });
				}
			}
			ls.user.validateRegistrationField($(e.target).attr('name'),$(e.target).val(),$('#registration-form'),aParams);
		});
		$('#registration-form').bind('submit',function(){
			ls.user.registration('registration-form');
			return false;
		});
		$('#registration-form-submit').attr('disabled',false);
	});
</script>

<h2 class="page-header">{$aLang.registration}</h2>

{hook run='registration_begin'}

<form action="{router page='registration'}" method="post" id="registration-form">
	{hook run='form_registration_begin'}

	<p><label for="popup-registration-login">{$aLang.registration_login}</label>
	<input type="text" name="login" id="popup-registration-login" value="{$_aRequest.login}" class="input-text input-width-300 js-ajax-validate" />
	<i class="icon-ok-green validate-ok-field-login" style="display: none"></i>
	<i class="icon-question-sign js-tip-help" title="{$aLang.registration_login_notice}"></i>
	<small class="validate-error-hide validate-error-field-login"></small></p>

	<p><label for="popup-registration-mail">{$aLang.registration_mail}</label>
	<input type="text" name="mail" id="popup-registration-mail" value="{$_aRequest.mail}" class="input-text input-width-300 js-ajax-validate" />
	<i class="icon-ok-green validate-ok-field-mail" style="display: none"></i>
	<i class="icon-question-sign js-tip-help" title="{$aLang.registration_mail_notice}"></i>
	<small class="validate-error-hide validate-error-field-mail"></small></p>

	<p><label for="popup-registration-user-password">{$aLang.registration_password}</label>
	<input type="password" name="password" id="popup-registration-user-password" value="" class="input-text input-width-300 js-ajax-validate" />
	<i class="icon-ok-green validate-ok-field-password" style="display: none"></i>
	<i class="icon-question-sign js-tip-help" title="{$aLang.registration_password_notice}"></i>
	<small class="validate-error-hide validate-error-field-password"></small></p>

	<p><label for="popup-registration-user-password-confirm">{$aLang.registration_password_retry}</label>
	<input type="password" value="" id="popup-registration-user-password-confirm" name="password_confirm" class="input-text input-width-300 js-ajax-validate" />
	<i class="icon-ok-green validate-ok-field-password_confirm" style="display: none"></i>
	<small class="validate-error-hide validate-error-field-password_confirm"></small></p>

	{if $oConfig->GetValue('reCaptcha.enabled')}
		{hookb run="popup_registration_captcha"}
			<p><div class="g-recaptcha" data-sitekey="{cfg name="reCaptcha.key"}"></div></p>
		{/hookb}
	{/if}

	{hook run='form_registration_end'}

	<button type="submit" name="submit_register" class="button button-primary" id="registration-form-submit" disabled="disabled">{$aLang.registration_submit}</button>
</form>

{hook run='registration_end'}

{include file='footer.tpl'}