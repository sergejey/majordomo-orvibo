<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='orvibodevices';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");

  global $ajax;
  if ($ajax) {
   global $op;
   if ($op=='learnir') {
    $this->setIRLearning($rec['ID']);
    echo "IR ";
   }
   if ($op=='sendir') {
    $this->sendIR($rec['ID'], $rec['VALUE_IR']);
    echo "Test ";
   }
   if ($op=='learnrf') {
    $this->setRFLearning($rec['ID']);
    echo "RF ";
   }

   if ($op=='switch') {
    if ($rec['STATUS']) {
     $this->setStatus($rec['ID'], 0);
    } else {
     $this->setStatus($rec['ID'], 1);
    }
    $new_status=current(SQLSelectOne("SELECT STATUS FROM orvibodevices WHERE ID='".$rec['ID']."'"));
    echo "$new_status ";
   }


   if ($op=='sendir') {
    $this->sendRF($rec['ID'], $rec['VALUE_RF']);
    echo "Test ";
   }
   echo "OK";
   exit;
  }

  if ($this->mode=='update') {
   $ok=1;
  //updating 'TITLE' (varchar, required)
   global $title;
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'LINKED_OBJECT' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object.'';
  //updating 'LINKED_PROPERTY' (varchar)
   global $linked_property;
   $rec['LINKED_PROPERTY']=$linked_property.'';
  //updating 'LINKED_METHOD' (varchar)
   global $linked_method;
   $rec['LINKED_METHOD']=$linked_method.'';

   global $linked_object_rf;
   $rec['LINKED_OBJECT_RF']=$linked_object_rf.'';
  //updating 'LINKED_PROPERTY' (varchar)
   global $linked_property_rf;
   $rec['LINKED_PROPERTY_RF']=$linked_property_rf.'';
  //updating 'LINKED_METHOD' (varchar)
   global $linked_method_rf;
   $rec['LINKED_METHOD_RF']=$linked_method_rf.'';

   global $linked_method_button;
   $rec['LINKED_METHOD_BUTTON']=$linked_method_button.'';

   $rec['BUTTON_CODE']=gr('button_code').'';



  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);

  if ($rec['VALUE_IR']) {
   $out['VALUE_IR']=chunk_split($rec['VALUE_IR'],8," ");
  }

 if ($rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY']) {
  addLinkedProperty($rec['LINKED_OBJECT'], $rec['LINKED_PROPERTY'], $this->name);
 }
