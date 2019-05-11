{if !$oUserCurrent}
	<div class="modal modal-login" id="window_login_form">
		<header class="modal-header">
			<h3>{$aLang.user_authorization}</h3>
			<a href="#" class="close jqmClose material-icons">close</a>
		</header>
		
		
		<script type="text/javascript">
			jQuery(function($){
				$('#popup-login-form').bind('submit',function(){
					ls.user.login('popup-login-form');
					return false;
				});
				$('#popup-login-form-submit').attr('disabled',false);
			});
		</script>
		
		<div class="modal-content">
			<ul class="nav nav-pills nav-pills-tabs">
				<li class="active js-block-popup-login-item" data-type="login"><a href="#">{$aLang.user_login_submit}</a></li>
				{if !$oConfig->GetValue('general.reg.invite')}
					<li class="js-block-popup-login-item" data-type="registration"><a href="#">{$aLang.registration}</a></li>
				{else}
					<li><a href="{router page='registration'}">{$aLang.registration}</a></li>
				{/if}
				<li class="js-block-popup-login-item" data-type="reminder"><a href="#">{$aLang.password_reminder}</a></li>
			</ul>
			
			
			<div class="tab-content js-block-popup-login-content" data-type="login">
				<h5><strong>Внимание!</strong></h5>
				<p>На сайте был призведен сброс паролей. Если вы не получили новый пароль на почту, свяжитесь с администрацией по 
почте (bunkerlunavodru@gmail.com) или иным способом для восстановления.</p><br />

				{hook run='login_popup_begin'}
				<form action="{router page='login'}" method="post" id="popup-login-form">
					{hook run='form_login_popup_begin'}

					<p><label for="popup-login">{$aLang.user_login}:</label>
					<input type="text" name="login" id="popup-login" class="input-text input-width-300"></p>
					
					<p><label for="popup-password">{$aLang.user_password}:</label>
					<input type="password" name="password" id="popup-password" class="input-text input-width-300">
					<small class="validate-error-hide validate-error-login"></small></p>
					
					<p><span class="checkbox"><span><input type="checkbox" id="remember" name="remember" class="input-checkbox" checked><label for="remember">{$aLang.user_login_remember}</label></span></span></p>

					{hook run='form_login_popup_end'}

					<input type="hidden" name="return-path" value="{$PATH_WEB_CURRENT|escape:'html'}">
					<button type="submit" name="submit_login" class="button button-primary" id="popup-login-form-submit" disabled="disabled">{$aLang.user_login_submit}</button>
				</form>
				{hook run='login_popup_end'}
			</div>


			{if !$oConfig->GetValue('general.reg.invite')}
			<div data-type="registration" class="tab-content js-block-popup-login-content" style="display:none;">
				<script type="text/javascript">
					jQuery(document).ready(function($){
						$('#popup-registration-form').find('input.js-ajax-validate').blur(function(e){
							var aParams={ };
							if ($(e.target).attr('name')=='password_confirm') {
								aParams['password']=$('#popup-registration-user-password').val();
							}
							if ($(e.target).attr('name')=='password') {
								aParams['password']=$('#popup-registration-user-password').val();
								if ($('#popup-registration-user-password-confirm').val()) {
									ls.user.validateRegistrationField('password_confirm',$('#popup-registration-user-password-confirm').val(),$('#popup-registration-form'),{ 'password': $(e.target).val() });
								}
							}
							ls.user.validateRegistrationField($(e.target).attr('name'),$(e.target).val(),$('#popup-registration-form'),aParams);
						});
						$('#popup-registration-form').bind('submit',function(){
							ls.user.registration('popup-registration-form');
							return false;
						});
						$('#popup-registration-form-submit').attr('disabled',false);
					});
				</script>

				{hook run='registration_begin' isPopup=true}
				<form action="{router page='registration'}" method="post" id="popup-registration-form">
					{hook run='form_registration_begin' isPopup=true}

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

					{if $oConfig->GetValue('reCaptcaha.enabled')}
					{hookb run="popup_registration_captcha"}
						<div class="g-recaptcha" data-sitekey="{cfg name="reCaptcha.key"}"></div>
					{/hookb}
					{/if}

					{hook run='form_registration_end' isPopup=true}

					<input type="hidden" name="return-path" value="{$PATH_WEB_CURRENT|escape:'html'}">
					<button type="submit" name="submit_register" class="button button-primary" id="popup-registration-form-submit" disabled="disabled">{$aLang.registration_submit}</button>
				</form>
				{hook run='registration_end' isPopup=true}
			</div>
			{/if}
			
			
			<div data-type="reminder" class="tab-content js-block-popup-login-content" style="display:none;">
				<script type="text/javascript">
					jQuery(document).ready(function($){
						$('#popup-reminder-form').bind('submit',function(){
							ls.user.reminder('popup-reminder-form');
							return false;
						});
						$('#popup-reminder-form-submit').attr('disabled',false);
					});
				</script>
				<form action="{router page='login'}reminder/" method="POST" id="popup-reminder-form">
					<p><label for="popup-reminder-mail">{$aLang.password_reminder_email}</label>
					<input type="text" name="mail" id="popup-reminder-mail" class="input-text input-width-300" />
					<small class="validate-error-hide validate-error-reminder"></small></p>

					<button type="submit" name="submit_reminder" class="button button-primary" id="popup-reminder-form-submit" disabled="disabled">{$aLang.password_reminder_submit}</button>
				</form>
			</div>
		</div>
	</div>
{/if}
