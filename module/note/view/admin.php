<?php $oPluginHtml=new plugin_html?>

<hr/>
<form action="" method="POST"> 

Ajouter une tache <input type="text" name="task"/> member <?php echo $oPluginHtml->getSelect('member_id',$this->tMember)?>


</form>



