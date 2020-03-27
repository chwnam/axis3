<?php
/**
 * 유틸리티 함수 모음
 *
 * @package Shoplic\Axis3\Functions;
 */

namespace Shoplic\Axis3\Functions;

/**
 * 클래스가 인터페이스를 구현했는지 검사
 *
 * @param string $className     확인할 클래스 이름
 * @param string $interfaceName 클래스가 구현하는 인터페이스
 *
 * @return bool 클래스가 인터페이스를 구현하면 참을 리턴, 아니면 거짓을 리턴한다.
 * @since 1.0.0
 *
 */
function classImplements(string $className, string $interfaceName): bool
{
    return
        class_exists($className) &&
        ($interfaces = class_implements($className)) &&
        isset($interfaces[$interfaceName]);
}


/**
 * 스크립트 URL 필터를 합니다.
 *
 * 'bar.min.js' 같은 스크립트가 있다면 'bar.js' 를 대신 불러오도록 처리한다.
 * 이렇게 되려면 SCRIPT_DEBUG 상수가 true 로 설정되어 있어야 합니다.
 *
 * @param string $minUrl minified 된 애셋 URL.
 *
 * @return string un-minified 처리된 애셋 URL.
 */
function filterScriptUrl(string $minUrl): string
{
    if (
        (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) &&
        preg_match('/^(.+)\.min(\.[A-Za-z0-9-_]+)$/', $minUrl, $matches)
    ) {
        return $matches[1] . $matches[2];
    } else {
        return $minUrl;
    }
}


/**
 * 쿼리 파트를 제거한 URL 경로를 리턴한다.
 *
 * @param null|string $url 제거할 입력 URL. NULL 이면 현재 서버 환경변수에서 가져온다.
 *
 * @return string
 */
function getCleanUrlPath($url = null): string
{
    if (!$url) {
        $url = $_SERVER['REQUEST_URI'] ?? '';
    }

    return trim(strtok($url, '?'));
}


/**
 * flush_rewrite_rules() 함수 실행을 주도한다. 단 한 번 부른다.
 *
 * @see   flush_rewrite_rules()
 * @see   requestFlushRewrite()
 * @since 1.0.0
 */
function _flushRewriteHard()
{
    static $flag = false;
    if (!$flag && doing_action('shutdown')) {
        $flag = true;
        flush_rewrite_rules(true);
    }
}

/**
 * flush_rewrite_rules() 함수 호출을 예약한다. shutdown 액션에 단 한 번 실행할 수 있도록 한다.
 *
 * @see   flush_rewrite_rules()
 * @see   _flushRewriteHard()
 * @since 1.0.0
 */
function requestFlushRewrite()
{
    if (!has_action('shutdown', 'Shoplic\Axis3\Functions\_flushRewriteHard')) {
        add_action('shutdown', 'Shoplic\Axis3\Functions\_flushRewriteHard');
    }
}


/**
 * 디렉토리를 재귀적으로 삭제.
 *
 * @param string $directory 입력 디렉토리.
 *
 * @since 1.0.0
 */
function rmdirRecursive(string $directory)
{
    if (is_file($directory)) {
        unlink($directory);
    } else {
        $dirIterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator    = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $it) {
            if ($it->isDir()) {
                rmdir($it->getRealPath());
            } else {
                unlink($it->getRealPath());
            }
        }
        rmdir($directory);
    }
}


/**
 * 주어진 문자열로 시작하는지 검사
 *
 * @param string $haystack 검사할 문자열
 * @param string $needle   시작하는 문자열
 *
 * @return bool
 */
function strStartsWith(string $haystack, string $needle): bool
{
    return $needle === '' || strpos($haystack, $needle) === 0;
}


/**
 * 주어진 문자열로 끝나는지 검사
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function strEndsWith(string $haystack, string $needle): bool
{
    return
        $needle === '' ||
        (
            (($h = strlen($haystack)) >= ($n = strlen($needle))) &&
            substr($haystack, $h - $n) === $needle
        );
}


/**
 * 문자열 표기법을 스네이크 케이스로 변경
 *
 * thisIsASnakeCasedSentence ==> this_is_a_snake_cased_sentence
 *
 * @param string $string 입력 단어.
 * @param string $glue   띄어쓰기 문자. 기본은 언더바 '_'
 *
 * @return string
 */
function toSnakeCase(string $string, string $glue = '_'): string
{
    return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $glue, $string));
}


/**
 * 문자열 표기법을 파스칼 케이스로 변경.
 *
 * this_is_a_pascal_cased_sentence ==> ThisIsAPascalCasedSentence
 *
 * @param string $string 입력 단어.
 * @param string $glue   띄어쓰기 문자. 기본은 언더바 '_'
 *
 * @return string
 */
function toPascalCase(string $string, string $glue = '_'): string
{
    return str_replace($glue, '', ucwords($string, $glue));
}


/**
 * 문자열 표기법을 카멜 표기법으로 변경.
 *
 * this_is_a_camel_cased_sentence ==> thisIsACamelCasedSentence
 *
 * @param string $string 입력 단어.
 * @param string $glue   띄어쓰기 문자. 기본은 언더바 '_'
 *
 * @return string
 */
function toCamelCase(string $string, string $glue = '_'): string
{
    return lcfirst(toPascalCase($string, $glue));
}


/**
 * 콜백을 일시적으로 해제하고 작업을 진행한다.
 *
 * @param string   $tag          액션, 필터의 태그.
 * @param callable $callback     잠시 해제할 액션, 필터로 등록된 콜백.
 * @param callable $task         해제시킨 동안 실행할 작업.
 * @param array    $taskArgs     함수로 전달할 인자들.
 * @param bool     $isFilter     필터이면 true, 액션이먄 false
 * @param int      $acceptedArgs 액션, 필터 등록시 허용 인자 수
 *
 * @return null|mixed 액션이면 null, 필터이면 $task 호출의 결과를 리턴.
 */
function callbackFreeTask(
    string $tag,
    callable $callback,
    callable $task,
    array $taskArgs = [],
    bool $isFilter = false,
    int $acceptedArgs = 1
) {
    $priority = $isFilter ? has_filter($tag, $callback) : has_action($tag, $callback);
    $output   = null;

    if ($priority) {
        if ($isFilter) {
            remove_filter($tag, $callback, $priority);
        } else {
            remove_action($tag, $callback, $priority);
        }
    }

    $output = call_user_func_array($task, $taskArgs);

    if ($priority) {
        if ($isFilter) {
            add_filter($tag, $callback, $priority, $acceptedArgs);
        } else {
            add_action($tag, $callback, $priority, $acceptedArgs);
        }
    }

    return $output;
}


/**
 * 경로에서 SVG 아이콘 이미지를 읽어 이미지를 리턴한다.
 *
 * @param string $path 이미지 경로
 *
 * @return string 리소스 정보. 헤더 정보를 자동으로 붙여준다.
 */
function getSvgIconUrl(string $path): string
{
    if (is_file($path) && is_readable($path)) {
        $content = file_get_contents($path);
    } else {
        $content = '';
    }

    return 'data:image/svg+xml;base64,' . base64_encode($content);
}


/**
 * 배열을 두 부분으로 자른다. 잘려진 세 부분은 아래와 같다.
 * - $key 를 기준으로 $key 전의 배열.
 * - $key 부터 $length 나머지 부분
 *
 * @param array $input        입력.
 * @param mixed $key          배열 내 기준 키.
 * @param bool  $keyToLastOne true 를 입력하면 기준 키는 인덱스 1 로 간다.
 *                            false 를 입력하면 기준 키는 인덱스 0 로 간다.
 *
 * @return array 두 부분을 나눈 결과. 항상 길이 2인 배열이 리턴된다.
 *               기준 키는 두 입력 중 인덱스 1에 붙는다.
 */
function splitArray(array $input, $key, $keyToLastOne = true): array
{
    $output = [[], []];

    $pos = array_search($key, array_keys($input), true);
    if (false !== $pos) {
        if ($keyToLastOne) {
            $output[0] = array_slice($input, 0, $pos);
            $output[1] = array_slice($input, $pos);
        } else {
            $output[0] = array_slice($input, 0, $pos + 1);
            $output[1] = array_slice($input, $pos + 1);
        }
    } else {
        $output[0] = $input;
    }

    return $output;
}


/**
 * 키-값 배열의 중간에 다른 배열을 삽입한다.
 *
 * @param array $input 입력할 배열.
 * @param mixed $key   입력 배열에서 찾을 키. 이 키를 기준으로 배열을 나눈다.
 * @param array $mixin 끼워 넣을 배열.
 *
 * @return array 접합된 배열 결과. 끼워넣은 배열 다음에 기준 키가 발견될 것이다.
 *
 * @uses \Shoplic\Axis3\Functions\splitArray()
 */
function mixinArray(array $input, $key, array $mixin): array
{
    list($left, $right) = splitArray($input, $key);

    return array_merge($left, $mixin, $right);
}


/**
 * 주어진 입력이 배열이면 지정한 인덱스를 리턴한다.
 *
 * 배열 중 단 하나의 요소만 끄집어낼 때 유용하다.
 *
 * @param mixed|array $maybeArray  입력. Array 에만 동작하며, 아닌 것이 들어요면 null 리턴.
 * @param int         $index       추출할 인덱스. 기본 0.
 * @param string|null $elementType 추출한 배열 요쇼의 타입 체크한다. null 이면 체크하지 않는다.
 *                                 체크했을 때 입력한 타입과 일치하지 않으면 리턴은 null.
 *
 * @return mixed|null
 */
function fetchElement($maybeArray, $index = 0, $elementType = null)
{
    $output = null;

    if (is_array($maybeArray) && $index < count($maybeArray)) {
        if (is_null($elementType) || $maybeArray[$index] instanceof $elementType) {
            $output = $maybeArray[$index];
        }
    }

    return $output;
}


/**
 * 한 번에 많은 권한을 역할에게 지정.
 *
 * @param string|array $roles 역할 이름.
 * @param array        $caps  권한 목록.
 * @param bool         $grant 부여/박탈 플래그. true 이면 부여, false 면 박탈.
 */
function addCapsToRoles($roles, $caps, $grant = true)
{
    global $wpdb, $wp_roles;

    if (!$wp_roles) {
        return;
    }

    $rolesData = &$wp_roles->roles;

    foreach ((array)$roles as $role) {
        if (!isset($rolesData[$role]['capabilities'])) {
            continue;
        }

        $capabilities = &$rolesData[$role]['capabilities'];

        if ($grant) {
            foreach (array_unique(array_filter((array)$caps)) as $cap) {
                if (!isset($capabilities[$cap]) || !$capabilities[$cap]) {
                    $capabilities[$cap] = true;
                }
            }
        } else {
            foreach (array_unique(array_filter((array)$caps)) as $cap) {
                if (isset($capabilities[$cap])) {
                    unset($capabilities[$cap]);
                }
            }
        }
    }

    update_option($wpdb->get_blog_prefix(get_current_blog_id()) . 'user_roles', $rolesData);
}


/**
 * array_key_first() 의 대용품.
 *
 * @param array $array
 *
 * @return int|string|null
 */
function arrayKeyFirst(array &$array)
{
    reset($array);
    return key($array);
}


/**
 * array_key_last() 의 대용품.
 *
 * @param array $array
 *
 * @return int|string|null
 */
function arrayKeyLast(array &$array)
{
    end($array);
    return key($array);
}


/**
 * 입력된 문자열을 배열로 만든다.
 *
 * @param string $text         입력 문자열.
 * @param int    $split_length 뭉칠 글자 개수.
 *
 * @return string[]
 */
function strSplit($text, $split_length = 1)
{
    return preg_split('/(.{' . $split_length . '})/su', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
}


/**
 * 한글 자소 분리를 한다.
 *
 * @param string $input 입력 문자열. UTF-8.
 *
 * @return array 분리된 자소.
 *               각 원소는 길이 3인 배열, 혹은 문자열.
 *               한글의 경우 초성, 중성, 종성대로 분리된 배열. 종성이 없다면 공백으로 메꾼다.
 *               만약 단모음(ㄱ, ㄴ, ㄷ, ...), 단자음(ㅏ, ㅑ, ㅓ, ...)으로만 된 글자였다면
 *               마찬가지로 길이 3인 배열인데, 가장 첫번째에 그 글자가 나오고 두 개의 공백이 이어져 항상 길이 3을 맞춘다.
 *               한글이 아니면 그냥 문자열.
 */
function decomposeHangul(string $input)
{
    $output = [];

    $hl = [
        'ㄱ',
        'ㄲ',
        'ㄴ',
        'ㄷ',
        'ㄸ',
        'ㄹ',
        'ㅁ',
        'ㅂ',
        'ㅃ',
        'ㅅ',
        'ㅆ',
        'ㅇ',
        'ㅈ',
        'ㅉ',
        'ㅊ',
        'ㅋ',
        'ㅌ',
        'ㅍ',
        'ㅎ',
    ];

    $vl = [
        'ㅏ',
        'ㅐ',
        'ㅑ',
        'ㅒ',
        'ㅓ',
        'ㅔ',
        'ㅕ',
        'ㅖ',
        'ㅗ',
        'ㅘ',
        'ㅙ',
        'ㅚ',
        'ㅛ',
        'ㅜ',
        'ㅝ',
        'ㅞ',
        'ㅟ',
        'ㅠ',
        'ㅡ',
        'ㅢ',
        'ㅣ',
    ];

    $tl = [
        '',
        'ㄱ',
        'ㄲ',
        'ㄳ',
        'ㄴ',
        'ㄵ',
        'ㄶ',
        'ㄷ',
        'ㄹ',
        'ㄺ',
        'ㄻ',
        'ㄼ',
        'ㄽ',
        'ㄾ',
        'ㄿ',
        'ㅀ',
        'ㅁ',
        'ㅂ',
        'ㅄ',
        'ㅅ',
        'ㅆ',
        'ㅇ',
        'ㅈ',
        'ㅊ',
        'ㅋ',
        'ㅌ',
        'ㅍ',
        'ㅎ',
    ];

    foreach (strSplit($input) as $chr) {
        $code = mb_ord($chr, 'UTF-8');
        if (44032 <= $code && $code <= 55203) {
            $t        = $code - 44032;
            $hi       = (int)($t / 588);
            $vi       = (int)(($t % 588) / 28);
            $ti       = (int)($t % 28);
            $output[] = [$hl[$hi], $vl[$vi], $tl[$ti]];
        } elseif (12593 <= $code && $code <= 12643) {
            $output[] = [$chr, '', ''];
        } else {
            $output[] = $chr;
        }
    }

    return $output;
}


/**
 * 단어에 따라 알맞는 조사 처리를 한다.
 *
 * @param string $input 입력 문장. 마지막 글자를 보고 판단한다.
 *                      마지막 글자가 알파벳인 경우라면 알파벳의 발음에서 추측한다. 'a'라면 '에이', 'm'이면 '엠' 처럼.
 *                      그러므로 영어로 입력되었을 때는 완벽하게 조사가 처리되지 않을 수 있다.
 *                      마지막 글자가 숫자인 경우라면 영, 일, 이, 삼, ... , 구라는 발음에서 추측한다.
 *                      마지막 글자가 단자음이라면 항상 받침이 있을 때, 단모음이면 받침이 없을때로 해석한다.
 * @param string $a     받침이 있을 때. 은, 을, 이.
 * @param string $b     받침이 없을 때. 는, 를, 가.
 *
 * @return string 단어와 조사를 합친 문자열. 만약 공백이 입력된다면 공백이 출력.
 *
 */
function josa(string $input, string $a, string $b): string
{
    $output = '';
    $split  = strSplit($input);
    $last   = $split[count($split) - 1];

    if (is_numeric($last)) {
        if (in_array($last, ['0', '1', '3', '6', '7', '8'])) {
            $output = $input . $a;
        } else {
            $output = $input . $b;
        }
    } elseif (preg_match('/[A-Z]/i', $last)) {
        if (in_array(strtolower($last), ['l', 'm', 'n', 'r'])) {
            $output = $input . $a;
        } else {
            $output = $input . $b;
        }
    } else {
        $s = decomposeHangul($last);
        if (3 === count($s[0])) {
            if (!$s[0][1] && !$s[0][2]) {
                // 단모음 단자음.
                $code = mb_ord($last, 'UTF-8');
                if (12593 <= $code && $code <= 12622) {
                    // 단모음
                    $output = $input . $b;
                } elseif (12623 <= $code && $code <= 12643) {
                    // 단자음
                    $output = $input . $a;
                }
            } elseif ($s[0][1] && !$s[0][2]) {
                // 받침 없음.
                $output = $input . $b;
            } else {
                // 바침 있음.
                $output = $input . $a;
            }
        } else {
            // whitespace or unknown case....
            $output = $input;
        }
    }

    return $output;
}


/**
 * 조사 함수 래핑. 은/는 구별.
 *
 * @param string $input
 *
 * @return string
 */
function josaEunNun(string $input): string
{
    return josa($input, '은', '는');
}


/**
 * 조사 함수 래핑. 을/를 구별.
 *
 * @param string $input
 *
 * @return string
 */
function josaEulLul(string $input): string
{
    return josa($input, '을', '를');
}


/**
 * 조사함수 래핑. 이/가 구별.
 *
 * @param string $input
 *
 * @return string
 */
function josaYiGa(string $input): string
{
    return josa($input, '이', '가');
}


/**
 * 문자열, 혹은 문자열의 배열을 입력받아 필터링 처리한다.
 *
 * 1. 입력값이 문자열이라면 주어진 토큰 기반으로 부숴뜨려 배열로 만든다. 입력값이 배열이면 이 과정은 생략.
 * 2. 배열에서 트리밍 처리를 시행. 기본은 문자열의 앞뒤로 붙은 공백을 제거하는 trim 함수 사용.
 * 3. 공백인 문자열 제거. 즉, array_filter() 적용.
 * 4. 중복된 문자열 제거. 즉, array_unique() 적용.
 * 5. 선택적으로 분리된 문자열을 다시 합칠 수 있음.
 *
 * @param array|string    $array   입력.
 * @param string          $token   구분자. 입력이 문자열 형태면 이 문자를 기준으로 explode() 함수 적용.
 * @param string|callable $trimmer 분리된 문자열에 대해 트리밍 처리할 함수. 기본 trim.
 * @param bool|callable   $filter  array_filter() 적용 여부. 기본 true. 호출 가능한 함수시 해당 콜백으로 호출.
 * @param bool|int        $unique  array_unique() 적용 여부. 기본 true. 정수로 입력시 sort_flags 로 인식한다.
 *                                 주의. array_unique() 를 명시적으로 적용시키고 싶지 않으면 'false'를 입력해야 한다.
 *                                 그렇지 않으면 SORT_REGULAR 상수값인 0과 혼동되어 원하는 대로 되지 않는다.
 * @param bool            $implode 마지막에 implode() 함수를 적용하여 문자열로 리턴시킬지 지정. 기본 true.
 *
 * @return array|string 배열 혹은 문자열. 적절하지 못한 입력값, 호출 불가능한 $trimmer 입력시에는
 *                      implode 여부에 따라 빈 배열, 혹은 공백을 리턴하게 된다.
 *
 */
function filterStringList($array, $token = "\r\n", $trimmer = 'trim', $filter = true, $unique = true, $implode = true)
{
    if (is_string($array)) {
        $array = explode($token, $array);
    }

    if (!is_array($array) || !is_callable($trimmer)) {
        return $implode ? '' : [];
    }

    $array = array_map($trimmer, $array);

    if ($filter) {
        $array = array_filter($array, is_callable($filter) ? $filter : null);
    }

    if (false !== $unique) {
        $array = array_unique($array, true === $unique ? SORT_STRING : $unique);
    }

    return $implode ? implode($token, $array) : $array;
}