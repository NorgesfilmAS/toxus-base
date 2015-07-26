<?php

return array(
    'buttons' => array(
      'add' => $this->button(array(
        'label' => Yii::t('button', 'btn-create'),
        'position' => 'pull-left',
        'url' => $this->createUrl('article/create'),
      )),
      'update' => $this->button(array(
        'label' => Yii::t('button', 'btn-edit'),
        'url' => '', 
        'id' => 'btn-edit'
      ))
    ), 
);