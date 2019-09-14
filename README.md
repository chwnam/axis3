# Axis 3

고도화된 워드프레스 플러그인, 테마 개발을 지원하는 워드프레스 MU (Must-Use) 플러그인 기반의 프레임워크.

## 설치
Axis 3는 PSR-1, 2, 4 에 의해 작성되었습니다. 
먼저 [Composer](https://getcomposer.org/download/)를 다운로드하여 설치하세요.

1. 워드프레스의 `wp-content/mu-plugins` 디렉토리가 있는지 확인해 주세요. 없으면 생성해 주세요.
2. `git clone https://github.com/chwnam/axis3.git` 명령으로 코드를 받습니다.
3. `composer dump-autoload` 명령을 사용하여 오토로더를 만들어 주세요. 
4. `wp-content/mu-plugins/axis3-loader.php` 스크립트를 아래 예제처럼 작성해 주세요.
5. 워드프레스 관리자 > 플러그인 > must-use 항목에 'axis3-loader'가 있는지 확인하세요.

### 로더 코드 예제
```php
<?php require_once __DIR__ . '/axis3/axis3.php';
```
