<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  //searching 'TITLE' (varchar)
  global $title;
  if ($title!='') {
   $qry.=" AND TITLE LIKE '%".DBSafe($title)."%'";
   $out['TITLE']=$title;
  }
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['orvibodevices_qry'];
  } else {
   $session->data['orvibodevices_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_orvibodevices;
  if (!$sortby_orvibodevices) {
   $sortby_orvibodevices=$session->data['orvibodevices_sort'];
  } else {
   if ($session->data['orvibodevices_sort']==$sortby_orvibodevices) {
    if (Is_Integer(strpos($sortby_orvibodevices, ' DESC'))) {
     $sortby_orvibodevices=str_replace(' DESC', '', $sortby_orvibodevices);
    } else {
     $sortby_orvibodevices=$sortby_orvibodevices." DESC";
    }
   }
   $session->data['orvibodevices_sort']=$sortby_orvibodevices;
  }
  if (!$sortby_orvibodevices) $sortby_orvibodevices="TITLE";
  $out['SORTBY']=$sortby_orvibodevices;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM orvibodevices WHERE $qry ORDER BY ".$sortby_orvibodevices);
  if ($res[0]['ID']) {
   colorizeArray($res);
   $total=count($res);
   for($i=0;$i<$total;$i++) {
       $tm=strtotime($res[$i]['UPDATED']);
       if ((time()-$tm)<=60) {
           $res[$i]['IS_ONLINE']=1;
       }
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }
