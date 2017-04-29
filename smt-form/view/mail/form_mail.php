<?php 
$form_datas=(object)$form_datas;
?>
<?php foreach($form_datas as $val):?>
      <p><?php echo $val['name'];?> : <?php echo isset($val['type'])?"<a href='".$val['url']."'>".$val['value']."</a>":$val['value'];?></p>
<?php endforeach;?>

