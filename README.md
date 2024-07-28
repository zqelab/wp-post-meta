# Your Package Name

A WordPress plugin to add meta fields with support for groups and repeatable fields.

## Installation

Use Composer to install:

```sh
composer require your-vendor-name/your-package-name
```

## Usage

Here's how you can use the `Wp_Post_Meta` class:

```php
use Zqe\Wp_Post_Meta;

add_action('init', function() {
    new Wp_Post_Meta('post', [
        [
            'id' => 'custom_text_field',
            'label' => __('Custom Text Field', 'textdomain'),
            'type' => 'text',
            'default' => '',
            'size' => '30',
            'required' => true,
            'placeholder' => __('Enter text here', 'textdomain'),
            'desc' => __('A description for this field.', 'textdomain'),
        ],
        [
            'id' => 'custom_group_field',
            'label' => __('Custom Group Field', 'textdomain'),
            'type' => 'group',
            'fields' => [
                [
                    'id' => 'subfield_1',
                    'label' => __('Subfield 1', 'textdomain'),
                    'type' => 'text',
                    'default' => '',
                    'size' => '30',
                    'required' => true,
                    'placeholder' => __('Enter text here', 'textdomain'),
                    'desc' => __('A description for this subfield.', 'textdomain'),
                ],
                [
                    'id' => 'subfield_2',
                    'label' => __('Subfield 2', 'textdomain'),
                    'type' => 'textarea',
                    'default' => '',
                    'size' => '30',
                    'required' => false,
                    'placeholder' => __('Enter text here', 'textdomain'),
                    'desc' => __('A description for this subfield.', 'textdomain'),
                ],
            ],
        ],
        [
            'id' => 'custom_repeatable_field',
            'label' => __('Custom Repeatable Field', 'textdomain'),
            'type' => 'group',
            'repeatable' => true,
            'fields' => [
                [
                    'id' => 'repeatable_subfield_1',
                    'label' => __('Repeatable Subfield 1', 'textdomain'),
                    'type' => 'text',
                    'default' => '',
                    'size' => '30',
                    'required' => true,
                    'placeholder' => __('Enter text here', 'textdomain'),
                    'desc' => __('A description for this subfield.', 'textdomain'),
                ],
                [
                    'id' => 'repeatable_subfield_2',
                    'label' => __('Repeatable Subfield 2', 'textdomain'),
                    'type' => 'color',
                    'default' => '',
                    'size' => '30',
                    'required' => false,
                    'placeholder' => __('Enter color here', 'textdomain'),
                    'desc' => __('A description for this subfield.', 'textdomain'),
                ],
            ],
        ],
    ]);
});
```
