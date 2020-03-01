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
