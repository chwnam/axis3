<?php

namespace Shoplic\Axis3\Functions;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * 시간을 워드프레스에서 지정한 날짜 형식에 맞춰 출력한다.
 *
 * @param DateTIme|string|int $datetime getDateTime() 첫번째 인자로 쓰인다.
 * @param string              $field    출력할 필드를 지정한다.
 *                                      - both:      날짜 시간 순으로 출력. 기본값.
 *                                      - date time: 'both' 와 동일.
 *                                      - time date: 시간 날짜 순으로 출력.
 *                                      - date:      날짜만 출력.
 *                                      - time:      시간만 출력.
 *                                      - 그 외:     날짜를 이 형식에 맞춰 출력한다.
 * @param string              $glue     날짜와 시간을 같이 출력할 때 둘을 구분하는 문자. 기본은 스페이스.
 *
 * @return string 형식화된 문자열.
 *
 * @link https://wordpress.org/support/article/formatting-date-and-time/ 형식 문자열에 관한 참조
 */
function datetimeI18n($datetime = 'now', $field = 'both', $glue = ' ')
{
    $datetime = getDatetime($datetime);

    if ($datetime) {
        switch ($field) {
            case 'both':
            case 'date time':
                $format = get_option('date_format') . $glue . get_option('time_format');
                break;

            case 'time date':
                $format = get_option('time_format') . $glue . get_option('date_format');
                break;

            case 'date':
                $format = get_option('date_format');
                break;

            case 'time':
                $format = get_option('time_format');
                break;

            default:
                $format = $field;
                break;
        }

        return date_i18n($format, $datetime->getTimestamp() + $datetime->getOffset());
    }

    return '';
}


/**
 * DateTime 객체를 얻어낸다.
 *
 * @param DateTime|string|int      $input       DateTime 생성자 첫번째 인자로 사용한다. 인자 형식은 링크를 참조.
 *                                              이 인자로 null, true, false, 빈 문자열 입력시 false 를 리턴한다.
 * @param DateTimeZone|string|null $timezone    시간대. 객체, 문자열로 지정할 수 있고 생략하면 워드프레스 환경에서 최대한 추측한다.
 * @param string|null              $inputFormat 특정한 인자 형식에 기반해 해석하려면 그 형식을 지정할 수 있다.
 *
 * @return DateTime|false
 * @see    getTimezone()
 * @link   https://www.php.net/manual/en/datetime.formats.php          가능한 인자 형식을 참조
 * @link   https://www.php.net/manual/en/datetime.createfromformat.php $inputFormat 형식을 참조
 */
function getDatetime($input = 'now', $timezone = null, $inputFormat = null)
{
    $output = false;

    if (!$timezone) {
        $timezone = getTimezone();
    } else {
        try {
            if ($timezone instanceof DateTimeZone) {
                $timezone = clone $timezone;
            } else {
                $timezone = new DateTimeZone($timezone);
            }
        } catch (Exception $e) {
            $timezone = getTimezone();
        }
    }

    try {
        if ($input instanceof DateTime) {
            $output = clone $input;
            $output->setTimezone($timezone);
        } elseif ($input instanceof DateTimeImmutable) {
            $output = DateTime::createFromImmutable($input);
        } elseif (is_numeric($input)) {
            // is_numeric() 함수는 true, false, 빈 문자열, null 에 대해 false 값을 리턴.
            $timestamp = intval($input);
            if (false !== $timestamp) {
                $output = new DateTime("@{$timestamp}", new DateTimeZone('UTC'));
                if ($output) {
                    $output->setTimezone($timezone);
                }
            } else {
                throw new Exception('Invalid input timestamp: ' . $input);
            }
        } elseif ($input) {
            // true 는 여기서 예외를 일으켜 false 리턴값을 내게 됨.
            if ($inputFormat) {
                $output = createFromFormat($inputFormat, $input, $timezone);
            } else {
                $output = new DateTime($input, $timezone);
            }
        }
        // false, null, empty string 은 어떤 if, elseif 안으로 들어가지않음.
    } catch (Exception $e) {
        $output = false;
    }

    return $output;
}


/**
 * 시간대를 가져온다. 한 번 알아낸 시간대는 계속 재활용한다.
 *
 * 워드프레스 설정에서 선택한 시간대가 올바르면 그것을 기반으로 DateTimeZone 객체를 만든다.
 * 제대로 설정되어 있지 않고 '3.5' 같은 오프셋으로 구성되어 있다면 오프셋으로부터 시간대를 적당히 추측해낸다.
 * 만약 이것마저도 제대로 되어 있지 않다면 기본인 UTC 시간대로 설정한 DateTimeZone 객체를 생성한다.
 *
 * @param bool $forceRefresh 시간대를 강제로 갱신하려면 true 를 준다.
 *
 * @return DateTimeZone
 */
function getTimezone($forceRefresh = false)
{
    static $cached = null;

    if (is_null($cached) || $forceRefresh) {
        try {
            $cached = new DateTimeZone(get_option('timezone_string', false));
        } catch (Exception $e) {
            $timezone = '';
            $offset   = intval(get_option('gmt_offset') * HOUR_IN_SECONDS);

            if ($offset) {
                $timezone = timezone_name_from_abbr('', $offset);
                if (!$timezone) {
                    /** @link https://bugs.php.net/bug.php?id=44780 timezone_name_from_abbr bug fix */
                    foreach (timezone_abbreviations_list() as $cities) {
                        foreach ($cities as $city) {
                            if ($city['offset'] == $offset) {
                                $timezone = $city['timezone_id'];
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!$timezone) {
                $timezone = 'UTC';
            }

            $cached = new DateTimeZone($timezone);
        }
    }

    return $cached;
}


function createFromFormat(string $format, string $input, DateTimeZone $timezone)
{
    global $wp_locale;

    $format = preg_replace('/(?<!\\\\)c/', DATE_W3C, $format);
    $format = preg_replace('/(?<!\\\\)r/', DATE_RFC2822, $format);

    if (!empty($wp_locale->month) && !empty($wp_locale->weekday)) {
        $expressions = [
            'weekday_abbrev' => 'D',
            'month'          => 'F',
            'weekday'        => 'l',
            'month_abbrev'   => 'M',
            'merdiem'        => '[aA]',
        ];

        foreach ($expressions as $property => $regex) {
            if (isset($wp_locale->{$property}) && is_array($wp_locale->{$property})) {
                if (preg_match('/' . implode('|', $wp_locale->{$property}) . '/', $input, $match)) {
                    $format = preg_replace("/([^\\\]){$regex}/", '\\1' . backslashit($match[0] ?? ''), $format);
                }
            }
        }
    }

    return DateTime::createFromFormat($format, $input, $timezone);
}