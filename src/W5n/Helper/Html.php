<?php
namespace W5n\Helper;

class Html
{
    
    static public function tag(
        $tagName, array $attributes = array(), $content = null
    ) {
        $isEmpty = $content === false;
        
        if ($isEmpty)
            return sprintf('<%s%s />', $tagName, self::parseAttrs($attributes));
        
        return self::tagOpen($tagName, $attributes) 
            . $content 
            . self::tagClose($tagName);
    }
    
    static public function tagOpen($tagName, array $attributes = array())
    {
        return sprintf('<%s%s>', $tagName, self::parseAttrs($attributes));
    }
    
    static public function tagClose($tagName)
    {
        return sprintf('</%s>', $tagName);
    }
    
    static public function form(
        $action, $method = 'post', $content = null, array $attrs = array()
    ) {
        $attrs['action'] = $action;
        $attrs['method'] = $method;
        
        return self::tag('form', $attrs, $content);
    }
    
    static public function input(
        $name, $value = null, array $attrs = array(), $type = 'text'
    ) {
        if (!empty($name) && !isset($attrs['name']))
            $attrs['name'] = $name;
        
        if (!empty($type) && !isset($attrs['type']))
            $attrs['type'] = $type;

        if (!empty($value) && !isset($attrs['value']))
            $attrs['value'] = $value;
        
        return self::tag('input', $attrs, false);
    }
    
    static public function inputText(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'text');
    }
    
    static public function inputRadio(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'radio');
    }
    
    static public function inputCheckbox(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'checkbox');
    }
    
    static public function inputHidden(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'hidden');
    }
    
    static public function inputButton(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'button');
    }
    
    static public function inputSubmit(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'submit');
    }
    
    static public function inputImage($src, array $attrs = array()) 
    {
        if (empty($attrs['src']))
            $attrs['src'] = $src;
        
        return self::input(null, null, $attrs, 'image');
    }
    
    static public function inputFile($name, array $attrs = array()) 
    {
        return self::input($name, null, $attrs, 'file');
    }
    
    static public function inputPassword(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'password');
    }
    
    static public function inputReset(
        $name, $value = null, array $attrs = array()
    ) {
        return self::input($name, $value, $attrs, 'reset');
    }
    
    static public function image($src, $alt = null, array $attrs = array())
    {
        if (!empty($src))
            $attrs['src'] = $src;
        
        if (!empty($alt))
            $attrs['alt'] = $alt;
        
        return self::tag('img', $attrs, false);
    }
    
    static public function link($href, $text, array $attrs = array())
    {
        if (!empty($href))
            $attrs['href'] = $href;

        return self::tag('a', $attrs, $text);
    }
    
    static public function label($label, $for, array $attrs = array())
    {
        if (!empty($for))
            $attrs['for'] = $for;

        return self::tag('label', $attrs, $label);
    }
    
    static private function parseAttrs(array $attributes)
    {
        if (empty($attributes))
            return '';
        
        $attrStr = '';
        ksort($attributes);
        
        foreach ($attributes as $attr => $value)
        {
            if (empty($value))
                continue;
            
            $attrStr .= sprintf(
                ' %s="%s"', $attr, htmlspecialchars($value, ENT_QUOTES)
            );
        }
        
        return $attrStr;
    }
    
}

