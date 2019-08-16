<?php
/**
 * Created by PhpStorm.
 * On: 2019-08-16 14:53.
 */

namespace beark\copy;

use yii\base\InvalidArgumentException;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

/**
 * copy text to your clipboard
 */
class CopyText extends Widget
{
    /**
     * [
     *     'text' => 'something to copy'
     * ]
     * or
     * [
     *     'text' => [
     *         'msg' => 'something to copy',
     *         'tag' => 'div',
     *         'options' => [ 'class' => 'btn btn-primary'],
     *     ]
     * ]
     * @var string|array $text 要复制文字
     * @see Html::tag
     */
    public $text;

    /**
     * the config the same as text
     * @see CopyText::$text
     * @var array $buttonText 复制按钮名字
     */
    public $button = [];

    /**
     * @var string $layout 布局
     */
    public $layout = "{text}\n{button}";

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerJs();

        $result = $this->layout;
        if (strstr($this->layout, '{text}')) {
            $result = str_replace('{text}', $this->renderText(), $result);
        }

        if (strstr($this->layout, '{button}')) {
            $result = str_replace('{button}', $this->renderButton(), $result);
        }

        return $result;
    }

    /**
     * render text
     *
     * @return string
     */
    private function renderText()
    {
        return $this->renderItem($this->text, [
            'tag' => 'span',
            'options' => [
                'class' => 'js-copy-target'
            ]
        ]);
    }

    /**
     * render button
     *
     * @return string
     */
    private function renderButton()
    {
        return $this->renderItem($this->button, [
            'tag' => 'a',
            'msg' => '复制',
            'options' => [
                'class' => 'js-copy-button',
                'style' => ['cursor' => 'pointer']
            ],
        ]);
    }

    /**
     * render item
     *
     * @param array|string $config
     * @param array|string $defaultConfig
     * @return string
     */
    private function renderItem($config, $defaultConfig)
    {
        \Yii::debug($config, '$config');
        \Yii::debug($defaultConfig, '$defaultConfig');
        if (!is_array($config)) {
            $config = [
                'msg' => $config
            ];
        }

        $config = ArrayHelper::merge($defaultConfig, $config);
        if (!isset($config['msg'])) {
            throw new InvalidArgumentException('config array must contains key call {msg}');
        }

        if (!isset($config['tag'])) {
            return $config['msg'];
        }

        return Html::tag($config['tag'], $config['msg'], ArrayHelper::getValue($config, 'options', null));
    }

    /**
     * register js function
     */
    private function registerJs()
    {
        $js = <<<js
(function() {
    function copyText(value) {
        var input = document.createElement('input');
        input.value = value;
        document.body.appendChild(input);
        input.select(); // 选择对象
        document.execCommand("Copy"); // 执行浏览器复制命令
        input.style.display = 'none';
        document.body.removeChild(input);
    }
    
    $('.js-copy-button').on('click', function() {
        console.log('asdasd')
        var target = $(this);
        if (target.copyTextTask) {
            clearTimeout(target.copyTextTask);
        }
        
        copyText(target.parent().find('.js-copy-target').text());
        var text = target.text();
        target.text('已复制!');
        target.copyTextTask = setTimeout(function() {
            target.text(text);
            target.copyTextTask = null;    
        }, 2000)
    });
})();
js;

        \Yii::$app->view->registerJs($js);
    }
}