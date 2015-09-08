<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/8
 * Time: 9:27
 */
class tpl_parser
{
    private $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    public function __construct($content)
    {
        $this->content = $content;
    }

    public function echo_parser()
    {
        $this->content = preg_replace("/\\[print\\s+(.*?)\\]/", "<?php=\\1?>", $this->content);
        return $this;
    }

    public function for_parser()
    {
        $new_content = preg_replace("/\\[for_start\\s+(.*?)\\s+(.*?)\\s+(.*?)\\s*\\](.*)\\[for_end\\]/", '<?php foreach(\\1 as \\2 => \\3) {?>\\4<?php}?>', $this->content);
        if ($new_content != $this->content) {
            $this->content = $new_content;
            $this->for_parser();
        }
        return $this;
    }

    public function while_parser()
    {
        $this->content = preg_replace("/\\[while_start\\s+condition\\((.*?)\\)\\s*\\]expr\\((.*?)\\)\\[while_end\\]/", "<?php while(\\1){?>\\2<?php}?>", $this->content);
        return $this;
    }

    public function do_while_parser()
    {
        $this->content = preg_replace("/\\[do_while_start\\s+condition\\((.*?)\\)\\s+expr\\((.*?)\\)\\s*\\]\\[do_while_end\\]/", '<?php do{?>\\1<?php}while(\\2)?>', $this->content);
        return $this;

    }

    public function if_parser()
    {
        $this->content = preg_replace("/\\[if_start\\s+condition\\((.*?)\\)\\s+expr\\((.*?)\\)\\s*]\\[if_end\\]/", '<?php if(\\1){?>\\2<?php}?>', $this->content);
        return $this;
    }

    public function elseif_parser()
    {
        $this->content = preg_replace("/\\[elseif_start\\s+condition\\((.*?)\\)\\s+expr\\((.*?)\\)\\s*]\\[elseif_end\\]/", '<?php elseif(\\1){?>\\2<?php}?>', $this->content);
        return $this;
    }

    public function  else_parser()
    {
        $this->content = preg_replace("/\\[else_start\\]expr\\((.*?)\\)\\[else_end\\]/", "<?php else{?>\\1<?php}?>", $this->content);
        return $this;
    }


    public function remove_php_tags()
    {
        $this->content = preg_replace('/(?<!=)\\?>\s*<\\?php(?!=)/', '', $this->content);
        $this->content = preg_replace("/\\?>\\s*<\\?php=(.*?)\\}/", 'echo \\1;}', $this->content);
        $this->content = preg_replace("/\\?>\\s*<\\?php=(.*)\\?><\\?php/", 'echo \\1;', $this->content);
        return $this;
    }

    public function display()
    {
        return $this->content;
    }
}

$parser = new tpl_parser('<?php $arr=[[1,2,3],[4,5,6]];?>[for_start $arr $key $value][for_start $value $key2 $value2][print $value2][for_end][for_end]');
//echo $parser->for_parser()->var_parser()->remove_php_tags()->display();
/*
$parser->setContent('<?php $arr=[1,2,3,4];?>  [for_start $arr $value $key][print $key][for_end]');
*/
/*
$parser->setContent('<?php $a="hello,world";?>[while_start condition(true)]expr([print $a])[while_end]');
*/
/*
$parser->setContent('<?php $a="hello,world";?>[while_start condition(false)]expr([print $a])[while_end]<?php $arr=[[1,2,3],[4,5,6]];?>[do_while_start condition([for_start $arr $key $value][for_start $value $key2 $value2][print $value2][for_end][for_end]) expr(false)][do_while_end]');
$content = $parser->while_parser()->do_while_parser()->for_parser()->echo_parser()->remove_php_tags()->display();
*/
/*
$parser->setContent('[if_start condition(true) expr([print "hello"])][if_end]');
$content = $parser->if_parser()->echo_parser()->remove_php_tags()->display();
*/
$parser->setContent('<?php $a="hello,world";?>[if_start condition(true) expr([print "hello"])][if_end][else_start]expr([print $a])[else_end]');
$content = $parser->if_parser()->else_parser()->echo_parser()->remove_php_tags()->display();
file_put_contents('compile_tmp.php', $content);
echo $content;
