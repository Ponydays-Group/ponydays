{include file='header.tpl'}
<form action="" method="POST">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <p>
	<label for="theme">Описание:</label> 
        <input type="radio" name="theme" value="dark">Dark</input>
        <input type="radio" name="theme" value="light">Light</input>
        </p>
	<p>
 		<button type="submit" class="button button-primary" name="submit_book_save">Сохранить</button> 
	</p> </form>
