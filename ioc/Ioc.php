<?php
class Ioc {
    /**
     * @var ע�����������
     */

    protected static $registry = array();

    /**
     * ���һ��resolve��registry������
     * @param  string $name ������ʶ
     * @param callable|object $resolve һ������������������ʵ��
     */
    public static function register($name, Closure $resolve)
    {
        static::$registry[$name] = $resolve;
    }

    /**
     * ����һ��ʵ��
     * @param  string $name �����ı�ʶ
     * @return mixed
     * @throws Exception
     */
    public static function resolve($name)
    {
        if ( static::registered($name) )
        {
            $name = static::$registry[$name];
            return $name();
        }
        throw new Exception('Nothing registered with that name, fool.');
    }
    /**
     * ��ѯĳ������ʵ���Ƿ����
     * @param  string $name id
     * @return bool
     */
    public static function registered($name)
    {
        return array_key_exists($name, static::$registry);
    }
}
