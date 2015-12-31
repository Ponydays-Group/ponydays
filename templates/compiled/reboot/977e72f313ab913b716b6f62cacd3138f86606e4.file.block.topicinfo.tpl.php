<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker/plugins/topicinfo/templates/skin/default/blocks/block.topicinfo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21186692075684d6cb1e19d1-11233173%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '977e72f313ab913b716b6f62cacd3138f86606e4' => 
    array (
      0 => '/var/www/bunker/plugins/topicinfo/templates/skin/default/blocks/block.topicinfo.tpl',
      1 => 1451256534,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21186692075684d6cb1e19d1-11233173',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'aLang' => 0,
    'oTopicUser' => 0,
    'oConfig' => 0,
    'LS' => 0,
    'aUsersTopic' => 0,
    'oCurTopic' => 0,
    'oMainPhoto' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb3083c6_73528735',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb3083c6_73528735')) {function content_5684d6cb3083c6_73528735($_smarty_tpl) {?>
    <!-- Topicinfo plugin -->
    <?php if ($_smarty_tpl->tpl_vars['oTopic']->value){?>
      <div class="block Topicinfo">
        <header class="block-header sep">
          <h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['topicinfo']['Block_Title'];?>
</h3>
        </header>
	<div class="topicinfo_content">
        <?php $_smarty_tpl->tpl_vars["oTopicUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getUser(), null, 0);?>
        <div class="AvatarHolder">
          <a href="<?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getUserWebPath();?>
" class="avatar"><img src="<?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getProfileAvatarPath(64);?>
" alt="avatar" itemprop="photo" /></a>
          <div class="Status <?php if ($_smarty_tpl->tpl_vars['oTopicUser']->value->isOnline()){?>online<?php }else{ ?>offline<?php }?>" title="<?php if ($_smarty_tpl->tpl_vars['oTopicUser']->value->isOnline()){?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_status_online'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_status_offline'];?>
<?php }?>"></div>
        </div>
        <div class="MoreInfo">
          <h2 class="header-table">
            <a href="<?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getUserWebPath();?>
" class="user"><?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getLogin();?>
</a>
          </h2>
          <div class="OneDescription">
            <p title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_rating'];?>
">
              <?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_rating'];?>
: <b class="r"><?php if ($_smarty_tpl->tpl_vars['oTopicUser']->value->getRating()>0){?>+<?php }?><?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getRating();?>
</b>
            </p>
            <p title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_skill'];?>
">
              <?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_skill'];?>
: <b class="s"><?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getSkill();?>
</b>
            </p>
          </div>
          

          
        </div>
        
        <div class="TopicList">
          <?php $_smarty_tpl->tpl_vars["aUsersTopic"] = new Smarty_variable($_smarty_tpl->tpl_vars['LS']->value->Topic_GetTopicsPersonalByUser($_smarty_tpl->tpl_vars['oTopicUser']->value->getId(),1,1,$_smarty_tpl->tpl_vars['oConfig']->value->GetValue("plugin.topicinfo.Topics_Count")), null, 0);?>
          <?php if ($_smarty_tpl->tpl_vars['aUsersTopic']->value){?>
            <h2 class="header-table"><a href="<?php echo $_smarty_tpl->tpl_vars['oTopicUser']->value->getUserWebPath();?>
created"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['topicinfo']['User_Topics'];?>
</a></h2>
            <ul>
              <?php  $_smarty_tpl->tpl_vars['oCurTopic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oCurTopic']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUsersTopic']->value['collection']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oCurTopic']->key => $_smarty_tpl->tpl_vars['oCurTopic']->value){
$_smarty_tpl->tpl_vars['oCurTopic']->_loop = true;
?>
                <?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getId()!=$_smarty_tpl->tpl_vars['oCurTopic']->value->getId()){?>
                  <li>
                    <?php if ($_smarty_tpl->tpl_vars['oCurTopic']->value->getType()=='photoset'){?>
                      <?php $_smarty_tpl->tpl_vars['oMainPhoto'] = new Smarty_variable($_smarty_tpl->tpl_vars['oCurTopic']->value->getPhotosetMainPhoto(), null, 0);?>
                      <?php if ($_smarty_tpl->tpl_vars['oMainPhoto']->value){?>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['oMainPhoto']->value->getWebPath(500);?>
" alt="image" />
                      <?php }?>
                    <?php }?>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['oCurTopic']->value->getUrl();?>
"><?php echo $_smarty_tpl->tpl_vars['oCurTopic']->value->getTitle();?>
</a>
                  </li>
                <?php }?>
              <?php } ?>
            </ul>
          <?php }?>
        </div>
      </div></div>
    <?php }?>
    <!-- /Topicinfo plugin -->
<?php }} ?>