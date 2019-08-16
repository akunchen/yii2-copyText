# yii2-copyText

## usage
- single use
```php
echo CopyText::widget([
    'text' => 'something to copy'
]);
```

- full config
```php
echo CopyText::widget([
    'text' => [
        'msg' => 'something to copy',
        'tag' => 'span',
        'options' => [
            'class' => 'js-copy-target'
        ]
    ],
    'button' => [
        'msg' => 'copy',
        'tag' => 'a',
        'options' => [
            'class' => 'btn btn-primary js-copy-button'
        ]
    ],
    'copiedText' => 'copied'
]);
```