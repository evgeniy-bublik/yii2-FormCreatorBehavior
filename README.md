## Yii2 form create behavior
This is behavior which generate view form for methods CREATE and UPDATE.
## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```sh
php composer.phar require  evgeniydev/yii2-form-creator-behavior
```

or add

```
"evgeniydev/yii2-form-creator-behavior": "*"
```

to the require section of your `composer.json` file.
## Basic usage
**Model file (app/models/SomeModel.php)**
```php
...
use evgeniydev\yii2\behaviors\FormCreatorBehavior
...
class SomeModel extends \yii\base\Model
{
public $field1;
public $field2;
public $field3;
...
public function behaviors()
{
    return [
        ...
        'formBehavior' => [
            'class' => FormCreatorBehavior::className(),
            'attributes' => [
                'field1' => [ // <input type="text">
                    'type' => FormCreatorBehavior::TEXT_INPUT_TYPE,
                    'inputOptions' => [
                        'class' => 'someClass',
                        // other html options
                    ],
                ],
                'field2', // by default generate <input type="text">
                'field3' => [ // generate <select>...</select>
                    'type' => FormCreatorBehavior::DROPDOWNLIST_TYPE,
                    'items' => ['option1', 'option2'],
                    'inputOptions' => [
                        'prompt' => 'Choose option',
                    ],
                ],
                'field4' => function($form, $model) { // callable function to return view field for this form
                    return $form->field($model)
                }
                // other fields
            ],
        ]
        ...
    ];
}
```
**Controller file (app/controllers/SomeController.php)**
```php
use app\models\SomeModel.php
...
public function actionCreate()
{
    $model = new SomeModel();
    if ($model->load(\Yii::$app->request->post()) && $model->save()) {
        //  model save
    }

    return $this->render('form', [
        'model' => $model,
    ]);
}

public function actionUpdate($id)
{
    $model = SomeModel::findOne($id);

    if (!$model) {
        // throw not found
    }
    if ($model->load(\Yii::$app->request->post()) && $model->save()) {
        //  model update
    }

    return $this->render('form', [
        'model' => $model,
    ]);
}
...
```
**View file (app/views/some/form.php)**
```
<?= $model->form; ?>
```
## Other field types
**Checkbox**
```php
'field1' => [
    'type' => FormCreatorBehavior::CHECKBOX_TYPE,
    'inputOptions' => [
        'class' => 'someClass',
        // other html options
    ],
],
```
**Checkbox list**
```php
'field1' => [
    'type' => FormCreatorBehavior::CHECKBOXLIST_TYPE,
    'items' => ['check1', 'check2'],
    'inputOptions' => [
        'class' => 'someClass',
        // other html options
    ],
],
```
**Widget**
```php
'field1' => [
    'type' => FormCreatorBehavior::WIDGET_TYPE,
    'widgetClass' => '' // widget class name
    'widgetOptions' => [...], // widget options
],
```
**List box**
```php
'field1' => [
    'type' => FormCreatorBehavior::LISTBOX_TYPE,
    'items' => ['check1', 'check2'],
    'inputOptions' => [
        'class' => 'someClass',
        // other html options
    ],
],
```
**Input with custom type**
```php
'field1' => [ // <input type="tel">
    'type' => FormCreatorBehavior::INPUT_TYPE,
    'inputType' => 'tel',
    'inputOptions' => [
        'class' => 'someClass',
        // other html options
    ],
],
```
## Full list constants types
* *__TEXT_INPUT_TYPE__* - text input
* *__TEXTAREA_TYPE__* - textarea
* *__CHECKBOX_TYPE__* - checkbox
* *__DROPDOWNLIST_TYPE__* - dropdownlist
* *__WIDGET_TYPE__* - widget
* *__INPUT_TYPE__* - input with custom type
* *__HIDDEN_INPUT_TYPE__* - hidden input
* *__FILE_INPUT_TYPE__* - file input
* *__PASSWORD_INPUT_TYPE__* - password input
* *__RADIO_TYPE__* - radio input
* *__RADIOLIST_TYPE__* - radio list
* *__CHECKBOXLIST_TYPE__* - checkboxe list
* *__LISTBOX_TYPE__* - list box
## Other configuration
**Input with callable function**
***function template: function($form, $model){ return ...; }***
```php
'field1' => function($form, $model) { // callable function
    return $form->field($model, 'field1');
},
```
**Label**
```php
'field1' => [
    'type' => FormCreatorBehavior::TEXT_INPUT_TYPE,
    'label' => 'Some label', // or false, if want not show label
    ...
],
```
**Hint (short form)**
```php
'field1' => [
    'type' => FormCreatorBehavior::TEXT_INPUT_TYPE,
    'hint' => 'Some hint',
    ...
],
```
or full form
```php
'field1' => [
    'type' => FormCreatorBehavior::TEXT_INPUT_TYPE,
    'hint' => [
        'content' => 'Some content', // hint content
        'options' => [...], // hint options
    ],
    ...
],
```
**Form configuration**
```php
'class' => FormCreatorBehavior::className(),
'attributes' => [...], // array attributes
'formOptions' => [
    'options' => [
        'enctype' => 'multipart/form-data'
    ],
    'id' => 'Some id',
    ... // other \yii\widgets\ActiveForm widget options
]
```
**Template view**
```php
'class' => FormCreatorBehavior::className(),
'attributes' => [...], // array attributes
'template' => '{items}{beginBlockButtons}{submitButton}{cancelButton}{endBlockButtons}', // template view
```
```
{items} - elements of form
{beginBlockButtons} - open tag for block with form buttons
{submitButton} - submit form button
{cancelButton} - cancel form button
{endBlockButtons} - end tag for block with buttons
```
**Submit button options**
```php
'class' => FormCreatorBehavior::className(),
'attributes' => [...], // array attributes
'submitButtonOptions' => [
    'createButtonOptions' => [ // create button options
        'title' => 'Create', // text create button
        'tag' => 'input', // tag create button (input|button)
        'htmlOptions' => [...], // create button html options
    ],
    'updateButtonOptions' => [
        'title' => 'Update', // text update button
        'tag' => 'input', // tag update button (input|button)
        'htmlOptions' => [...], // update button html options
    ],
],
```
**Cancel button options**
```php
'class' => FormCreatorBehavior::className(),
'attributes' => [...], // array attributes
'cancelButtonOptions' => [
    'show' => true, // true or false, show cancel button
    'title' => 'Cancel', // text cancel button
    'action' => ['index'], // url to go cancel operation, by default is action index
    'htmlOptions' => [...], // cancel button html options
],
```
**Wrapper block buttons form options**
```php
'class' => FormCreatorBehavior::className(),
'attributes' => [...], // array attributes
'wrapperBlockButtonsOptions' => [
    'tag' => 'div', // tag name wrapper buttons block or false
    'htmlOptions' => [...], // cancel button html options
],
```
**Tabs**
```php
'class' => FormCreatorBehavior::className(),
'tabOptions' => [ // tab options
    'widgetName' => '...', // tab widget name, default is \yii\bootstrap\Tabs
    'widgetOptions' => [
        'keyNameContentField' => '...', // key name tab content field, default 'content'
        // ... other some widget options
    ],
    'tabs' => [
        [ // tab 1
            'tabAttributes' => ['someField1', 'someField2', ...], // list attributes on this tab,
            'content' => '{items}', // template of content tab, {items} to be replaced by fields attribues on this tab, 'content' if
                       // keyNameContentField = 'content', if keyNameContentField not equal 'content', then key 'content' key must be other
            // .. other tab options for this widget tab, example \yii\bootstrap\Tabs: 'title' => 'Some title',
        ],
        [ // tab 2
            'tabAttributes' => ['someField3', 'someField4', ...], // list attributes on this tab,
            // .. other tab options for this widget tab, example \yii\bootstrap\Tabs: 'title' => 'Some title',
        ],
        // ... other tabs
    ],
],
```