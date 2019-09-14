<?php
/**
 * 클래스의 메소드를 외부에서 접근 가능하게 (public) 만듭니다.
 *
 * Reflection 클래스를 리턴 받는데, 아래 예처럼 사용할 수 있습니다.
 *
 * $foo           = new FooClass();
 * $fooReflection = makeMethodAccessible('FooClass', 'bar'); // private, or protected method FooClass::bar()
 * $fooReflection->invoke($foo);                             // public 처럼 호출 가능함.
 *
 * @param string $className  클래스 FQCN
 * @param string $methodName 클래스의 메소드 이름
 *
 * @return ReflectionMethod|null 에러인 경우 null, 성공하면 ReflectionMethod 리턴.
 */
function makeMethodAccessible(string $className, string $methodName)
{
    try {
        $reflection = new ReflectionClass($className);
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);
    } catch (Exception $e) {
        return null;
    }

    return $method;
}

/**
 * 클래스의 속성을 외부에서 접근 가능하게 (public) 만듭니다.
 *
 * Reflection 클래스를 리턴 받는데, 아래 예처럼 사용할 수 있습니다.
 *
 * $foo           = new FooClass();
 * $fooReflection = makePropertyAccessible('FooClass', 'baz' );
 * $baz           = $fooReflection->getValue($foo);
 *
 * @param string $className 클래스 FQCN
 * @param string $propertyName 클래스의 속성 이름
 *
 * @return ReflectionProperty|null 에러인 경우 null, 성공하면 ReflectionMethod 리턴.
 */
function makePropertyAccessible(string $className, string $propertyName)
{
    try {
        $reflection = new ReflectionClass($className);
        $property   = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
    } catch (Exception $e) {
        return null;
    }

    return $property;
}
