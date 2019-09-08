<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

require_once('./lib/Jevix/jevix.class.php');

/**
 * Модуль обработки текста на основе типографа Jevix
 * Позволяет вырезать из текста лишние HTML теги и предотвращает различные попытки внедрить в текст JavaScript
 * <pre>
 * $sText=$this->Text_Parser($sTestSource);
 * </pre>
 * Настройки парсинга находятся в конфиге /config/jevix.php
 *
 * @package engine.modules
 * @since 1.0
 */
class ModuleText extends Module {
	/**
	 * Объект типографа
	 *
	 * @var Jevix
	 */
	protected $oJevix;

	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {
		/**
		 * Создаем объект типографа и запускаем его конфигурацию
		 */
		$this->oJevix = new Jevix();
		$this->JevixConfig();
	}
	/**
	 * Конфигурирует типограф
	 *
	 */
	protected function JevixConfig() {
		// загружаем конфиг
		$this->LoadJevixConfig();
	}
	/**
	 * Загружает конфиг Jevix'а
	 *
	 * @param string $sType Тип конфига
	 * @param bool $bClear	Очищать предыдущий конфиг или нет
	 */
	public function LoadJevixConfig($sType='default',$bClear=true) {
		if ($bClear) {
			$this->oJevix->tagsRules=array();
		}
		$aConfig=Config::Get('jevix.'.$sType);
		if (is_array($aConfig)) {
			foreach ($aConfig as $sMethod => $aExec) {
				foreach ($aExec as $aParams) {
					if (in_array(strtolower($sMethod),array_map("strtolower",array('cfgSetTagCallbackFull','cfgSetTagCallback')))) {
						if (isset($aParams[1][0]) and $aParams[1][0]=='_this_') {
							$aParams[1][0]=$this;
						}
					}
					call_user_func_array(array($this->oJevix,$sMethod), $aParams);
				}
			}
			/**
			 * Хардкодим некоторые параметры
			 */
			unset($this->oJevix->entities1['&']); // разрешаем в параметрах символ &
			if (Config::Get('view.noindex') and isset($this->oJevix->tagsRules['a'])) {
				$this->oJevix->cfgSetTagParamDefault('a','rel','nofollow',true);
			}
		}
	}
	/**
	 * Возвращает объект Jevix
	 *
	 * @return Jevix
	 */
	public function GetJevix() {
		return $this->oJevix;
	}
	/**
	 * Парсинг текста с помощью Jevix
	 *
	 * @param string $sText	Исходный текст
	 * @param array $aError	Возвращает список возникших ошибок
	 * @return string
	 */
	public function JevixParser($sText,&$aError=null) {
		// Если конфиг пустой, то загружаем его
		if (!count($this->oJevix->tagsRules)) {
			$this->LoadJevixConfig();
		}
		$sResult=$this->oJevix->parse($sText,$aError);
		return $sResult;
	}
	/**
	 * Парсинг текста на предмет видео
	 * Находит теги <pre><video></video></pre> и реобразовываетих в видео
	 *
	 * @param string $sText	Исходный текст
	 * @return string
	 */
	public function VideoParser($sText) {
		/**
		 * youtube.com
		 */
		$sText = preg_replace(
		    '/<video>(?:http(?:s|):|)(?:\/\/|)(?:www\.|)youtu(?:\.|)be(?:-nocookie|)(?:\.com|)\/(?:e(?:mbed|)\/|v\/|watch\?(?:.+&|)v=|)([a-zA-Z0-9_\-]+?)(&.+)?<\/video>/Ui',
    		'<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen="true"></iframe>',
		    $sText
		);	
		$sText = preg_replace(
    		'/<video>(?:http(?:s|):|)(?:\/\/|)(?:www\.|)m\.youtu(?:\.|)be(?:-nocookie|)(?:\.com|)\/(?:e(?:mbed|)\/|v\/|watch\?(?:.+&|)v=|)([a-zA-Z0-9_\-]+?)(&.+)?<\/video>/Ui',
		    '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen="true"></iframe>',
    		$sText
		);
		$sText = preg_replace('/<video>(?:http(?:s|):|)\/\/coub\.com\/view\/(.*)<\/video>/Ui', '<iframe src="https://coub.com/embed/$1" allowfullscreen="true" frameborder="0" width="480" height="270"></iframe>', $sText);
		/**
		 * vimeo.com
		 */
		$sText = preg_replace('/<video>http:\/\/(?:www\.|)vimeo\.com\/(\d+).*<\/video>/i', '<iframe src="http://player.vimeo.com/video/$1" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>', $sText);
		/**
		 * rutube.ru
		 */
		$sText = preg_replace('/<video>http:\/\/(?:www\.|)rutube\.ru\/tracks\/(\d+)\.html.*<\/video>/Ui', '<OBJECT width="470" height="353"><PARAM name="movie" value="http://video.rutube.ru/$1"></PARAM><PARAM name="wmode" value="window"></PARAM><PARAM name="allowFullScreen" value="true"></PARAM><EMBED src="http://video.rutube.ru/$1" type="application/x-shockwave-flash" wmode="window" width="470" height="353" allowFullScreen="true" ></EMBED></OBJECT>', $sText);
		/**
		 * video.yandex.ru
		 */
		$sText = preg_replace('/<video>http:\/\/video\.yandex\.ru\/users\/([a-zA-Z0-9_\-]+)\/view\/(\d+).*<\/video>/i', '<object width="467" height="345"><param name="video" value="http://video.yandex.ru/users/$1/view/$2/get-object-by-url/redirect"></param><param name="allowFullScreen" value="true"></param><param name="scale" value="noscale"></param><embed src="http://video.yandex.ru/users/$1/view/$2/get-object-by-url/redirect" type="application/x-shockwave-flash" width="467" height="345" allowFullScreen="true" scale="noscale" ></embed></object>', $sText);
		/**
		 * vk.com
		 */		
		$sText = preg_replace('/<video>http:\/\/vk\.com\/(.*)<\/video>/Ui', '<iframe src="https://vk.com/$1" width="607" height="360" frameborder="0"></iframe>', $sText);
		return $sText;
	}
	/**
	 * Парсит текст, применя все парсеры
	 *
	 * @param string $sText Исходный текст
	 * @return string
	 */
	public function Parser($sText) {
		if (!is_string($sText)) {
			return '';
		}
		$sResult=$this->FlashParamParser($sText);
		$sResult=preg_replace('/<iframe src=\"http:\/\/vk\.com\/(.*)\"(.*)\"><\/iframe>/Ui','<video>http://vk.com/$1</video>',$sResult);
        $sResult=$this->JevixParser($sResult);
		$sResult=$this->VideoParser($sResult);
		$sResult=$this->CodeSourceParser($sResult);
		$sResult=$this->RelativeLinkParser($sResult);
		return $sResult;
	}
	/**
	 * Заменяет все вхождения короткого тега <param/> на длиную версию <param></param>
	 * Заменяет все вхождения короткого тега <embed/> на длиную версию <embed></embed>
	 *
	 * @param string $sText Исходный текст
	 * @return string
	 */
	protected function FlashParamParser($sText) {
		if (preg_match_all("@(<\s*param\s*name\s*=\s*(?:\"|').*(?:\"|')\s*value\s*=\s*(?:\"|').*(?:\"|'))\s*/?\s*>(?!</param>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$str_new=$str.'></param>';
				$sText=str_replace($aMatch[0][$key],$str_new,$sText);
			}
		}
		if (preg_match_all("@(<\s*embed\s*.*)\s*/?\s*>(?!</embed>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$str_new=$str.'></embed>';
				$sText=str_replace($aMatch[0][$key],$str_new,$sText);
			}
		}
		/**
		 * Удаляем все <param name="wmode" value="*"></param>
		 */
		if (preg_match_all("@(<param\s.*name=(?:\"|')wmode(?:\"|').*>\s*</param>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$sText=str_replace($aMatch[0][$key],'',$sText);
			}
		}
		/**
		 * А теперь после <object> добавляем <param name="wmode" value="opaque"></param>
		 * Решение не фантан, но главное работает :)
		 */
		if (preg_match_all("@(<object\s.*>)@Ui",$sText,$aMatch)) {
			foreach ($aMatch[1] as $key => $str) {
				$sText=str_replace($aMatch[0][$key],$aMatch[0][$key].'<param name="wmode" value="opaque"></param>',$sText);
			}
		}
		return $sText;
	}
	/**
	 * Подсветка исходного кода
	 *
	 * @param string $sText Исходный текст
	 * @return mixed
	 */
	public function CodeSourceParser($sText) {
		$sText=str_replace("<code>",'<pre class="prettyprint"><code>',$sText);
		$sText=str_replace("</code>",'</code></pre>',$sText);
		return $sText;
	}
	/**
	 * Производить резрезание текста по тегу cut.
	 * Возвращаем массив вида:
	 * <pre>
	 * array(
	 * 		$sTextShort - текст до тега <cut>
	 * 		$sTextNew   - весь текст за исключением удаленного тега
	 * 		$sTextCut   - именованное значение <cut>
	 * )
	 * </pre>
	 *
	 * @param  string $sText Исходный текст
	 * @return array
	 */
	public function Cut($sText) {
		$sTextShort = $sText;
		$sTextNew   = $sText;
		$sTextCut   = null;

		$sTextTemp=str_replace("\r\n",'[<rn>]',$sText);
		$sTextTemp=str_replace("\n",'[<n>]',$sTextTemp);

		if (preg_match("/^(.*)<cut(.*)>(.*)$/Ui",$sTextTemp,$aMatch)) {
			$aMatch[1]=str_replace('[<rn>]',"\r\n",$aMatch[1]);
			$aMatch[1]=str_replace('[<n>]',"\r\n",$aMatch[1]);
			$aMatch[3]=str_replace('[<rn>]',"\r\n",$aMatch[3]);
			$aMatch[3]=str_replace('[<n>]',"\r\n",$aMatch[3]);
			$sTextShort=$aMatch[1];
			$sTextNew=$aMatch[1].' <a name="cut"></a> '.$aMatch[3];
			if (preg_match('/^\s*name\s*=\s*"(.+)"\s*\/?$/Ui',$aMatch[2],$aMatchCut)) {
				$sTextCut=trim($aMatchCut[1]);
			}
		}

		return array($sTextShort,$sTextNew,$sTextCut ? htmlspecialchars($sTextCut) : null);
	}
	/**
	 * Обработка тега ls в тексте
	 * <pre>
	 * <ls user="admin" />
	 * </pre>
	 *
	 * @param string $sTag	Тег на ктором сработал колбэк
	 * @param array $aParams Список параметров тега
	 * @return string
	 */
	public function CallbackTagLs($sTag,$aParams) {
		$sText='';
		if (isset($aParams['user'])) {
			if ($oUser=$this->User_getUserByLogin($aParams['user'])) {
				$sText.="<a href=\"{$oUser->getUserWebPath()}\" class=\"ls-user\">{$oUser->getLogin()}</a> ";
			}
		}
		return $sText;
	}

    public function RelativeLinkParser(string $sText) : string {
        $sText = str_replace("href=\"".Config::Get("path.root.web"),"href=\"", $sText);
        $sText = str_replace("href='".Config::Get("path.root.web"),"href='", $sText);

        return $sText;
	}

	public $aRepl = [
        "*" => "&#42;",
        "(" => "&#40;",
        ")" => "&#41;",
        "[" => "&#91;",
        "]" => "&#93;",
        "~" => "&#126;",
        "`" => "&#96;",
		"<" => "&#60;",
		">" => "&#62;",
		" " => "&nbsp;",
		"_" => "&#95;"
    ];

	public function Escape($sText) {
		return str_replace(array_keys($this->aRepl), array_values($this->aRepl), $sText);
	}

	public function Mark($sText) {
        $sText = preg_replace_callback('/\`\`\`([.\s\S]*?)\`\`\`/',
            function ($matches) {
                return "<code>" . $this->Escape($matches[1]) . "</code>";
            }, $sText);

		$sText = preg_replace_callback('/\`\`([.\s\S]*?)\`\`/',
			function ($matches) {
				return "<blockquote>" . $matches[1] . "</blockquote>";
			}, $sText);

        $sText = preg_replace_callback('/\`([.\s\S]*?)\`/',
            function ($matches) {
                return "<code class='inline'>" . $this->Escape($matches[1]) . "</code>";
            }, $sText);

        $sText = preg_replace_callback('/[\\\](.)/',
            function ($matches) {
                return $this->Escape($matches[1]);
            }, $sText);

        $sText = preg_replace_callback('/\[\[([.\s\S]*?)\]\]/',
            function ($matches) {
                return "<span class=\"spoiler-gray\">" . $matches[1] . "</span>";
            }, $sText);

        $sText = preg_replace_callback('/\(([.\s\S]*?)\)\[([.\s\S]*?)\]/',
            function ($matches) {
                return "<a target='_blank' href='".$matches[1]."'>" . $matches[2] . "</a>";
            }, $sText);

        $sText = preg_replace_callback('/\*\*([.\s\S]*?)\*\*/',
            function ($matches) {
                return "<b>" . $matches[1] . "</b>";
            }, $sText);

        $sText = preg_replace_callback('/\*([.\s\S]*?)\*/',
            function ($matches) {
                return "<em>" . $matches[1] . "</em>";
            }, $sText);

        $sText = preg_replace_callback('/\~\~([.\s\S]*?)\~\~/',
            function ($matches) {
                return "<s>" . $matches[1] . "</s>";
            }, $sText);

        $sText = preg_replace_callback('/\_\_([.\s\S]*?)\_\_/',
            function ($matches) {
                return "<u>" . $matches[1] . "</u>";
            }, $sText);


        return $sText;
	}

	public function CommentParser($oComment, $bDice = true) {
        if ($oComment->getTargetType()=="topic") {
        	$oTarget = $this->Topic_GetTopicById($oComment->getTargetId());
		} else {
            $oTarget = $this->Talk_GetTalkById($oComment->getTargetId());
		}

        $html = str_get_html($oComment->getText(), true, true, 'UTF-8', false);
        foreach($html->find('.spoiler-body img') as $element) {
            $element->attr['data-src'] = $element->src;
            $element->src = "";
        }
        foreach($html->find('.spoiler-body iframe') as $element) {
            $element->attr['data-src'] = $element->src;
            $element->src = "";
        }

        $sText = $html->save();

		if ($bDice) {
            $sText = preg_replace_callback('/\[(\d*)d(\d*)\]/',
                function ($matches) {
                    $i = (int)$matches[1];
                    $d = (int)$matches[2];
                    $r = "<span class='dice_result'>" . $matches[0] . ": ";
                    for ($y = 0; $y < $i; $y++) {
                        $r = $r . rand(1, $d) . ", ";
                    }
                    $r = substr($r, 0, -2);
                    $r = $r . "</span>";
                    return $r;
                }, $sText);
        }

        $sText = str_replace('href="'.$oTarget->getUrl(),"href=\"", $sText);
        $sText = str_replace("href='".$oTarget->getUrl(),"href='", $sText);

        return $sText;
	}
}
?>
