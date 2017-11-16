<?php

namespace evgeniydev\formcreator;

use yii\helpers\Html;
use yii\base\Behavior;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\MethodNotAllowedHttpException;

class FormCreatorBehavior extends Behavior
{
    /**
     * Show simple form
     */
    const SIMPLE_FORM = 'simple';
    /**
     * Show tab form
     */
    const TAB_FORM    = 'tab';
    /**
     * Text input field
     */
    const TEXT_INPUT_TYPE = 'textInput';
    /**
     * Textarea input field
     */
    const TEXTAREA_TYPE = 'textarea';
    /**
     * Checkbox input field
     */
    const CHECKBOX_TYPE = 'checkbox';
    /**
     * Checkbox list input field
     */
    const CHECKBOXLIST_TYPE = 'checkboxList';
    /**
     * Dropdownlist input field
     */
    const DROPDOWNLIST_TYPE = 'dropDownList';
    /**
     * Widget input field
     */
    const WIDGET_TYPE = 'widget';
    /**
     * Simple input field
     */
    const INPUT_TYPE = 'input';
    /**
     * Hidden input field
     */
    const HIDDEN_INPUT_TYPE = 'hiddenInput';
    /**
     * File input field
     */
    const FILE_INPUT_TYPE = 'fileInput';
    /**
     * Password input field
     */
    const PASSWORD_INPUT_TYPE = 'passwordInput';
    /**
     * Listbox input field
     */
    const LISTBOX_TYPE = 'listBox';
    /**
     * Radio input field
     */
    const RADIO_TYPE = 'radio';
    /**
     * Radio list input field
     */
    const RADIOLIST_TYPE = 'radioList';
    /**
     * @var string $type Form type
     */
    public $type = self::SIMPLE_FORM;
    /**
     * @var mixed[] $formOptions Array options for widget ActiveForm
     */
    public $formOptions = [
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ];
    /**
     * @var mixed[] $submitButtonOptions Array options for submit buttons (update|create).
     *
     * ...
     * 'submitButtonOptions' => [
     *    'createButtonOptions' => [ // Options for button when model create
     *        'title'       => 'Title', // Title button
     *        'tag'         => 'input', // Submit button type (input|button)
     *        'htmlOptions' => [...], // List html options for submit button
     *    ],
     *    'updateButtonOptions' => [ // Options for button when model update. Duplicate key 'createButtonOptions'
     *        ...
     *    ],
     * ],
     */
    public $submitButtonOptions;
    /**
     * @var mixed[] $cancelButtonOptions Array options for cancel link
     *
     * ...
     * 'cancelButtonOptions' => [
     *    'show'        => true, // Boolean show cancel button or not
     *    'title'       => 'Cancel', // Text cancel button
     *    'action'      => ['index'], // Url to go cancel operation, by default is action index
     *    'htmlOptions' => [...], // Html options for cancel button
     * ],
     */
    public $cancelButtonOptions;
    /**
     * @var mixed[] $wrapperBlockButtonsOptions Array options for buttons form wrapper.
     *
     * ...
     * 'wrapperBlockButtonsOptions' => [
     *    'tag' => 'div', // Name tag for wrapper buttons string|false
     *    'htmlOptions' => [...], // Html options for wrapper tag
     * ],
     */
    public $wrapperBlockButtonsOptions;
    /**
     * @var string template for view form
     */
    public $template = '{items}{beginBlockButtons}{submitButton}{cancelButton}{endBlockButtons}';
    /**
     * @var mixed[] $attributes Array attributes model which be show in view
     *
     * ...
     * 'attributes' => [
     *      'someAttribute1', // generate text field for this attribute
     *      'someAttribute2' => [
     *          'type' => FormCreatorBehavior::WIDGET_TYPE // generate widget field for this attribute
     *          'widgetClass' => '...', // Widget class
     *          'widgetOptions' => [], // Widget options
     *      ],
     *      'someAttribute3' => [
     *          'type' => FormCreatorBehavior::DROPDOWNLIST_TYPE // generate dropDownList field for this attribute
     *          'items' => ['option1', 'option2'], // dropdown items
     *          'attributeOptions' => [ // ActiveField attribute options
     *              'labelOptions' => [...],
     *              'template' => '...',
     *          ],
     *          'inputOptions' => [ // dropDown options
     *              'prompt' => 'Choose option',
     *          ],
     *          'hint' => '...', // hint for this attribute
     *      ]
     * ],
     *
     */
    public $attributes;
    /**
     * @var string $form Attribute which contains html form for this model
     */
    protected $form;
    /**
     * @var mixed[] $allowedFormInputTypes Array list allowed types input
     */
    private $allowedFormInputTypes = [
        self::TEXT_INPUT_TYPE     => self::TEXT_INPUT_TYPE,
        self::TEXTAREA_TYPE       => self::TEXTAREA_TYPE,
        self::CHECKBOX_TYPE       => self::CHECKBOX_TYPE,
        self::DROPDOWNLIST_TYPE   => self::DROPDOWNLIST_TYPE,
        self::WIDGET_TYPE         => self::WIDGET_TYPE,
        self::INPUT_TYPE          => self::INPUT_TYPE,
        self::HIDDEN_INPUT_TYPE   => self::HIDDEN_INPUT_TYPE,
        self::FILE_INPUT_TYPE     => self::FILE_INPUT_TYPE,
        self::PASSWORD_INPUT_TYPE => self::PASSWORD_INPUT_TYPE,
        self::RADIO_TYPE          => self::RADIO_TYPE,
        self::RADIOLIST_TYPE      => self::RADIOLIST_TYPE,
        self::CHECKBOXLIST_TYPE   => self::CHECKBOXLIST_TYPE,
        self::LISTBOX_TYPE        => self::LISTBOX_TYPE,
    ];

    /**
     * Generate form fields
     *
     * @throws MethodNotAllowedHttpException If type form equal tab
     * @return string
     */
    public function getForm()
    {
        switch ($this->type) {
            case static::SIMPLE_FORM:
                return $this->getSimpleForm();
            case static::TAB_FORM:
                throw MethodNotAllowedHttpException('This method not allowed');
            default:
                return $this->getSimpleForm();
        }
    }

    /**
     * Generate form fields for simple form
     *
     * @return string Return html form for attributes model
     */
    protected function getSimpleForm()
    {
        if (!empty($this->form)) {
            return $this->form;
        }

        $model = $this->owner;
        $items = '';

        ob_start();

        $form = ActiveForm::begin($this->formOptions);

        foreach ($this->attributes as $attributeName => $options) {
            $field = null;

            if (!is_array($options)) {
                $attributeName  = $options;
                $options        = [];
            }

            $attributeOptions = ArrayHelper::getValue($options, 'attributeOptions', []);
            $type             = ArrayHelper::getValue($options, 'type', static::TEXT_INPUT_TYPE);
            $label            = ArrayHelper::getValue($options, 'label', null);
            $hint             = ArrayHelper::getValue($options, 'hint', null);

            if (!ArrayHelper::keyExists($type, $this->allowedFormInputTypes)) {
                $type = static::TEXT_INPUT_TYPE;
            }

            $field = $form->field($model, $attributeName, $attributeOptions);

            if ($label !== false && !is_null($label)) {
                $field->label($label);
            }

            if (!is_null($hint)) {

                if (is_array($hint)) {
                    $content = ArrayHelper::getValue($hint, 'content', null);
                    $options = ArrayHelper::getValue($hint, 'options', []);
                } else {
                    $content = $hint;
                    $options = [];

                }

                $field->hint($content, $options);
            }

            switch ($type) {
                case static::WIDGET_TYPE:
                    $items .= $this->generateWidgetField($field, $options);
                    break;
                case static::DROPDOWNLIST_TYPE:
                case static::RADIOLIST_TYPE:
                case static::CHECKBOXLIST_TYPE:
                case static::LISTBOX_TYPE:
                    $items .= $this->generateInputWithItems($field, $type, $options);
                    break;
                case static::RADIO_TYPE:
                    $items .= $this->generateRadionInput($field, $options);
                    break;
                case static::INPUT_TYPE:
                    $items .= $this->generateInput($field, $options);
                    break;
                default:
                    $inputOptions = ArrayHelper::getValue($options, 'inputOptions', []);

                    call_user_func_array([$field, $type], [$inputOptions]);

                    $items .= $field;
            }
        }

        echo strtr($this->template, [
            '{items}'             => $items,
            '{beginBlockButtons}' => $this->getBeginBlockButtons(),
            '{endBlockButtons}'   => $this->getEndBlockButtons(),
            '{submitButton}'      => $this->getSubmitButton(),
            '{cancelButton}'      => $this->getCancelButton(),
        ]);

        ActiveForm::end();

        $result = ob_get_contents();

        ob_get_clean();

        return $this->form = $result;
    }

    /**
     * Generate radio input
     *
     * @param \yii\widgets\ActiveField $field ActiveField object
     * @param mixed[] $options Array options for widget
     *
     * @return \yii\widgets\ActiveField Return ActiveField object
     */
    protected function generateRadionInput($field, $options)
    {
        $inputOptions     = ArrayHelper::getValue($options, 'inputOptions', []);
        $enclosedByLabel  = ArrayHelper::getValue($options, 'enclosedByLabel', true);

        call_user_func_array([$field, static::RADIO_TYPE], [$inputOptions, $enclosedByLabel]);

        return $field;
    }

    /**
     * Generate widget input
     *
     * @param \yii\widgets\ActiveField $field ActiveField object
     * @param mixed[] $options Array options for widget
     *
     * @return \yii\widgets\ActiveField Return ActiveField object
     */
    protected function generateWidgetField($field, $options)
    {
        $widgetClass = ArrayHelper::getValue($options, 'widgetClass', null);

        if (is_null($widgetClass)) {
            throw new InvalidConfigException('For widget input type must be set option "widgetClass"');
        }

        $widgetOptions = ArrayHelper::getValue($options, 'widgetOptions', []);

        call_user_func_array([$field, static::WIDGET_TYPE], [$widgetClass, $widgetOptions]);

        return $field;
    }

    /**
     * Generate input with items
     *
     * @param \yii\widgets\ActiveField $field ActiveField object
     * @param string $type Type input (checkboxList|radioList|listBox|dropdownlist)
     * @param mixed[] $options Array options for widget
     *
     * @return \yii\widgets\ActiveField Return ActiveField object
     */
    protected function generateInputWithItems($field, $type, $options)
    {
        $inputOptions = ArrayHelper::getValue($options, 'inputOptions', []);
        $items        = ArrayHelper::getValue($options, 'items', []);

        call_user_func_array([$field, $type], [$items, $inputOptions]);

        return $field;
    }

    /**
     * Generate input type
     *
     * @param \yii\widgets\ActiveField $field ActiveField object
     * @param mixed[] $options Array options for widget
     *
     * @return \yii\widgets\ActiveField Return ActiveField object
     */
    protected function generateInput($field, $options)
    {
        $inputOptions = ArrayHelper::getValue($options, 'inputOptions', []);
        $type         = ArrayHelper::getValue($options, 'inputType', 'text');

        call_user_func_array([$field, static::INPUT_TYPE], [$type, $inputOptions]);

        return $field;
    }

    /**
     * Open wrapper buttons tag
     *
     * @return string Open tag for wrapper buttons form
     */
    protected function getBeginBlockButtons()
    {
        $wrapperBlockButtonsOptions = $this->wrapperBlockButtonsOptions;

        $tag = ArrayHelper::getValue($wrapperBlockButtonsOptions, 'tag', 'div');

        if ($tag === false) {
            return null;
        }

        $htmlOptions = ArrayHelper::getValue($wrapperBlockButtonsOptions, 'htmlOptions', []);

        if (!isset($htmlOptions[ 'class' ])) {
            Html::addCssClass($htmlOptions, 'form-group');
        }

        return Html::beginTag($tag, $htmlOptions);
    }

    /**
     * Close wrapper buttons tag
     *
     * @return string Close tag for wrapper buttons form
     */
    protected function getEndBlockButtons()
    {
        $wrapperBlockButtonsOptions = $this->wrapperBlockButtonsOptions;

        $tag = ArrayHelper::getValue($wrapperBlockButtonsOptions, 'tag', 'div');

        if ($tag === false) {
            return null;
        }

        return Html::endTag($tag);
    }

    /**
     * Generate submit button form
     *
     * @return string Return html tag sumbit button
     */
    protected function getSubmitButton()
    {
        $submitButtonOptions  = $this->submitButtonOptions;
        $model                = $this->owner;

        $defaultCreateButtonOptions = [
            'title'       => 'Create',
            'tag'         => 'input',
            'htmlOptions' => [
                'class' => 'btn btn-success',
            ],
        ];

        $defaultUpdateButtonOptions = [
            'title'       => 'Update',
            'tag'         => 'input',
            'htmlOptions' => [
                'class' => 'btn btn-primary',
            ]
        ];

        // create button options
        $createButtonOptions = ArrayHelper::getValue($submitButtonOptions, 'createButtonOptions', []);
        $createButtonOptions = ArrayHelper::merge($defaultCreateButtonOptions, $createButtonOptions);

        // update button options
        $updateButtonOptions = ArrayHelper::getValue($submitButtonOptions, 'updateButtonOptions', []);
        $updateButtonOptions = ArrayHelper::merge($defaultUpdateButtonOptions, $updateButtonOptions);

        $currentButtonOptions = ($model->isNewRecord) ? $createButtonOptions : $updateButtonOptions;

        $title        = ArrayHelper::getValue($currentButtonOptions, 'title');
        $tag          = ArrayHelper::getValue($currentButtonOptions, 'tag');
        $htmlOptions  = ArrayHelper::getValue($currentButtonOptions, 'htmlOptions');

        if ($tag === 'input') {
            return Html::submitInput($title, $htmlOptions);
        }

        return Html::submitButton($title, $htmlOptions);
    }

    /**
     * Generate cancel button form
     *
     * @return string Return html tag cancel button
     */
    protected function getCancelButton()
    {
        $cancelButtonOptions = $this->cancelButtonOptions;

        if (!is_array($cancelButtonOptions)) {
            $cancelButtonOptions = [];
        }

        $defaultCancelButtonOptions = [
            'show'        => true,
            'title'       => 'Cancel',
            'action'      => ['index'],
            'htmlOptions' => [
                'class' => 'btn btn-default',
            ],
        ];

        if (ArrayHelper::getValue($cancelButtonOptions, 'show') === false) {
            return null;
        }

        $cancelButtonOptions = ArrayHelper::merge($defaultCancelButtonOptions, $cancelButtonOptions);

        $title        = ArrayHelper::getValue($cancelButtonOptions, 'title');
        $action       = ArrayHelper::getValue($cancelButtonOptions, 'action');
        $htmlOptions  = ArrayHelper::getValue($cancelButtonOptions, 'htmlOptions');

        return Html::a($title, $action, $htmlOptions);
    }
}